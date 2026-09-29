<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->latest()->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
            'link_url' => 'nullable|string',
            'position' => 'required|in:hero,promo,popup',
            'sort_order' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('banners', 'public_uploads');
            $validated['image_url'] = '/uploads/' . $path;
        }

        if (empty($validated['image_url'])) {
            return redirect()->back()->withErrors(['image_url' => 'Vui lòng cung cấp link ảnh hoặc tải tệp ảnh lên.'])->withInput();
        }

        unset($validated['image_file']);
        Banner::create($validated);

        return redirect()->back()->with('success', 'Đã thêm banner quảng cáo mới.');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
            'link_url' => 'nullable|string',
            'position' => 'required|in:hero,promo,popup',
            'sort_order' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('banners', 'public_uploads');
            $validated['image_url'] = '/uploads/' . $path;
        }

        if (empty($validated['image_url']) && empty($banner->image_url)) {
            return redirect()->back()->withErrors(['image_url' => 'Vui lòng cung cấp link ảnh hoặc tải tệp ảnh lên.'])->withInput();
        }

        if (empty($validated['image_url'])) {
            unset($validated['image_url']);
        }

        unset($validated['image_file']);
        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Đã cập nhật banner quảng cáo thành công.');
    }

    public function toggle($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->status = $banner->status === 'active' ? 'inactive' : 'active';
        $banner->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái hiển thị banner.');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return redirect()->back()->with('success', 'Đã xóa banner thành công.');
    }
}
