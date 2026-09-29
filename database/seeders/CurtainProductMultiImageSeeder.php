<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class CurtainProductMultiImageSeeder extends Seeder
{
    public function run(): void
    {
        $galleryLibrary = [
            // Rèm vải & voan
            'fabric' => [
                'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1540518614846-7ede433c4ef2?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1000&q=85',
            ],
            // Rèm cầu vồng & rèm cuốn
            'rainbow' => [
                'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1000&q=85',
            ],
            // Rèm sáo gỗ
            'wood' => [
                'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1583847268964-b28dc8f51f92?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1000&q=85',
            ],
            // Rèm tổ ong & roman
            'cellular' => [
                'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1618219908412-a29a1bb7b86e?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=1000&q=85',
            ],
            // Rèm tự động & trần
            'motorized' => [
                'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1000&q=85',
                'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1000&q=85',
            ],
        ];

        $products = Product::all();

        foreach ($products as $product) {
            $slug = $product->slug;
            $name = mb_strtolower($product->name);

            // Determine image theme
            if (str_contains($slug, 'go') || str_contains($name, 'gỗ')) {
                $pool = $galleryLibrary['wood'];
            } elseif (str_contains($slug, 'to-ong') || str_contains($slug, 'roman') || str_contains($name, 'tổ ong') || str_contains($name, 'roman')) {
                $pool = $galleryLibrary['cellular'];
            } elseif (str_contains($slug, 'cau-vong') || str_contains($slug, 'cuon') || str_contains($name, 'cầu vồng') || str_contains($name, 'cuốn') || str_contains($name, 'lá dọc')) {
                $pool = $galleryLibrary['rainbow'];
            } elseif (str_contains($slug, 'dong-co') || str_contains($slug, 'tran') || str_contains($name, 'động cơ') || str_contains($name, 'tự động') || str_contains($name, 'somfy')) {
                $pool = $galleryLibrary['motorized'];
            } else {
                $pool = $galleryLibrary['fabric'];
            }

            // Always make sure primary image is item 0
            if ($product->image && !in_array($product->image, $pool)) {
                array_unshift($pool, $product->image);
            }

            // Remove existing to reseed cleanly
            ProductImage::where('product_id', $product->id)->delete();

            $seen = [];
            $sort = 1;
            foreach ($pool as $imgUrl) {
                if (in_array($imgUrl, $seen)) continue;
                $seen[] = $imgUrl;

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url'  => $imgUrl,
                    'is_primary' => $sort === 1,
                    'sort_order' => $sort,
                ]);

                $sort++;
                if ($sort > 4) break; // 4 high quality images per product
            }
        }
    }
}
