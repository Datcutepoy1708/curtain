<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images']);

        // Tìm kiếm theo từ khóa (Tên, Mã SKU, Chất liệu, Xuất xứ)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('material', 'like', "%{$search}%")
                  ->orWhere('origin', 'like', "%{$search}%");
            });
        }

        // Lọc theo Danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo Đơn vị tính
        if ($request->filled('price_unit')) {
            $query->where('price_unit', $request->price_unit);
        }

        // Lọc theo Mức giá
        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case 'under_500':
                    $query->where('price', '<', 500000);
                    break;
                case '500_1000':
                    $query->whereBetween('price', [500000, 1000000]);
                    break;
                case 'over_1000':
                    $query->where('price', '>', 1000000);
                    break;
            }
        }

        // Lọc theo Tình trạng kho
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock', '>', 10);
            } elseif ($request->stock_status === 'low_stock') {
                $query->whereBetween('stock', [1, 10]);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            }
        }

        // Lọc theo Nổi bật & Trạng thái
        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->is_featured == '1');
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'price_unit' => 'required|in:sqm,meter,piece',
            'stock' => 'required|integer|min:0',
            'blackout_rate' => 'nullable|integer|min:0|max:100',
            'min_area' => 'nullable|numeric|min:0',
            'min_width' => 'nullable|integer',
            'max_width' => 'nullable|integer',
            'min_height' => 'nullable|integer',
            'max_height' => 'nullable|integer',
            'installation_type' => 'nullable|string',
            'material' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:6144',
        ]);

        // Tạo sản phẩm
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sku' => $request->sku ?: 'REM-' . strtoupper(Str::random(6)),
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'price_unit' => $request->price_unit,
            'blackout_rate' => $request->blackout_rate ?: 100,
            'min_area' => $request->min_area ?: 1.0,
            'min_width' => $request->min_width ?: 100,
            'max_width' => $request->max_width ?: 600,
            'min_height' => $request->min_height ?: 100,
            'max_height' => $request->max_height ?: 450,
            'installation_type' => $request->installation_type ?: 'indoor',
            'stock' => $request->stock,
            'material' => $request->material,
            'origin' => $request->origin,
            'description' => $request->description,
            'image' => $request->image ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80',
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
        ]);

        // Xử lý Upload nhiều ảnh (Multi-image Upload)
        if ($request->hasFile('image_files')) {
            $isFirst = true;
            foreach ($request->file('image_files') as $idx => $file) {
                $path = $file->store('products', 'public');
                $imgUrl = '/storage/' . $path;

                // Nếu ảnh đầu tiên, gán làm ảnh đại diện chính
                if ($isFirst && !$request->filled('image')) {
                    $product->image = $imgUrl;
                    $product->save();
                    $isFirst = false;
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imgUrl,
                    'is_primary' => $idx === 0,
                    'sort_order' => $idx + 1,
                ]);
            }
        }

        // Xử lý các link ảnh thêm từ textarea nếu có
        if ($request->filled('additional_image_urls')) {
            $urls = preg_split('/[\r\n,]+/', $request->additional_image_urls);
            foreach ($urls as $i => $u) {
                $u = trim($u);
                if (filter_var($u, FILTER_VALIDATE_URL)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $u,
                        'is_primary' => false,
                        'sort_order' => 10 + $i,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Đã thêm mẫu rèm mới kèm bộ sưu tập hình ảnh thành công!');
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'images']);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'price_unit' => 'required|in:sqm,meter,piece',
            'stock' => 'required|integer|min:0',
            'blackout_rate' => 'nullable|integer|min:0|max:100',
            'min_area' => 'nullable|numeric|min:0',
            'min_width' => 'nullable|integer',
            'max_width' => 'nullable|integer',
            'min_height' => 'nullable|integer',
            'max_height' => 'nullable|integer',
            'installation_type' => 'nullable|string',
            'material' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:6144',
        ]);

        $imagePath = $request->image ?: $product->image;

        // Xử lý Upload thêm nhiều ảnh mới
        if ($request->hasFile('image_files')) {
            $currentOrder = $product->images()->max('sort_order') ?: 0;
            foreach ($request->file('image_files') as $idx => $file) {
                $path = $file->store('products', 'public');
                $imgUrl = '/storage/' . $path;

                if (!$imagePath || $imagePath === 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80') {
                    $imagePath = $imgUrl;
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imgUrl,
                    'is_primary' => false,
                    'sort_order' => $currentOrder + $idx + 1,
                ]);
            }
        }

        // Thêm link ảnh từ textarea
        if ($request->filled('additional_image_urls')) {
            $urls = preg_split('/[\r\n,]+/', $request->additional_image_urls);
            $currentOrder = $product->images()->max('sort_order') ?: 0;
            foreach ($urls as $i => $u) {
                $u = trim($u);
                if (filter_var($u, FILTER_VALIDATE_URL)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $u,
                        'is_primary' => false,
                        'sort_order' => $currentOrder + 10 + $i,
                    ]);
                }
            }
        }

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sku' => $request->sku ?: $product->sku,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'price_unit' => $request->price_unit,
            'blackout_rate' => $request->blackout_rate ?: 100,
            'min_area' => $request->min_area ?: 1.0,
            'min_width' => $request->min_width ?: 100,
            'max_width' => $request->max_width ?: 600,
            'min_height' => $request->min_height ?: 100,
            'max_height' => $request->max_height ?: 450,
            'installation_type' => $request->installation_type ?: 'indoor',
            'stock' => $request->stock,
            'material' => $request->material,
            'origin' => $request->origin,
            'description' => $request->description,
            'image' => $imagePath,
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật thông tin mẫu rèm và thư viện ảnh thành công!');
    }

    public function deleteImage($id)
    {
        $img = ProductImage::findOrFail($id);

        if (str_starts_with($img->image_url, '/storage/')) {
            $path = str_replace('/storage/', '', $img->image_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $img->delete();

        return redirect()->back()->with('success', 'Đã xóa ảnh khỏi bộ sưu tập mẫu rèm.');
    }

    public function destroy(Product $product)
    {
        // Xóa ảnh chính
        if ($product->image && str_starts_with($product->image, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $product->image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Xóa tất cả ảnh gallery
        foreach ($product->images as $img) {
            if (str_starts_with($img->image_url, '/storage/')) {
                $path = str_replace('/storage/', '', $img->image_url);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm rèm cửa thành công!');
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->is_active = !$product->is_active;
        $product->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái hiển thị của mẫu rèm.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
        ]);

        $ids = $request->ids;
        $count = count($ids);

        switch ($request->action) {
            case 'activate':
                Product::whereIn('id', $ids)->update(['is_active' => true]);
                $msg = "Đã kích hoạt hiển thị {$count} mẫu rèm.";
                break;
            case 'deactivate':
                Product::whereIn('id', $ids)->update(['is_active' => false]);
                $msg = "Đã ẩn {$count} mẫu rèm.";
                break;
            case 'delete':
                $products = Product::whereIn('id', $ids)->get();
                foreach ($products as $p) {
                    $p->images()->delete();
                    $p->delete();
                }
                $msg = "Đã xóa {$count} mẫu rèm thành công.";
                break;
        }

        return redirect()->back()->with('success', $msg);
    }
}
