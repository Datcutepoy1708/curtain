<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'     => 'required|exists:products,id',
            'rating'         => 'required|integer|min:1|max:5',
            'customer_name'  => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'comment'        => 'required|string|min:5|max:1500',
        ], [
            'rating.required'        => 'Vui lòng chọn số sao đánh giá.',
            'customer_name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'comment.required'       => 'Vui lòng nhập nội dung đánh giá/nhận xét.',
            'comment.min'            => 'Nhận xét phải có ít nhất 5 ký tự.',
        ]);

        if (!Auth::check()) {
            $msg = 'Vui lòng đăng nhập tài khoản đã đặt may để gửi đánh giá xác thực cho sản phẩm này.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $msg], 401);
            }
            return back()->with('error', $msg);
        }

        // KIỂM TRA ĐIỀU KIỆN ĐÃ MUA HÀNG: Chỉ khách hàng có đơn hàng chứa sản phẩm này mới được đánh giá
        $user = Auth::user();
        $hasPurchased = \App\Models\Order::where('user_id', $user->id)
            ->whereIn('order_status', ['confirmed', 'manufacturing', 'shipping', 'installed', 'completed'])
            ->whereHas('items', function ($q) use ($validated) {
                $q->where('product_id', $validated['product_id']);
            })
            ->latest()
            ->first();

        // Nếu admin hoặc khách đã mua -> Hợp lệ
        if (!$hasPurchased && !$user->isAdmin()) {
            $msg = 'Chỉ khách hàng đã đặt may mẫu rèm này tại CurtainLux mới có thể viết đánh giá xác thực. Quý khách vui lòng đặt hàng và trải nghiệm sản phẩm trước khi gửi nhận xét.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        $review = Review::create([
            'product_id'        => $validated['product_id'],
            'user_id'           => $user->id,
            'order_id'          => $hasPurchased?->id,
            'customer_name'     => $user->name ?: trim($validated['customer_name']),
            'customer_phone'    => $user->phone ?: trim($validated['customer_phone'] ?? ''),
            'rating'            => (int) $validated['rating'],
            'comment'           => trim($validated['comment']),
            'is_verified_buyer' => true,
            'status'            => 'approved', // Hiển thị ngay
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Cảm ơn bạn đã gửi đánh giá! Nhận xét xác thực của bạn đã được hiển thị trên sản phẩm.',
                'review'  => $review,
            ]);
        }

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá! Nhận xét xác thực của bạn đã được hiển thị trên sản phẩm.');
    }
}
