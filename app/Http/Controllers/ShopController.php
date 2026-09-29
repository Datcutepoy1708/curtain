<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->withCount('products')->get();
        
        $query = Product::where('is_active', true)->with(['category', 'images']);

        // Lọc Danh mục
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('material', 'like', "%{$search}%")
                  ->orWhere('origin', 'like', "%{$search}%");
            });
        }

        // Lọc Chất liệu
        if ($request->filled('material')) {
            $query->where('material', 'like', "%{$request->material}%");
        }

        // Lọc Xuất xứ
        if ($request->filled('origin')) {
            $query->where('origin', 'like', "%{$request->origin}%");
        }

        // Lọc khoảng giá
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

        // Sắp xếp
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'best_selling':
                    $query->select('products.*')
                          ->selectSub(function ($q) {
                              $q->from('order_items')
                                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                                ->whereColumn('order_items.product_id', 'products.id')
                                ->where('orders.order_status', '!=', 'cancelled')
                                ->selectRaw('COALESCE(SUM(order_items.quantity), 0)');
                          }, 'total_sold')
                          ->orderByDesc('total_sold');
                    break;
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(9)->withQueryString();

        // Top 4 Sản phẩm bán chạy thật tổng hợp từ bảng order_items
        $bestSellers = Product::bestSellers(4)->with(['category', 'images'])->get();

        $materials = Product::whereNotNull('material')->where('material', '!=', '')->pluck('material')->unique();
        $origins = Product::whereNotNull('origin')->where('origin', '!=', '')->pluck('origin')->unique();

        $userId = auth()->id();
        $sessionId = session()->get('wishlist_session_id') ?: session()->getId();
        $wishlistedProductIds = \App\Models\Wishlist::when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->pluck('product_id')
            ->toArray();

        return view('shop.index', compact('categories', 'products', 'bestSellers', 'materials', 'origins', 'wishlistedProductIds'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->with(['category', 'images', 'approvedReviews.replier'])->firstOrFail();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with('images')
            ->take(4)
            ->get();

        $optionGroups = \App\Models\CurtainOptionGroup::with('values')->orderBy('sort_order')->get();

        $userId = auth()->id();
        $sessionId = session()->get('wishlist_session_id') ?: session()->getId();
        $isWishlisted = \App\Models\Wishlist::when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->where('product_id', $product->id)
            ->exists();

        return view('shop.show', compact('product', 'relatedProducts', 'optionGroups', 'isWishlisted'));
    }
}
