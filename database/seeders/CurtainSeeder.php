<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\CurtainOptionGroup;
use App\Models\CurtainOptionValue;
use App\Models\Consultation;

class CurtainSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo Danh Mục Rèm Cửa
        $catData = [
            [
                'name' => 'Rèm Vải 2 Lớp',
                'slug' => 'rem-vai-2-lop',
                'description' => 'Rèm vải cao cấp gồm 1 lớp gấm/nhung cản sáng và 1 lớp voan thêu tinh tế sang trọng.',
                'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Rèm Cầu Vồng Hàn Quốc',
                'slug' => 'rem-cau-vong-han-quoc',
                'description' => 'Rèm cầu vồng Combi nhập khẩu Hàn Quốc điều chỉnh ánh sáng linh hoạt, phong cách hiện đại.',
                'image' => 'https://images.unsplash.com/photo-1615874959474-d609969a20ed?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Rèm Cuốn Văn Phòng',
                'slug' => 'rem-cuon-van-phong',
                'description' => 'Rèm cuốn trơn cản sáng nhiệt, độ bền cao, phù hợp cho văn phòng, chung cư và showroom.',
                'image' => 'https://images.unsplash.com/photo-1540518614846-7ede433c5172?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Rèm Sáo Gỗ Tự Nhiên',
                'slug' => 'rem-sao-go-tu-nhien',
                'description' => 'Rèm sáo gỗ sồi, gỗ bách tự nhiên 100% xử lý sấy nhiệt chống cong vênh mối mọt.',
                'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'Rèm Tự Động Thông Minh',
                'slug' => 'rem-tu-dong-thong-minh',
                'description' => 'Rèm động cơ tự động kết nối Smart Home, điều khiển qua Remote, Smartphone hoặc giọng nói.',
                'image' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=600&q=80',
                'is_active' => true,
            ],
        ];

        $categories = [];
        foreach ($catData as $item) {
            $categories[$item['slug']] = Category::firstOrCreate(['slug' => $item['slug']], $item);
        }

        // 2. Tạo Sản Phẩm Rèm Cửa
        $products = [
            [
                'category_id' => $categories['rem-vai-2-lop']->id,
                'name' => 'Rèm Vải Nhập Khẩu Bỉ Luxury Velvet',
                'slug' => 'rem-vai-nhap-khau-bi-luxury-velvet',
                'sku' => 'REM-BI-01',
                'price' => 1250000,
                'sale_price' => 990000,
                'price_unit' => 'meter', // Tính theo mét ngang hoàn thiện
                'min_area' => 1.00,
                'min_width' => 100,
                'max_width' => 600,
                'min_height' => 100,
                'max_height' => 450,
                'blackout_rate' => 100,
                'installation_type' => 'indoor',
                'stock' => 45,
                'material' => 'Vải Nhung Bỉ & Voan Thêu',
                'origin' => 'Bỉ (Belgium)',
                'description' => 'Mẫu rèm vải 2 lớp sang trọng bậc nhất cho phòng khách biệt thự & penthouse. Sợi nhung mịn cản sáng 100%, chống nóng, cách âm vượt trội.',
                'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $categories['rem-vai-2-lop']->id,
                'name' => 'Rèm Vải Gấm Nhật Bản Anti-UV',
                'slug' => 'rem-vai-gam-nhat-ban-anti-uv',
                'sku' => 'REM-JP-02',
                'price' => 850000,
                'sale_price' => 780000,
                'price_unit' => 'meter',
                'min_area' => 1.00,
                'min_width' => 100,
                'max_width' => 500,
                'min_height' => 100,
                'max_height' => 380,
                'blackout_rate' => 95,
                'installation_type' => 'indoor',
                'stock' => 30,
                'material' => 'Vải Gấm Cao Cấp',
                'origin' => 'Nhật Bản',
                'description' => 'Sợi vải dệt 3 lớp chống tia cực tím UV 99%, giữ nhiệt độ phòng ổn định quanh năm, màu sắc be ấm Japandi tinh tế.',
                'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $categories['rem-cau-vong-han-quoc']->id,
                'name' => 'Rèm Cầu Vồng Modero Basic Korea',
                'slug' => 'rem-cau-vong-modero-basic-korea',
                'sku' => 'REM-CV-03',
                'price' => 620000,
                'sale_price' => 550000,
                'price_unit' => 'sqm', // Tính theo m2
                'min_area' => 1.00,
                'min_width' => 40,
                'max_width' => 280,
                'min_height' => 50,
                'max_height' => 320,
                'blackout_rate' => 85,
                'installation_type' => 'indoor',
                'stock' => 60,
                'material' => '100% Polyester Kháng Khuẩn Hàn Quốc',
                'origin' => 'Hàn Quốc',
                'description' => 'Rèm cầu vồng Combi Modero với 2 lớp vải đan xen tạo hiệu ứng ánh sáng tuyệt mỹ. Dễ dàng vệ sinh, không bám bụi.',
                'image' => 'https://images.unsplash.com/photo-1615874959474-d609969a20ed?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $categories['rem-cuon-van-phong']->id,
                'name' => 'Rèm Cuốn Trơn Cản Nhiệt Star-Blinds',
                'slug' => 'rem-cuon-tron-can-nhiet-star-blinds',
                'sku' => 'REM-RC-05',
                'price' => 280000,
                'sale_price' => 240000,
                'price_unit' => 'sqm',
                'min_area' => 1.00,
                'min_width' => 40,
                'max_width' => 300,
                'min_height' => 50,
                'max_height' => 350,
                'blackout_rate' => 100,
                'installation_type' => 'indoor',
                'stock' => 120,
                'material' => 'Vải Phủ Nhựa PVC Cản Nắng',
                'origin' => 'Việt Nam (Công nghệ Mỹ)',
                'description' => 'Giải pháp chống nắng và cách nhiệt cho văn phòng, phòng ngủ chung cư với chi phí tối ưu nhất.',
                'image' => 'https://images.unsplash.com/photo-1540518614846-7ede433c5172?auto=format&fit=crop&w=800&q=80',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'category_id' => $categories['rem-sao-go-tu-nhien']->id,
                'name' => 'Rèm Gỗ Sồi Nga Bản 5cm Sơn UV Cao Cấp',
                'slug' => 'rem-go-soi-nga-ban-5cm-son-uv-cao-cap',
                'sku' => 'REM-SG-06',
                'price' => 950000,
                'sale_price' => 890000,
                'price_unit' => 'sqm',
                'min_area' => 1.00,
                'min_width' => 50,
                'max_width' => 240,
                'min_height' => 60,
                'max_height' => 300,
                'blackout_rate' => 95,
                'installation_type' => 'indoor',
                'stock' => 18,
                'material' => 'Gỗ Sồi Nga Tự Nhiên 100%',
                'origin' => 'Nga / Lắp ráp Việt Nam',
                'description' => 'Lá gỗ rộng 5cm được sấy khô chống cong vênh, sơn phủ 3 lớp UV chống bạc màu dưới ánh nắng gắt.',
                'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $categories['rem-tu-dong-thong-minh']->id,
                'name' => 'Bộ Động Cơ Rèm Tuya Zigbee Smart Curtain',
                'slug' => 'bo-dong-co-rem-tuya-zigbee-smart-curtain',
                'sku' => 'REM-TD-07',
                'price' => 3500000,
                'sale_price' => 3190000,
                'price_unit' => 'piece',
                'min_area' => 1.00,
                'min_width' => 100,
                'max_width' => 800,
                'min_height' => 100,
                'max_height' => 500,
                'blackout_rate' => 100,
                'installation_type' => 'indoor',
                'stock' => 10,
                'material' => 'Hợp kim nhôm & Động cơ Brushless',
                'origin' => 'Chính Hãng Tuya',
                'description' => 'Động cơ siêu êm dưới 30dB, kéo được rèm nặng đến 60kg, điều khiển qua Smartphone, Google Home, Apple HomeKit & remote cầm tay.',
                'image' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=800&q=80',
                'is_featured' => true,
                'is_active' => true,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 3. Tạo Tùy Chọn Gia Công & Phụ Kiện (Curtain Options)
        $groups = [
            [
                'name' => 'Kiểu May Rèm',
                'code' => 'header_style',
                'applies_to' => 'fabric',
                'is_required' => true,
                'sort_order' => 1,
                'values' => [
                    ['name' => 'May Định Hình S-Fold (Hiện đại, tạo sóng đều đẹp)', 'price_impact_type' => 'fixed', 'extra_price' => 0, 'is_default' => true],
                    ['name' => 'May Ore (Xỏ khuyên tròn truyền thống)', 'price_impact_type' => 'fixed', 'extra_price' => 0, 'is_default' => false],
                    ['name' => 'May Xếp Ly (Xếp 2 ly / 3 ly thanh lịch)', 'price_impact_type' => 'fixed', 'extra_price' => 0, 'is_default' => false],
                ]
            ],
            [
                'name' => 'Hệ Thanh Ray Treo',
                'code' => 'track_type',
                'applies_to' => 'all',
                'is_required' => true,
                'sort_order' => 2,
                'values' => [
                    ['name' => 'Ray Hợp Kim Nhôm Tiêu Chuẩn', 'price_impact_type' => 'fixed', 'extra_price' => 0, 'is_default' => true],
                    ['name' => 'Ray Bi Trượt Chống Ồn Chuyên Dụng Khách Sạn', 'price_impact_type' => 'per_meter', 'extra_price' => 50000, 'is_default' => false],
                    ['name' => 'Thanh Suốt Gỗ Sồi Tự Nhiên Cổ Điển', 'price_impact_type' => 'per_meter', 'extra_price' => 80000, 'is_default' => false],
                ]
            ],
            [
                'name' => 'Tùy Chọn Động Cơ Tự Động',
                'code' => 'motor_type',
                'applies_to' => 'all',
                'is_required' => false,
                'sort_order' => 3,
                'values' => [
                    ['name' => 'Kéo tay thủ công (Không dùng động cơ)', 'price_impact_type' => 'fixed', 'extra_price' => 0, 'is_default' => true],
                    ['name' => 'Động Cơ Tuya Zigbee / WiFi (Điều khiển App & Remote)', 'price_impact_type' => 'fixed', 'extra_price' => 1500000, 'is_default' => false],
                    ['name' => 'Động Cơ Aqara C2 Siêu Êm (Hệ sinh thái Apple HomeKit)', 'price_impact_type' => 'fixed', 'extra_price' => 2300000, 'is_default' => false],
                    ['name' => 'Động Cơ Cao Cấp Somfy Pháp (Bảo hành 5 năm)', 'price_impact_type' => 'fixed', 'extra_price' => 3800000, 'is_default' => false],
                ]
            ],
            [
                'name' => 'Lớp Voan Lót Đi Kèm',
                'code' => 'sheer_layer',
                'applies_to' => 'fabric',
                'is_required' => false,
                'sort_order' => 4,
                'values' => [
                    ['name' => 'Không lấy lớp voan (Chỉ may 1 lớp vải chính)', 'price_impact_type' => 'fixed', 'extra_price' => 0, 'is_default' => true],
                    ['name' => 'Lớp Voan Trắng Xước Basic Nhẹ Nhàng', 'price_impact_type' => 'per_meter', 'extra_price' => 220000, 'is_default' => false],
                    ['name' => 'Lớp Voan Thêu Hoa Phong Cách Hoàng Gia', 'price_impact_type' => 'per_meter', 'extra_price' => 380000, 'is_default' => false],
                ]
            ],
        ];

        foreach ($groups as $gData) {
            $values = $gData['values'];
            unset($gData['values']);

            $group = CurtainOptionGroup::firstOrCreate(['code' => $gData['code']], $gData);

            foreach ($values as $index => $vData) {
                $vData['group_id'] = $group->id;
                $vData['sort_order'] = $index + 1;
                CurtainOptionValue::firstOrCreate([
                    'group_id' => $group->id,
                    'name' => $vData['name']
                ], $vData);
            }
        }

        // 4. Tạo Một Vài Lịch Hẹn Khảo Sát Mẫu
        Consultation::firstOrCreate(['code' => 'LH-20260917-001'], [
            'customer_name' => 'Nguyễn Anh Tuấn',
            'customer_phone' => '0912345678',
            'customer_email' => 'anhtuan.nguyen@example.com',
            'address' => 'Căn hộ 14.08, Tòa Landmark 2, Vinhomes Central Park',
            'city' => 'Hồ Chí Minh',
            'district' => 'Bình Thạnh',
            'ward' => 'Phường 22',
            'preferred_date' => date('Y-m-d', strtotime('+1 day')),
            'preferred_time' => 'Sáng (09:00 - 11:30)',
            'curtain_types_interested' => ['rem-vai-2-lop', 'rem-cau-vong-han-quoc'],
            'estimated_windows' => 4,
            'notes' => 'Cần mang mẫu vải gấm tông màu be ấm / Japandi để so màu sơn tường và sofa.',
            'status' => 'pending',
        ]);

        Consultation::firstOrCreate(['code' => 'LH-20260917-002'], [
            'customer_name' => 'Trần Thị Thu Hà',
            'customer_phone' => '0988776655',
            'customer_email' => 'thuha.tran@example.com',
            'address' => 'Biệt thự B2-12 Đảo Kim Cương, Phường Thạnh Mỹ Lợi',
            'city' => 'Hồ Chí Minh',
            'district' => 'Thủ Đức',
            'ward' => 'Thạnh Mỹ Lợi',
            'preferred_date' => date('Y-m-d', strtotime('+2 days')),
            'preferred_time' => 'Chiều (14:00 - 16:30)',
            'curtain_types_interested' => ['rem-vai-2-lop', 'rem-tu-dong-thong-minh'],
            'estimated_windows' => 8,
            'notes' => 'Nhà có cửa thông tầng cao 6.5m, cần tư vấn động cơ Somfy công suất lớn.',
            'status' => 'pending',
        ]);

        // 5. Tạo Các Đơn Đặt Hàng Rèm Mẫu
        $p1 = Product::first();
        $p2 = Product::skip(1)->first();

        $order1 = \App\Models\Order::firstOrCreate(['order_code' => 'DH-20260915-8821'], [
            'customer_name' => 'Phạm Minh Đức',
            'customer_phone' => '0903112233',
            'customer_email' => 'minhduc.pham@gmail.com',
            'shipping_address' => 'P.1204 Tháp Aqua 1, Vinhomes Golden River, Bến Nghé, Quận 1',
            'city' => 'TP. Hồ Chí Minh',
            'district' => 'Quận 1',
            'total_amount' => 14580000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'partially_paid',
            'order_status' => 'manufacturing',
            'notes' => 'May rèm định vị sóng đều, thanh ray nhôm xước âm trần thạch cao.',
            'created_at' => now()->subDays(2),
        ]);

        if ($order1->items()->count() === 0 && $p1) {
            $order1->items()->create([
                'product_id' => $p1->id,
                'product_name' => $p1->name,
                'room_label' => 'Cửa chính phòng khách',
                'width' => 320.0,
                'height' => 280.0,
                'mount_type' => 'inside',
                'calculated_units' => 8.96,
                'unit_price' => 990000,
                'selected_options' => [
                    'Kiểu may' => 'Định hình sóng rèm cao cấp (Wave style)',
                    'Hệ thanh treo' => 'Thanh ray nhôm định hình chống ồn Forest Hà Lan',
                    'Lớp voan' => 'Lớp Voan Trắng Xước Basic Nhẹ Nhàng'
                ],
                'quantity' => 1,
                'subtotal' => 8870400,
            ]);

            if ($p2) {
                $order1->items()->create([
                    'product_id' => $p2->id,
                    'product_name' => $p2->name,
                    'room_label' => 'Phòng ngủ Master',
                    'width' => 240.0,
                    'height' => 260.0,
                    'mount_type' => 'inside',
                    'calculated_units' => 6.24,
                    'unit_price' => 780000,
                    'selected_options' => [
                        'Kiểu may' => 'May Ore (Khoen tròn luồn thanh)',
                        'Hệ thanh treo' => 'Thanh ray nhôm định hình chống ồn Forest Hà Lan'
                    ],
                    'quantity' => 1,
                    'subtotal' => 5709600,
                ]);
            }
        }

        $order2 = \App\Models\Order::firstOrCreate(['order_code' => 'DH-20260916-9932'], [
            'customer_name' => 'Lê Hoàng Yến',
            'customer_phone' => '0934556677',
            'customer_email' => 'hoangyen.le@gmail.com',
            'shipping_address' => 'Số 45 Đường số 8, KDC Him Lam, Tân Hưng, Quận 7',
            'city' => 'TP. Hồ Chí Minh',
            'district' => 'Quận 7',
            'total_amount' => 8950000,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'notes' => 'Khách yêu cầu thợ mang theo máy khoan hút bụi khi lắp đặt.',
            'created_at' => now()->subDay(),
        ]);

        if ($order2->items()->count() === 0 && $p1) {
            $order2->items()->create([
                'product_id' => $p1->id,
                'product_name' => $p1->name,
                'room_label' => 'Ban công phòng làm việc',
                'width' => 280.0,
                'height' => 270.0,
                'mount_type' => 'outside',
                'calculated_units' => 7.56,
                'unit_price' => 990000,
                'selected_options' => [
                    'Kiểu may' => 'May xếp ly 2 nếp gọn gàng',
                    'Động cơ' => 'Động Cơ Tuya Zigbee / WiFi'
                ],
                'quantity' => 1,
                'subtotal' => 8950000,
            ]);
        }

        // 6. Tạo Mã Giảm Giá (Coupons)
        \App\Models\DiscountCode::firstOrCreate(['code' => 'CURTAINLUX10'], [
            'description' => 'Ưu đãi 10% cho đơn hàng đầu tiên chào mừng khách hàng mới',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'max_discount_amount' => 1500000,
            'min_order_value' => 3000000,
            'usage_limit' => 100,
            'used_count' => 8,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addMonths(2),
            'status' => 'active',
        ]);

        \App\Models\DiscountCode::firstOrCreate(['code' => 'TRIAN500K'], [
            'description' => 'Giảm trực tiếp 500.000₫ cho hóa đơn đặt may rèm từ 5 triệu',
            'discount_type' => 'fixed',
            'discount_value' => 500000,
            'min_order_value' => 5000000,
            'usage_limit' => 50,
            'used_count' => 14,
            'start_date' => now()->subDays(10),
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ]);

        // 7. Tạo Banners Quảng Cáo
        \App\Models\Banner::firstOrCreate(['title' => 'Bộ Sưu Tập Rèm Cửa Japandi 2026'], [
            'subtitle' => 'Gam màu be ấm, phong cách tối giản mộc mạc và sang trọng cho căn hộ hiện đại',
            'image_url' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1600&q=80',
            'link_url' => '/san-pham/rem-vai-nhap-khau-bi-luxury-velvet',
            'position' => 'hero',
            'sort_order' => 1,
            'status' => 'active',
        ]);

        \App\Models\Banner::firstOrCreate(['title' => 'Rèm Tự Động Kết Nối Nhà Thông Minh'], [
            'subtitle' => 'Động cơ Aqara & Somfy siêu êm, đóng mở tự động theo ngữ cảnh mặt trời mọc',
            'image_url' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=80',
            'link_url' => '/san-pham/rem-vai-gam-nhat-ban-anti-uv',
            'position' => 'promo',
            'sort_order' => 2,
            'status' => 'active',
        ]);

        // 8. Tạo Cẩm Nang & Tin Tức CMS
        \App\Models\News::firstOrCreate(['slug' => '5-sai-lam-pho-bien-khi-chon-rem-phong-ngu'], [
            'title' => '5 Sai lầm phổ biến khi chọn mua rèm phòng ngủ chống nắng',
            'category' => 'guide',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1540518614846-7ede433c5172?auto=format&fit=crop&w=800&q=80',
            'excerpt' => 'Tìm hiểu các lỗi thường gặp như chọn sai độ cản sáng, đo kích thước hụt mép tường khiến phòng ngủ bị chói sáng và cách khắc phục chuẩn chuyên gia.',
            'content' => 'Phòng ngủ là không gian nghỉ ngơi quan trọng nhất trong ngôi nhà. Một bộ rèm đạt chuẩn không chỉ cần có tính thẩm mỹ mà còn phải cản sáng tối thiểu 95-100%, cách âm tốt và tạo cảm giác thư giãn dịu mắt...',
            'status' => 'published',
            'views' => 452,
        ]);

        \App\Models\News::firstOrCreate(['slug' => 'xu-huong-rem-cua-phong-cach-japandi-2026'], [
            'title' => 'Xu hướng rèm cửa phong cách Japandi lên ngôi trong năm 2026',
            'category' => 'trends',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1615874959474-d609969a20ed?auto=format&fit=crop&w=800&q=80',
            'excerpt' => 'Sự hòa quyện tuyệt mỹ giữa nét mộc mạc Wabi-Sabi Nhật Bản và sự tinh tế tiện nghi Scandinavia qua những nếp rèm vải linen màu be ấm.',
            'content' => 'Năm 2026 chứng kiến sự trở lại mạnh mẽ của các tông màu trung tính ấm áp (Warm Neutrals). Thay vì các chất liệu bóng bẩy, gia chủ hiện đại ưa chuộng vải dệt thô tự nhiên, gấm mờ chống bám bụi...',
            'status' => 'published',
            'views' => 618,
        ]);

        // 9. Tạo Đánh Giá Sản Phẩm Mẫu
        if ($p1) {
            \App\Models\Review::firstOrCreate([
                'product_id' => $p1->id,
                'customer_name' => 'Chị Mai Lan',
            ], [
                'customer_phone' => '0918889900',
                'rating' => 5,
                'comment' => 'Rèm nhung Bỉ thực sự chất lượng vượt trội. Thợ đến đo rất tận tâm, tư vấn màu be phối tường cực kỳ chuẩn tone Japandi mình thích. Chắn sáng 100% ngủ ngon hơn hẳn!',
                'status' => 'approved',
            ]);

            \App\Models\Review::firstOrCreate([
                'product_id' => $p1->id,
                'customer_name' => 'Anh Quốc Bảo',
            ], [
                'customer_phone' => '0903445566',
                'rating' => 5,
                'comment' => 'Đặt may 3 cửa phòng khách và phòng ngủ, đường may sóng rèm định hình cực kỳ thẳng và đều. Động cơ Aqara chạy êm ru không nghe thấy tiếng.',
                'status' => 'approved',
            ]);
        }

        // 10. Tạo Cài Đặt Hệ Thống Mặc Định
        \App\Models\Setting::set('store_name', 'CurtainLux - Rèm Cửa Cao Cấp & Nội Thất Vải Tinh Tế');
        \App\Models\Setting::set('store_hotline', '0912.345.678');
        \App\Models\Setting::set('store_address', 'Số 168 Đường Nguyễn Văn Trỗi, Phường 8, Quận Phú Nhuận, TP. Hồ Chí Minh');
        \App\Models\Setting::set('store_email', 'contact@curtainlux.vn');
        \App\Models\Setting::set('store_hours', '08:00 - 20:30 (Mở cửa tất cả các ngày trong tuần)');
        \App\Models\Setting::set('bank_name', 'Vietcombank (VCB)');
        \App\Models\Setting::set('bank_account_number', '9888123456789');
        \App\Models\Setting::set('bank_account_name', 'CONG TY TNHH CURTAIN LUX VIET NAM');
        \App\Models\Setting::set('policy_survey', 'Miễn phí 100% dịch vụ mang cây mẫu vải thực tế đến tận nhà và đo đạc kích thước lọt lòng/phủ bì.');
        \App\Models\Setting::set('policy_warranty', 'Bảo hành 3 năm cho hệ thanh ray và động cơ thông minh; bảo hành 2 năm cho màu vải và sóng may rèm.');
    }
}

