<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Consultation;
use App\Models\ConsultationWindow;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NationwideConsultationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    /**
     * 1. Kiểm tra danh mục 34 đơn vị hành chính cấp tỉnh
     */
    public function test_provinces_config_contains_exactly_34_units(): void
    {
        $provinces = config('provinces');

        $this->assertIsArray($provinces);
        $this->assertCount(34, $provinces, 'Phải có đúng 34 đơn vị hành chính cấp tỉnh theo Nghị quyết 202/2025/QH15');
        $this->assertEquals('Thành phố Hà Nội', $provinces['01']);
        $this->assertEquals('Thành phố Hồ Chí Minh', $provinces['79']);
        $this->assertEquals('Tỉnh Cà Mau', $provinces['96']);
    }

    /**
     * 2. SettingController lưu mảng JSON tỉnh thành chuẩn xác, không bị lỗi ép chuỗi "Array"
     */
    public function test_setting_stores_provinces_as_json_array_without_string_corruption(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->post(route('admin.settings.update'), [
            'has_home_survey_config' => '1',
            'home_survey_province_codes' => ['01', '79'],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $stored = Setting::get('home_survey_province_codes');
        $this->assertNotEquals('Array', $stored, 'Giá trị lưu không được là chuỗi "Array"');

        $decoded = json_decode($stored, true);
        $this->assertIsArray($decoded);
        $this->assertContains('01', $decoded);
        $this->assertContains('79', $decoded);
        $this->assertNotContains('96', $decoded);
    }

    /**
     * 3. Luồng đặt lịch tại nhà: Tự động phân loại, loại bỏ tham số từ xa và bảo hiểm do client gửi
     */
    public function test_storefront_home_survey_booking_sanitizes_remote_inputs(): void
    {
        Setting::set('home_survey_province_codes', json_encode(['01', '79']), 'booking');

        $response = $this->post(route('consultation.store'), [
            'customer_name' => 'Nguyễn Văn Tại Nhà',
            'customer_phone' => '0901234567',
            'province_code' => '79', // TP.HCM (Hỗ trợ tại nhà)
            'address' => '123 Lê Lợi, Phường Bến Nghé, Quận 1',
            'preferred_date' => now()->addDays(1)->toDateString(),
            'preferred_time' => 'Sáng (08:30 - 11:30)',
            // Thử gửi tham số từ xa và bảo hiểm gian lận
            'service_mode' => 'remote_consultation',
            'contact_method' => 'zalo_video',
            'sample_box_requested' => '1',
            'fit_guarantee_eligible' => '1',
        ]);

        $response->assertSessionHasNoErrors();

        $consultation = Consultation::latest('id')->first();
        $this->assertNotNull($consultation);
        $this->assertEquals('Nguyễn Văn Tại Nhà', $consultation->customer_name);
        $this->assertEquals(Consultation::SERVICE_MODE_HOME_SURVEY, $consultation->service_mode);
        $this->assertEquals('Thành phố Hồ Chí Minh', $consultation->province_name);
        $this->assertNull($consultation->contact_method, 'Home survey phải triệt tiêu contact_method');
        $this->assertFalse($consultation->sample_box_requested, 'Home survey không nhận sample_box_requested');
        $this->assertEquals(Consultation::SAMPLE_BOX_NONE, $consultation->sample_box_status);
        $this->assertFalse($consultation->fit_guarantee_eligible, 'Client không được tự kích hoạt bảo hiểm');
    }

    /**
     * 4. Luồng đặt lịch từ xa: Bắt buộc thông tin liên hệ và phương án lắp đặt
     */
    public function test_storefront_remote_consultation_booking_requires_remote_fields(): void
    {
        Setting::set('home_survey_province_codes', json_encode(['01', '79']), 'booking');

        // Gửi thiếu contact_method và installation_method cho tỉnh ngoài khu vực (Cà Mau: 96)
        $invalidResponse = $this->post(route('consultation.store'), [
            'customer_name' => 'Trần Văn Cà Mau',
            'customer_phone' => '0987654321',
            'province_code' => '96',
            'address' => 'Ấp 3, Huyện Năm Căn',
            'preferred_date' => now()->addDays(2)->toDateString(),
        ]);

        $invalidResponse->assertSessionHasErrors(['contact_method', 'installation_method'], null, 'consultation');

        // Gửi đầy đủ thông tin hợp lệ
        $validResponse = $this->post(route('consultation.store'), [
            'customer_name' => 'Trần Văn Cà Mau',
            'customer_phone' => '0987654321',
            'province_code' => '96',
            'address' => 'Ấp 3, Huyện Năm Căn',
            'preferred_date' => now()->addDays(2)->toDateString(),
            'preferred_time' => 'Chiều (14:00 - 17:30)',
            'contact_method' => 'zalo_video',
            'sample_box_requested' => '1',
            'installation_method' => Consultation::INSTALL_SELF,
        ]);

        $validResponse->assertSessionHasNoErrors();

        $consultation = Consultation::latest('id')->first();
        $this->assertEquals(Consultation::SERVICE_MODE_REMOTE, $consultation->service_mode);
        $this->assertEquals('Tỉnh Cà Mau', $consultation->province_name);
        $this->assertEquals('zalo_video', $consultation->contact_method);
        $this->assertTrue($consultation->sample_box_requested);
        $this->assertEquals(Consultation::SAMPLE_BOX_PENDING, $consultation->sample_box_status);
        $this->assertEquals(Consultation::MEASUREMENT_PENDING, $consultation->measurement_status);
        $this->assertFalse($consultation->fit_guarantee_eligible);
    }

    /**
     * 5. Thông báo thành công chính xác theo lựa chọn kết nối và hộp mẫu
     */
    public function test_success_message_adapts_to_contact_method_and_sample_box_choice(): void
    {
        Setting::set('home_survey_province_codes', json_encode(['79']), 'booking');

        // Trường hợp 1: Chọn Google Meet và không nhận hộp mẫu
        $resp1 = $this->post(route('consultation.store'), [
            'customer_name' => 'Lê Khách Xa 1',
            'customer_phone' => '0911223344',
            'province_code' => '48', // Đà Nẵng
            'address' => '12 Nguyễn Văn Linh',
            'preferred_date' => now()->addDays(2)->toDateString(),
            'contact_method' => 'google_meet',
            'sample_box_requested' => '0',
            'installation_method' => Consultation::INSTALL_CUSTOMER_ARRANGED,
        ]);
        $resp1->assertSessionHas('success');
        $msg1 = session('success');
        $this->assertStringContainsString('Google Meet', $msg1);
        $this->assertStringNotContainsString('hộp mẫu vải', $msg1);

        // Trường hợp 2: Chọn Zalo Video và có nhận hộp mẫu
        $resp2 = $this->post(route('consultation.store'), [
            'customer_name' => 'Lê Khách Xa 2',
            'customer_phone' => '0911223355',
            'province_code' => '48',
            'address' => '34 Nguyễn Văn Linh',
            'preferred_date' => now()->addDays(2)->toDateString(),
            'contact_method' => 'zalo_video',
            'sample_box_requested' => '1',
            'installation_method' => Consultation::INSTALL_SELF,
        ]);
        $resp2->assertSessionHas('success');
        $msg2 = session('success');
        $this->assertStringContainsString('Zalo Video Call 1-1', $msg2);
        $this->assertStringContainsString('hộp mẫu vải dệt thực tế', $msg2);
    }

    /**
     * 6. Backend chặn không cho phép cập nhật hộp mẫu hoặc số đo đối với lịch khảo sát tại nhà
     */
    public function test_admin_cannot_update_sample_box_or_measurement_for_home_survey(): void
    {
        $this->actingAs($this->adminUser);

        $homeCons = Consultation::create([
            'customer_name' => 'Khách Hà Nội',
            'customer_phone' => '0909999888',
            'address' => '1 Phố Huế',
            'province_code' => '01',
            'province_name' => 'Thành phố Hà Nội',
            'service_mode' => Consultation::SERVICE_MODE_HOME_SURVEY,
            'preferred_date' => now()->addDays(1)->toDateString(),
            'status' => 'pending',
            'sample_box_requested' => false,
            'sample_box_status' => Consultation::SAMPLE_BOX_NONE,
        ]);

        // Thử cập nhật hộp mẫu cho lịch tại nhà -> phải bị chặn 422
        $respBox = $this->put(route('admin.consultations.update-sample-box', $homeCons->id), [
            'sample_box_status' => Consultation::SAMPLE_BOX_PENDING,
        ]);
        $respBox->assertStatus(422);

        // Thử cập nhật đo đạc từ xa cho lịch tại nhà -> phải bị chặn 422
        $respMeas = $this->put(route('admin.consultations.update-measurement', $homeCons->id), [
            'measurement_status' => Consultation::MEASUREMENT_CONFIRMED,
        ]);
        $respMeas->assertStatus(422);
    }

    /**
     * 7. Hộp mẫu: Bắt buộc mã vận đơn khi chuyển trạng thái "shipped" và khóa khi "delivered"
     */
    public function test_sample_box_requires_tracking_code_when_shipped_and_locks_when_delivered(): void
    {
        $this->actingAs($this->adminUser);

        $remoteCons = Consultation::create([
            'customer_name' => 'Khách Cà Mau Hộp Mẫu',
            'customer_phone' => '0933445566',
            'address' => 'Thị trấn Cái Nước',
            'province_code' => '96',
            'province_name' => 'Tỉnh Cà Mau',
            'service_mode' => Consultation::SERVICE_MODE_REMOTE,
            'contact_method' => 'zalo_video',
            'sample_box_requested' => true,
            'sample_box_status' => Consultation::SAMPLE_BOX_PENDING,
            'preferred_date' => now()->addDays(2)->toDateString(),
            'status' => 'pending',
        ]);

        // Gửi thiếu shipping_tracking_code khi chuyển sang shipped -> Phải lỗi validation
        $failResponse = $this->put(route('admin.consultations.update-sample-box', $remoteCons->id), [
            'sample_box_status' => Consultation::SAMPLE_BOX_SHIPPED,
            'shipping_tracking_code' => '',
        ]);
        $failResponse->assertSessionHasErrors(['shipping_tracking_code']);

        // Gửi kèm shipping_tracking_code hợp lệ -> Thành công
        $successResponse = $this->put(route('admin.consultations.update-sample-box', $remoteCons->id), [
            'sample_box_status' => Consultation::SAMPLE_BOX_SHIPPED,
            'shipping_tracking_code' => 'VNPOST-888999',
        ]);
        $successResponse->assertSessionHasNoErrors();
        $remoteCons->refresh();
        $this->assertEquals(Consultation::SAMPLE_BOX_SHIPPED, $remoteCons->sample_box_status);
        $this->assertEquals('VNPOST-888999', $remoteCons->shipping_tracking_code);

        // Chuyển tiếp sang delivered -> Thành công
        $this->put(route('admin.consultations.update-sample-box', $remoteCons->id), [
            'sample_box_status' => Consultation::SAMPLE_BOX_DELIVERED,
        ]);
        $remoteCons->refresh();
        $this->assertEquals(Consultation::SAMPLE_BOX_DELIVERED, $remoteCons->sample_box_status);

        // Cố tình sửa lại sau khi đã delivered -> Phải bị chặn với thông báo lỗi
        $blockedResponse = $this->put(route('admin.consultations.update-sample-box', $remoteCons->id), [
            'sample_box_status' => Consultation::SAMPLE_BOX_SHIPPED,
        ]);
        $blockedResponse->assertSessionHas('error');
    }

    /**
     * 8. Bảo hiểm vừa vặn: Tuyệt đối không được bật nếu số đo chưa đạt trạng thái "confirmed"
     */
    public function test_fit_guarantee_cannot_be_activated_without_confirmed_measurement(): void
    {
        $this->actingAs($this->adminUser);

        $remoteCons = Consultation::create([
            'customer_name' => 'Khách Đo Video',
            'customer_phone' => '0977889900',
            'address' => 'Phường 5, TP. Cà Mau',
            'province_code' => '96',
            'province_name' => 'Tỉnh Cà Mau',
            'service_mode' => Consultation::SERVICE_MODE_REMOTE,
            'contact_method' => 'zalo_video',
            'measurement_status' => Consultation::MEASUREMENT_PENDING,
            'fit_guarantee_eligible' => false,
            'preferred_date' => now()->addDays(2)->toDateString(),
            'status' => 'pending',
        ]);

        // Cố tình kích hoạt bảo hiểm khi đang ở trạng thái guided_via_call
        $this->put(route('admin.consultations.update-measurement', $remoteCons->id), [
            'measurement_status' => Consultation::MEASUREMENT_GUIDED,
            'fit_guarantee_eligible' => '1',
        ]);
        $remoteCons->refresh();
        $this->assertEquals(Consultation::MEASUREMENT_GUIDED, $remoteCons->measurement_status);
        $this->assertFalse($remoteCons->fit_guarantee_eligible, 'Chưa chốt confirmed thì không được bật bảo hiểm');

        // Chuyển sang confirmed -> Bảo hiểm được kích hoạt và lưu vết người thẩm định
        $this->put(route('admin.consultations.update-measurement', $remoteCons->id), [
            'measurement_status' => Consultation::MEASUREMENT_CONFIRMED,
            'fit_guarantee_eligible' => '1',
        ]);
        $remoteCons->refresh();
        $this->assertEquals(Consultation::MEASUREMENT_CONFIRMED, $remoteCons->measurement_status);
        $this->assertTrue($remoteCons->fit_guarantee_eligible);
        $this->assertEquals($this->adminUser->id, $remoteCons->fit_guarantee_confirmed_by);
        $this->assertNotNull($remoteCons->fit_guarantee_confirmed_at);
    }

    /**
     * 9. Khóa số đo và bảo hiểm tuyệt đối sau khi đã lập báo giá
     */
    public function test_measurement_is_strictly_locked_after_quotation_is_generated(): void
    {
        $this->actingAs($this->adminUser);

        $category = Category::create([
            'name' => 'Rèm Vải Cao Cấp',
            'slug' => 'rem-vai-cao-cap',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Rèm Vải Nhập Khẩu Nhật',
            'slug' => 'rem-vai-nhap-khau-nhat',
            'sku' => 'RV-001',
            'price' => 500000,
            'price_unit' => 'sqm',
            'is_active' => true,
        ]);

        $remoteCons = Consultation::create([
            'customer_name' => 'Khách Có Báo Giá',
            'customer_phone' => '0988776655',
            'address' => 'Đầm Dơi, Cà Mau',
            'province_code' => '96',
            'province_name' => 'Tỉnh Cà Mau',
            'service_mode' => Consultation::SERVICE_MODE_REMOTE,
            'contact_method' => 'zalo_video',
            'measurement_status' => Consultation::MEASUREMENT_CONFIRMED,
            'fit_guarantee_eligible' => true,
            'preferred_date' => now()->addDays(2)->toDateString(),
            'status' => 'surveying',
        ]);

        ConsultationWindow::create([
            'consultation_id' => $remoteCons->id,
            'room_name' => 'Phòng Khách',
            'product_id' => $product->id,
            'product_name' => $product->name,
            'width' => 200,
            'height' => 250,
            'install_type' => 'inside',
            'calculated_units' => 5.0,
            'unit_label' => 'm²',
            'unit_price' => 500000,
            'fabric_color' => 'Màu be',
            'has_sheer' => false,
            'quantity' => 1,
            'subtotal' => 2500000,
        ]);

        // Tạo báo giá chính thức
        $quoteResponse = $this->post(route('admin.quotations.generate', $remoteCons->id), []);
        $quoteResponse->assertSessionHasNoErrors();
        $this->assertEquals(1, $remoteCons->quotations()->count());

        // Thử đổi trạng thái số đo sau khi đã có báo giá -> Phải bị từ chối và báo lỗi
        $lockResponse = $this->put(route('admin.consultations.update-measurement', $remoteCons->id), [
            'measurement_status' => Consultation::MEASUREMENT_SELF,
            'fit_guarantee_eligible' => '0',
        ]);
        $lockResponse->assertSessionHas('error');

        $remoteCons->refresh();
        $this->assertEquals(Consultation::MEASUREMENT_CONFIRMED, $remoteCons->measurement_status, 'Số đo phải giữ nguyên trạng thái confirmed');
        $this->assertTrue($remoteCons->fit_guarantee_eligible, 'Bảo hiểm phải giữ nguyên không bị hủy');
    }

    /**
     * 10. Chặn lập báo giá nếu tư vấn từ xa chưa được chốt số đo
     */
    public function test_quotation_generation_blocked_if_remote_measurement_not_confirmed(): void
    {
        $this->actingAs($this->adminUser);

        $category = Category::create([
            'name' => 'Rèm Vải Cao Cấp 2',
            'slug' => 'rem-vai-cao-cap-2',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Rèm Vải Bỉ Chống Nắng',
            'slug' => 'rem-vai-bi-chong-nang',
            'sku' => 'RV-002',
            'price' => 600000,
            'price_unit' => 'sqm',
            'is_active' => true,
        ]);

        $remoteCons = Consultation::create([
            'customer_name' => 'Khách Chưa Chốt Số Đo',
            'customer_phone' => '0933221100',
            'address' => 'Thới Bình, Cà Mau',
            'province_code' => '96',
            'province_name' => 'Tỉnh Cà Mau',
            'service_mode' => Consultation::SERVICE_MODE_REMOTE,
            'contact_method' => 'zalo_video',
            'measurement_status' => Consultation::MEASUREMENT_PENDING, // Chưa chốt số đo
            'fit_guarantee_eligible' => false,
            'preferred_date' => now()->addDays(2)->toDateString(),
            'status' => 'pending',
        ]);

        ConsultationWindow::create([
            'consultation_id' => $remoteCons->id,
            'room_name' => 'Phòng Khách',
            'product_id' => $product->id,
            'product_name' => $product->name,
            'width' => 200,
            'height' => 250,
            'install_type' => 'inside',
            'calculated_units' => 5.0,
            'unit_label' => 'm²',
            'unit_price' => 600000,
            'fabric_color' => 'Màu ghi',
            'has_sheer' => false,
            'quantity' => 1,
            'subtotal' => 3000000,
        ]);

        // Cố tình lập báo giá khi measurement_status vẫn là pending -> Phải bị chặn
        $blockedQuote = $this->post(route('admin.quotations.generate', $remoteCons->id), []);
        $blockedQuote->assertSessionHas('error');
        $this->assertEquals(0, $remoteCons->quotations()->count(), 'Không được tạo báo giá khi chưa chốt số đo');
    }
}
