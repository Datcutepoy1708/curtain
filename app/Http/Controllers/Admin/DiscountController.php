<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $query = DiscountCode::latest();

        if ($keyword = $request->input('keyword')) {
            $query->where('code', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $discounts = $query->paginate(10)->withQueryString();

        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        DiscountCode::create($validated);

        return redirect()->route('admin.discounts.index')->with('success', 'Đã tạo mã giảm giá mới.');
    }

    public function edit($id)
    {
        $discount = DiscountCode::findOrFail($id);
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, $id)
    {
        $discount = DiscountCode::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code,' . $discount->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        $discount->update($validated);

        return redirect()->route('admin.discounts.index')->with('success', "Đã cập nhật mã giảm giá '{$discount->code}' thành công!");
    }

    public function toggle($id)
    {
        $discount = DiscountCode::findOrFail($id);
        $discount->status = $discount->status === 'active' ? 'inactive' : 'active';
        $discount->save();

        return redirect()->back()->with('success', "Đã chuyển trạng thái mã {$discount->code}.");
    }

    public function destroy($id)
    {
        $discount = DiscountCode::findOrFail($id);
        $discount->delete();

        return redirect()->back()->with('success', 'Đã xóa mã giảm giá.');
    }
}
