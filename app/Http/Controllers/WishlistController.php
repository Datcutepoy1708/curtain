<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    protected function getSessionId(): string
    {
        if (!session()->has('wishlist_session_id')) {
            session()->put('wishlist_session_id', session()->getId() ?: bin2hex(random_bytes(16)));
        }
        return session()->get('wishlist_session_id');
    }

    public function index()
    {
        $userId = Auth::id();
        $sessionId = $this->getSessionId();

        $query = Wishlist::with(['product.category', 'product.images'])->latest();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $wishlists = $query->paginate(12);

        return view('shop.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = (int) $request->product_id;
        $userId = Auth::id();
        $sessionId = $this->getSessionId();

        if ($userId) {
            $item = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();
        } else {
            $item = Wishlist::where('session_id', $sessionId)->where('product_id', $productId)->first();
        }

        if ($item) {
            $item->delete();
            $status = 'removed';
            $message = 'Đã bỏ sản phẩm khỏi danh sách yêu thích.';
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $productId,
            ]);
            $status = 'added';
            $message = 'Đã thêm mẫu rèm vào danh sách yêu thích!';
        }

        // Calculate new count
        if ($userId) {
            $count = Wishlist::where('user_id', $userId)->count();
        } else {
            $count = Wishlist::where('session_id', $sessionId)->count();
        }

        return response()->json([
            'status' => $status,
            'count' => $count,
            'message' => $message,
            'is_wishlisted' => ($status === 'added'),
        ]);
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $sessionId = $this->getSessionId();

        $query = Wishlist::where('id', $id);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $item = $query->first();
        if ($item) {
            $item->delete();
            return redirect()->back()->with('success', 'Đã xóa mẫu rèm khỏi danh sách yêu thích.');
        }

        return redirect()->back()->with('info', 'Mục yêu thích không tồn tại hoặc đã được xóa.');
    }
}
