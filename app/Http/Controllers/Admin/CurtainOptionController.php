<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurtainOptionGroup;
use App\Models\CurtainOptionValue;
use Illuminate\Http\Request;

class CurtainOptionController extends Controller
{
    public function index()
    {
        $groups = CurtainOptionGroup::with('values')->orderBy('sort_order')->get();
        return view('admin.options.index', compact('groups'));
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:curtain_option_groups,code',
            'type' => 'required|in:single_select,multi_select,boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_required'] = $request->has('is_required');
        CurtainOptionGroup::create($validated);

        return redirect()->back()->with('success', 'Đã thêm nhóm tùy chọn rèm mới.');
    }

    public function updateGroup(Request $request, $id)
    {
        $group = CurtainOptionGroup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:curtain_option_groups,code,' . $group->id,
            'applies_to' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_required'] = $request->has('is_required');
        $validated['is_active'] = $request->has('is_active');

        $group->update($validated);

        return redirect()->back()->with('success', 'Đã cập nhật thông tin nhóm tùy chọn.');
    }

    public function destroyGroup($id)
    {
        $group = CurtainOptionGroup::findOrFail($id);
        $group->values()->delete();
        $group->delete();

        return redirect()->back()->with('success', 'Đã xóa nhóm tùy chọn và các phụ kiện liên quan.');
    }

    public function toggleGroupStatus($id)
    {
        $group = CurtainOptionGroup::findOrFail($id);
        $group->is_active = !$group->is_active;
        $group->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái nhóm tùy chọn.');
    }

    public function storeValue(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:curtain_option_groups,id',
            'name' => 'required|string|max:255',
            'surcharge' => 'required|numeric|min:0',
            'price_type' => 'required|in:fixed,per_meter,per_sqm',
            'sort_order' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $validated['is_default'] = $request->has('is_default');
        $validated['is_active'] = $request->has('is_active', true);
        $validated['extra_price'] = $validated['surcharge'];
        $validated['price_impact_type'] = $validated['price_type'];

        CurtainOptionValue::create($validated);

        return redirect()->back()->with('success', 'Đã thêm tùy chọn / phụ kiện mới.');
    }

    public function updateValue(Request $request, $id)
    {
        $value = CurtainOptionValue::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surcharge' => 'required|numeric|min:0',
            'price_type' => 'required|in:fixed,per_meter,per_sqm',
            'sort_order' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $validated['is_default'] = $request->has('is_default');
        $validated['is_active'] = $request->has('is_active');
        $validated['extra_price'] = $validated['surcharge'];
        $validated['price_impact_type'] = $validated['price_type'];

        $value->update($validated);

        return redirect()->back()->with('success', 'Đã cập nhật thông tin tùy chọn phụ kiện.');
    }

    public function toggleValueStatus($id)
    {
        $value = CurtainOptionValue::findOrFail($id);
        $value->is_active = !$value->is_active;
        $value->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái tùy chọn.');
    }

    public function destroyValue($id)
    {
        $value = CurtainOptionValue::findOrFail($id);
        $value->delete();

        return redirect()->back()->with('success', 'Đã xóa tùy chọn thành công.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:curtain_option_values,id',
        ]);

        $ids = $request->ids;
        $count = count($ids);

        switch ($request->action) {
            case 'activate':
                CurtainOptionValue::whereIn('id', $ids)->update(['is_active' => true]);
                $msg = "Đã kích hoạt {$count} tùy chọn phụ kiện thành công.";
                break;
            case 'deactivate':
                CurtainOptionValue::whereIn('id', $ids)->update(['is_active' => false]);
                $msg = "Đã vô hiệu hóa {$count} tùy chọn phụ kiện thành công.";
                break;
            case 'delete':
                CurtainOptionValue::whereIn('id', $ids)->delete();
                $msg = "Đã xóa {$count} tùy chọn phụ kiện thành công.";
                break;
        }

        return redirect()->back()->with('success', $msg);
    }
}
