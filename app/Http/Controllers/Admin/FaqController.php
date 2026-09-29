<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\AuditLog;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('question', 'like', "%{$s}%")
                  ->orWhere('answer', 'like', "%{$s}%");
            });
        }

        $faqs = $query->orderBy('sort_order')->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $categories = Faq::categories();

        return view('admin.faqs.index', compact('faqs', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Faq::categories())),
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $faq = Faq::create($validated);

        AuditLog::record(
            'create',
            'faqs',
            "Thêm câu hỏi FAQ mới: \"{$faq->question}\"",
            (string) $faq->id,
            null,
            $faq->toArray()
        );

        return redirect()->route('admin.faqs.index')->with('success', 'Đã thêm câu hỏi FAQ mới thành công!');
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);
        $old = $faq->toArray();

        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Faq::categories())),
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $faq->update($validated);

        AuditLog::record(
            'update',
            'faqs',
            "Cập nhật nội dung câu hỏi FAQ #{$faq->id}: \"{$faq->question}\"",
            (string) $faq->id,
            $old,
            $faq->toArray()
        );

        return redirect()->route('admin.faqs.index')->with('success', 'Đã cập nhật câu hỏi FAQ thành công!');
    }

    public function toggle($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->is_active = !$faq->is_active;
        $faq->save();

        AuditLog::record(
            'status',
            'faqs',
            ($faq->is_active ? 'Bật hiển thị' : 'Ẩn') . " câu hỏi FAQ #{$faq->id}",
            (string) $faq->id
        );

        return redirect()->route('admin.faqs.index')->with('success', 'Đã cập nhật trạng thái hiển thị FAQ!');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $q = $faq->question;
        $faq->delete();

        AuditLog::record(
            'delete',
            'faqs',
            "Xóa câu hỏi FAQ: \"{$q}\"",
            (string) $id
        );

        return redirect()->route('admin.faqs.index')->with('success', 'Đã xóa câu hỏi FAQ thành công!');
    }
}
