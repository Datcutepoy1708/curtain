<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use App\Models\AuditLog;
use App\Models\User;

class FaqAndAuditSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed FAQs
        $faqs = [
            [
                'question' => 'Dịch vụ mang mẫu và khảo sát đo đạc tại nhà có mất phí không?',
                'answer' => 'Dịch vụ khảo sát tận nhà và mang mẫu vải thực tế của CurtainLux là hoàn toàn MIỄN PHÍ 100% trong khu vực nội thành. Bạn có thể thoải mái xem chất liệu, đối chiếu ánh sáng phòng thực tế mà không phải chịu bất kỳ ràng buộc nào.',
                'category' => 'do_dac',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Làm sao để biết căn phòng của tôi nên dùng rèm vải 2 lớp hay rèm cầu vồng?',
                'answer' => 'Rèm vải 2 lớp (vải chính cản sáng + voan mềm) rất thích hợp cho phòng khách và phòng ngủ master rộng rãi, mang lại nét ấm cúng và sang trọng. Rèm cầu vồng Hàn Quốc nhỏ gọn, hiện đại, điều chỉnh ánh sáng linh hoạt, rất lý tưởng cho phòng ngủ nhỏ, phòng làm việc và cửa sổ chung cư.',
                'category' => 'chat_lieu',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Thời gian may đo và hoàn thiện lắp đặt rèm mất bao lâu?',
                'answer' => 'Thông thường từ 2 đến 4 ngày làm việc kể từ lúc chốt số đo và mẫu rèm. Đối với các đơn rèm vải nhập khẩu Châu Âu hoặc công trình biệt thự lớn, thời gian có thể từ 4 đến 6 ngày để đảm bảo độ rủ sóng hoàn mỹ nhất.',
                'category' => 'lap_dat',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'CurtainLux có chính sách bảo hành như thế nào?',
                'answer' => 'Tất cả mẫu rèm tại CurtainLux đều được bảo hành chính hãng: Bảo hành phụ kiện ray kéo và bi chạy 2 năm; bảo hành động cơ rèm tự động từ 3 đến 5 năm đổi mới; bảo hành đường may và chất lượng vải 12 tháng.',
                'category' => 'bao_hanh',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Rèm cản sáng 100% (Blackout) khác gì so với rèm cản sáng 80%?',
                'answer' => 'Rèm cản sáng 100% được dệt công nghệ 3 lớp hoặc phủ lớp silicon nhiệt, ngăn hoàn toàn tia UV và ánh sáng gắt, giúp phòng tối hoàn toàn ngay cả giữa trưa hè oi bức, thích hợp cho người nhạy cảm với ánh sáng khi ngủ. Rèm cản sáng 80% cho phép một lượng ánh sáng mờ dịu đi qua, giữ phòng thông thoáng.',
                'category' => 'chat_lieu',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'question' => 'Tôi có thể thanh toán theo những hình thức nào?',
                'answer' => 'CurtainLux hỗ trợ đa dạng phương thức: Chuyển khoản VietQR tự động, thanh toán trực tuyến qua cổng VNPAY (ATM nội địa / Visa / Mastercard), hoặc tiền mặt khi kỹ thuật viên đến lắp đặt và nghiệm thu hoàn tất.',
                'category' => 'bao_hanh',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'question' => 'Cách tự vệ sinh và giặt rèm cửa đúng chuẩn tại nhà?',
                'answer' => 'Với rèm cuốn và rèm cầu vồng, bạn chỉ cần dùng chổi lông mềm hoặc máy hút bụi đầu nhỏ lau nhẹ. Với rèm vải 2 lớp, nên giặt khô định kỳ 6-12 tháng một lần để giữ nếp sóng vải và độ bền của lớp phủ chống nắng.',
                'category' => 'general',
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], $faq);
        }

        // 2. Seed realistic recent Audit Logs
        $admin = User::where('role', 'admin')->first();
        $tech = User::where('role', 'technician')->first();
        $staff = User::where('role', 'staff')->first();

        $logs = [
            [
                'user_id' => $admin ? $admin->id : 1,
                'user_name' => $admin ? $admin->name : 'Quản Trị Viên CurtainLux',
                'user_role' => 'admin',
                'action' => 'login',
                'module' => 'auth',
                'description' => 'Đăng nhập thành công vào trang quản trị',
                'target_id' => null,
                'old_values' => null,
                'new_values' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subHours(2),
            ],
            [
                'user_id' => $admin ? $admin->id : 1,
                'user_name' => $admin ? $admin->name : 'Quản Trị Viên CurtainLux',
                'action' => 'assign',
                'module' => 'consultations',
                'description' => 'Phân công thợ kỹ thuật Trần Văn Hùng khảo sát lịch hẹn #CS-1002 tại Thảo Điền',
                'target_id' => 'CS-1002',
                'old_values' => ['staff_id' => null, 'status' => 'pending'],
                'new_values' => ['staff_id' => $tech ? $tech->id : 9, 'status' => 'assigned'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now()->subHours(1)->subMinutes(45),
            ],
            [
                'user_id' => $tech ? $tech->id : 9,
                'user_name' => $tech ? $tech->name : 'Trần Văn Hùng',
                'user_role' => 'technician',
                'action' => 'update_status',
                'module' => 'consultations',
                'description' => 'Cập nhật kết quả đo đạc 3 ô cửa kính phòng khách cho lịch hẹn #CS-1002',
                'target_id' => 'CS-1002',
                'old_values' => ['windows_measured' => 0],
                'new_values' => ['windows_measured' => 3, 'status' => 'surveying'],
                'ip_address' => '115.78.23.14',
                'user_agent' => 'CurtainLux Mobile Survey App',
                'created_at' => now()->subHours(1)->subMinutes(15),
            ],
            [
                'user_id' => $admin ? $admin->id : 1,
                'user_name' => $admin ? $admin->name : 'Quản Trị Viên CurtainLux',
                'user_role' => 'admin',
                'action' => 'send',
                'module' => 'quotations',
                'description' => 'Gửi bảng báo giá chính thức #BG-9921 cho khách hàng Trần Thị Thu Hà (Trị giá: 14.250.000 ₫)',
                'target_id' => 'BG-9921',
                'old_values' => ['status' => 'draft'],
                'new_values' => ['status' => 'sent', 'total_amount' => 14250000],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now()->subMinutes(50),
            ],
            [
                'user_id' => $admin ? $admin->id : 1,
                'user_name' => $admin ? $admin->name : 'Quản Trị Viên CurtainLux',
                'user_role' => 'admin',
                'action' => 'create',
                'module' => 'discounts',
                'description' => 'Tạo mã khuyến mãi mới MUAHE2026 giảm 15% cho rèm cản nhiệt',
                'target_id' => 'MUAHE2026',
                'old_values' => null,
                'new_values' => ['code' => 'MUAHE2026', 'discount_percent' => 15, 'min_order' => 3000000],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now()->subMinutes(30),
            ],
            [
                'user_id' => $staff ? $staff->id : 10,
                'user_name' => $staff ? $staff->name : 'Lê Hoàng Long',
                'user_role' => 'staff',
                'action' => 'create',
                'module' => 'faqs',
                'description' => 'Thêm câu hỏi FAQ mới: "Dịch vụ mang mẫu và khảo sát đo đạc tại nhà có mất phí không?"',
                'target_id' => 'FAQ-1',
                'old_values' => null,
                'new_values' => ['question' => 'Dịch vụ mang mẫu và khảo sát đo đạc tại nhà có mất phí không?'],
                'ip_address' => '14.232.180.99',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now()->subMinutes(12),
            ],
        ];

        foreach ($logs as $log) {
            AuditLog::create($log);
        }
    }
}
