<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with('author')->latest();

        if ($keyword = $request->input('keyword')) {
            $query->where('title', 'like', "%{$keyword}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $news = $query->paginate(10)->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'thumbnail_url' => 'nullable|string',
            'thumbnail_file' => 'nullable|image|max:5120',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:published,draft',
        ]);

        if ($request->hasFile('thumbnail_file')) {
            $path = $request->file('thumbnail_file')->store('news', 'public_uploads');
            $validated['thumbnail_url'] = '/uploads/' . $path;
        }

        unset($validated['thumbnail_file']);
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['author_id'] = auth()->id();

        News::create($validated);

        return redirect()->route('admin.news.index')->with('success', 'Đã đăng bài viết cẩm nang rèm thành công.');
    }

    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'thumbnail_url' => 'nullable|string',
            'thumbnail_file' => 'nullable|image|max:5120',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:published,draft',
        ]);

        if ($request->hasFile('thumbnail_file')) {
            $path = $request->file('thumbnail_file')->store('news', 'public_uploads');
            $validated['thumbnail_url'] = '/uploads/' . $path;
        }

        if (empty($validated['thumbnail_url'])) {
            unset($validated['thumbnail_url']);
        }

        unset($validated['thumbnail_file']);
        $news->update($validated);

        return redirect()->route('admin.news.index')->with('success', 'Đã cập nhật bài viết thành công.');
    }

    public function toggle($id)
    {
        $item = News::findOrFail($id);
        $item->status = $item->status === 'published' ? 'draft' : 'published';
        $item->save();

        return redirect()->back()->with('success', 'Đã chuyển trạng thái bài viết.');
    }

    public function destroy($id)
    {
        $item = News::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Đã xóa bài viết thành công.');
    }
}
