<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $imagePath = $request->image;

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $imagePath = '/storage/' . $path;
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'image' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục rèm cửa thành công!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $imagePath = $request->image ?: $category->image;

        if ($request->hasFile('image_file')) {
            // Xóa file ảnh cũ nếu tồn tại trong thư mục storage cục bộ
            if ($category->image && str_starts_with($category->image, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $category->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('image_file')->store('categories', 'public');
            $imagePath = '/storage/' . $path;
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'image' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục rèm cửa thành công!');
    }

    public function destroy(Category $category)
    {
        // Xóa file ảnh cũ từ ổ đĩa nếu là file upload cục bộ
        if ($category->image && str_starts_with($category->image, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $category->image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục thành công!');
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái hiển thị của danh mục.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        $ids = $request->ids;
        $count = count($ids);

        switch ($request->action) {
            case 'activate':
                Category::whereIn('id', $ids)->update(['is_active' => true]);
                $msg = "Đã kích hoạt hiển thị {$count} danh mục rèm.";
                break;
            case 'deactivate':
                Category::whereIn('id', $ids)->update(['is_active' => false]);
                $msg = "Đã ẩn {$count} danh mục rèm.";
                break;
            case 'delete':
                Category::whereIn('id', $ids)->delete();
                $msg = "Đã xóa {$count} danh mục rèm thành công.";
                break;
        }

        return redirect()->back()->with('success', $msg);
    }
}
