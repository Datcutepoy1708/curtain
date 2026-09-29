<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user', 'replier'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($rating = $request->input('rating')) {
            $query->where('rating', $rating);
        }

        $reviews = $query->paginate(15)->withQueryString();

        $totalReviews = Review::count();
        $approvedReviews = Review::where('status', 'approved')->count();
        $pendingReviews = Review::where('status', 'pending')->count();
        $avgRating = Review::avg('rating') ?: 5.0;

        return view('admin.reviews.index', compact(
            'reviews',
            'totalReviews',
            'approvedReviews',
            'pendingReviews',
            'avgRating'
        ));
    }

    public function reply(Request $request, $id)
    {
        $validated = $request->validate([
            'reply_content' => 'required|string|min:2|max:2000',
        ], [
            'reply_content.required' => 'Vui lòng nhập nội dung phản hồi cho khách hàng.',
            'reply_content.min'      => 'Nội dung phản hồi phải có ít nhất 2 ký tự.',
            'reply_content.max'      => 'Nội dung phản hồi không được vượt quá 2000 ký tự.',
        ]);

        $review = Review::findOrFail($id);
        $review->reply_content = trim($validated['reply_content']);
        $review->replied_by = auth()->id();
        $review->replied_at = now();
        $review->status = 'approved'; // Tự động duyệt hiển thị khi đã phản hồi chính thức
        $review->save();

        return redirect()->back()->with('success', 'Đã lưu và xuất bản phản hồi đánh giá thành công.');
    }

    public function deleteReply($id)
    {
        $review = Review::findOrFail($id);
        $review->reply_content = null;
        $review->replied_by = null;
        $review->replied_at = null;
        $review->save();

        return redirect()->back()->with('success', 'Đã xóa phản hồi của đánh giá này.');
    }

    public function toggleStatus($id)
    {
        $review = Review::findOrFail($id);
        $review->status = $review->status === 'approved' ? 'hidden' : 'approved';
        $review->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái hiển thị đánh giá.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Đã xóa đánh giá thành công.');
    }
}
