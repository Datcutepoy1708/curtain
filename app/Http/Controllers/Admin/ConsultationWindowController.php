<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationWindow;
use App\Models\Product;
use Illuminate\Http\Request;

class ConsultationWindowController extends Controller
{
    public function store(Request $request, $consultationId)
    {
        $consultation = Consultation::findOrFail($consultationId);

        $validated = $request->validate([
            'room_name' => 'required|string|max:150',
            'product_id' => 'required|exists:products,id',
            'width' => 'required|numeric|min:20|max:2000',
            'height' => 'required|numeric|min:20|max:2000',
            'install_type' => 'required|in:inside,outside',
            'fabric_color' => 'nullable|string|max:100',
            'has_sheer' => 'nullable|boolean',
            'sewing_style' => 'required|in:wave,pleat,eyelet,roman',
            'motor_type' => 'required|in:manual,smart_wifi,somfy',
            'quantity' => 'required|integer|min:1|max:50',
            'notes' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:4096',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('survey_windows', 'public');
        }

        $window = new ConsultationWindow();
        $window->consultation_id = $consultation->id;
        $window->room_name = $validated['room_name'];
        $window->product_id = $product->id;
        $window->product_name = $product->name;
        $window->width = $validated['width'];
        $window->height = $validated['height'];
        $window->install_type = $validated['install_type'];
        $window->fabric_color = $validated['fabric_color'] ?? 'Theo catalogue tiêu chuẩn';
        $window->has_sheer = $request->has('has_sheer') && $request->has_sheer == '1';
        $window->sewing_style = $validated['sewing_style'];
        $window->motor_type = $validated['motor_type'];
        $window->quantity = $validated['quantity'];
        $window->notes = $validated['notes'] ?? null;
        if ($photoPath) {
            $window->photo_path = $photoPath;
        }

        // Tính giá dự toán
        $pricing = $window->calculatePricing();
        $window->estimated_price = $pricing['subtotal'];
        $window->save();

        // Cập nhật trạng thái cuộc hẹn nếu đang ở bước trước
        if (in_array($consultation->status, ['pending', 'assigned'])) {
            $consultation->update(['status' => 'surveying']);
        }

        return redirect()->back()->with('success', "Đã lưu số đo thực tế cho ô cửa [{$window->room_name}] thành công!");
    }

    public function update(Request $request, $id)
    {
        $window = ConsultationWindow::findOrFail($id);

        $validated = $request->validate([
            'room_name' => 'required|string|max:150',
            'product_id' => 'required|exists:products,id',
            'width' => 'required|numeric|min:20|max:2000',
            'height' => 'required|numeric|min:20|max:2000',
            'install_type' => 'required|in:inside,outside',
            'fabric_color' => 'nullable|string|max:100',
            'has_sheer' => 'nullable|boolean',
            'sewing_style' => 'required|in:wave,pleat,eyelet,roman',
            'motor_type' => 'required|in:manual,smart_wifi,somfy',
            'quantity' => 'required|integer|min:1|max:50',
            'notes' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:4096',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($request->hasFile('photo')) {
            $window->photo_path = $request->file('photo')->store('survey_windows', 'public');
        }

        $window->room_name = $validated['room_name'];
        $window->product_id = $product->id;
        $window->product_name = $product->name;
        $window->width = $validated['width'];
        $window->height = $validated['height'];
        $window->install_type = $validated['install_type'];
        $window->fabric_color = $validated['fabric_color'] ?? $window->fabric_color;
        $window->has_sheer = $request->has('has_sheer') && $request->has_sheer == '1';
        $window->sewing_style = $validated['sewing_style'];
        $window->motor_type = $validated['motor_type'];
        $window->quantity = $validated['quantity'];
        $window->notes = $validated['notes'] ?? null;

        $pricing = $window->calculatePricing();
        $window->estimated_price = $pricing['subtotal'];
        $window->save();

        return redirect()->back()->with('success', "Đã cập nhật thông số ô cửa [{$window->room_name}] thành công!");
    }

    public function destroy($id)
    {
        $window = ConsultationWindow::findOrFail($id);
        $name = $window->room_name;
        $window->delete();

        return redirect()->back()->with('success', "Đã xóa ô cửa [{$name}] khỏi danh sách khảo sát.");
    }
}
