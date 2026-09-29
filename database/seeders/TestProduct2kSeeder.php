<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class TestProduct2kSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::first();
        $catId = $category ? $category->id : 1;

        $existing = Product::where('slug', 'rem-vai-test-checkout-ngan-hang-2k')->first();
        if ($existing) {
            $existing->update([
                'price' => 2000,
                'sale_price' => 2000,
                'price_unit' => 'piece',
                'stock' => 999,
                'is_active' => true,
                'is_featured' => true,
            ]);
            echo "Updated existing 2k product: ID " . $existing->id . PHP_EOL;
            return;
        }

        $sampleProduct = Product::first();
        $sampleImage = $sampleProduct && $sampleProduct->image ? $sampleProduct->image : '/images/curtains/curtain-1.jpg';

        $product = Product::create([
            'category_id' => $catId,
            'name' => 'Rèm Vải Test Checkout Ngân Hàng 2K',
            'slug' => 'rem-vai-test-checkout-ngan-hang-2k',
            'sku' => 'REM-TEST-2K',
            'price' => 2000,
            'sale_price' => 2000,
            'price_unit' => 'piece',
            'min_area' => 1.0,
            'min_width' => 50,
            'max_width' => 500,
            'min_height' => 50,
            'max_height' => 400,
            'blackout_rate' => 100,
            'installation_type' => 'both',
            'stock' => 999,
            'material' => 'Vải Canvas thử nghiệm',
            'origin' => 'CurtainLux Test Lab',
            'description' => 'Sản phẩm mẫu đặc biệt với giá chỉ 2.000 VNĐ phục vụ kiểm thử thanh toán chuyển khoản ngân hàng (VietQR / SePay) trực tiếp trên website.',
            'image' => $sampleImage,
            'is_featured' => true,
            'is_active' => true,
        ]);

        echo "Created 2k product successfully: ID " . $product->id . " | Slug: " . $product->slug . PHP_EOL;
    }
}
