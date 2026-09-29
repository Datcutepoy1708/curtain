<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        if ($products->isEmpty()) return;

        $reviewSamples = [
            [
                'name' => 'Anh Hoàng Dũng',
                'phone' => '0912***456',
                'rating' => 5,
                'comment' => 'Vải rèm 2 lớp Bỉ rất đẹp, thớ vải dày dặn và độ rũ tự nhiên chuẩn phong cách Japandi. Thợ CurtainLux đến đo tận nhà rất đúng giờ và tư vấn chọn màu hợp sàn gỗ.',
            ],
            [
                'name' => 'Chị Mai Lan',
                'phone' => '0988***123',
                'rating' => 5,
                'comment' => 'Cửa sổ phòng ngủ Master hướng Tây nắng gắt mà lắp loại rèm này vào cản nhiệt rõ rệt, phòng mát hơn hẳn. Ray trượt chạy cực kỳ êm ái không hề nghe tiếng động.',
            ],
            [
                'name' => 'Bác Trần Văn Quý',
                'phone' => '0903***789',
                'rating' => 5,
                'comment' => 'Rèm cầu vồng lắp cho căn hộ chung cư rất gọn và sang. Kéo so le lấy sáng rất tiện lợi. Gia đình tôi rất ưng ý với thái độ phục vụ của xưởng.',
            ],
            [
                'name' => 'Chị Phương Thảo',
                'phone' => '0975***668',
                'rating' => 4,
                'comment' => 'Màu sắc thực tế bên ngoài rất giống mẫu catalogue. Giao hàng và lắp đặt nhanh chóng trong vòng 48h. Sẽ tiếp tục ủng hộ khi làm rèm cho phòng các bé.',
            ],
            [
                'name' => 'Anh Quốc Tuấn',
                'phone' => '0934***555',
                'rating' => 5,
                'comment' => 'Hệ động cơ điện thông minh điều khiển qua App rất nhạy, có thể hẹn giờ mở rèm buổi sáng. Đường kim mũi chỉ may giấu chỉ rất tinh xảo và đều nếp sóng.',
            ],
        ];

        foreach ($products as $product) {
            // Add 2-3 reviews per product if not already having reviews
            if ($product->reviews()->count() < 2) {
                foreach (array_slice($reviewSamples, 0, rand(2, 4)) as $sample) {
                    Review::create([
                        'product_id'     => $product->id,
                        'customer_name'  => $sample['name'],
                        'customer_phone' => $sample['phone'],
                        'rating'         => $sample['rating'],
                        'comment'        => $sample['comment'],
                        'status'         => 'approved',
                    ]);
                }
            }
        }
    }
}
