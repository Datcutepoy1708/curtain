<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
    /**
     * Tạo báo giá mới hoặc tạo phiên bản tiếp theo (v1, v2...) từ các ô cửa đã đo
     */
    public function generate(Request $request, $consultationId)
    {
        $request->validate([
            'discount_amount' => 'nullable|integer|min:0',
            'installation_fee' => 'nullable|integer|min:0',
            'deposit_percent' => 'nullable|integer|min:10|max:100',
            'valid_days' => 'nullable|integer|min:1|max:90',
            'delivery_days' => 'nullable|integer|min:1|max:180',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        $consultation = Consultation::with('windows.product')->findOrFail($consultationId);

        if ($consultation->windows->isEmpty()) {
            return redirect()->back()->with('error', 'Chưa có ô cửa đo đạc nào! Vui lòng thêm ít nhất một ô cửa trước khi lập báo giá.');
        }

        $latestQuotation = Quotation::where('consultation_id', $consultation->id)->orderBy('version', 'desc')->first();
        if ($latestQuotation && in_array($latestQuotation->status, ['draft', 'accepted', 'converted'])) {
            return redirect()->back()->with('error', 'Hãy hoàn tất bản nháp hiện tại trước khi tạo phiên bản mới.');
        }
        $newVersion = $latestQuotation ? ($latestQuotation->version + 1) : 1;

        $discount = (float)$request->input('discount_amount', 0);
        $installFee = (float)$request->input('installation_fee', 0);
        $depositPercent = (int)$request->input('deposit_percent', 30);
        $validDays = (int)$request->input('valid_days', 15);
        $deliveryDays = (int)$request->input('delivery_days', 7);

        DB::beginTransaction();
        try {
            $code = 'BG-' . date('Ymd') . '-' . strtoupper(Str::random(4)) . '-V' . $newVersion;

            $quotation = Quotation::create([
                'quotation_code' => $code,
                'consultation_id' => $consultation->id,
                'version' => $newVersion,
                'status' => 'draft',
                'subtotal' => 0,
                'discount_amount' => $discount,
                'installation_fee' => $installFee,
                'total_amount' => 0,
                'deposit_percent' => $depositPercent,
                'deposit_amount' => 0,
                'valid_until' => now()->addDays($validDays)->toDateString(),
                'estimated_delivery_date' => now()->addDays($deliveryDays)->toDateString(),
                'admin_notes' => $request->input('admin_notes', "Báo giá phiên bản v{$newVersion} lập dựa trên số đo thực tế tại nhà khách hàng."),
            ]);

            $subtotal = 0;

            foreach ($consultation->windows as $window) {
                $pricing = $window->calculatePricing();
                $itemSubtotal = $pricing['subtotal'];
                $subtotal += $itemSubtotal;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'consultation_window_id' => $window->id,
                    'room_name' => $window->room_name,
                    'product_id' => $window->product_id,
                    'product_name' => $window->product_name,
                    'width' => $window->width,
                    'height' => $window->height,
                    'install_type' => $window->install_type,
                    'calculated_units' => $pricing['calculated_units'],
                    'unit_label' => $pricing['unit_label'],
                    'unit_price' => $pricing['unit_price'],
                    'fabric_cost' => $pricing['fabric_cost'],
                    'options_cost' => $pricing['options_cost'],
                    'options_detail' => $pricing['options_detail'],
                    'quantity' => $window->quantity,
                    'subtotal' => $itemSubtotal,
                    'notes' => $window->notes,
                ]);
            }

            $totalAmount = max(0, $subtotal - $discount + $installFee);
            $depositAmount = round(($totalAmount * $depositPercent) / 100);

            $quotation->update([
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'deposit_amount' => $depositAmount,
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Đã lập thành công Bảng Báo Giá chính thức [{$quotation->quotation_code}] (Phiên bản v{$newVersion})!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi lập báo giá: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật thông số chiết khấu, % cọc, ghi chú của báo giá
     */
    public function update(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);
        if ($quotation->status !== 'draft') {
            return redirect()->back()->with('error', 'Báo giá đã gửi không thể sửa trực tiếp. Hãy lập phiên bản mới.');
        }

        $validated = $request->validate([
            'discount_amount' => 'nullable|integer|min:0',
            'installation_fee' => 'nullable|integer|min:0',
            'deposit_percent' => 'required|integer|min:10|max:100',
            'valid_until' => 'nullable|date',
            'estimated_delivery_date' => 'nullable|date',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if (($validated['discount_amount'] ?? 0) > (int) $quotation->subtotal + ($validated['installation_fee'] ?? 0)) {
            return redirect()->back()->with('error', 'Chiết khấu không được lớn hơn tổng giá trị báo giá.');
        }

        $quotation->update($validated);
        $quotation->recalculateTotals();

        return redirect()->back()->with('success', "Đã cập nhật Báo giá [{$quotation->quotation_code}] thành công!");
    }

    /**
     * Gửi báo giá cho khách hàng duyệt (chuyển sang 'sent')
     */
    public function sendToCustomer($id)
    {
        $quotation = Quotation::with('consultation')->findOrFail($id);
        if ($quotation->status !== 'draft') {
            return redirect()->back()->with('error', 'Chỉ có thể gửi bản nháp.');
        }
        $quotation->update(['status' => 'sent']);

        if ($quotation->consultation) {
            $quotation->consultation->update([
                'status' => 'quoted',
                'current_quotation_id' => $quotation->id,
                'quotation_amount' => $quotation->total_amount,
            ]);
        }

        return redirect()->back()->with('success', "Đã công bố báo giá [{$quotation->quotation_code}]. Hãy chia sẻ đường dẫn báo giá cho khách.");
    }

    /**
     * Chuyển đổi báo giá đã duyệt thành Đơn hàng may đo chính thức (Order & OrderItems)
     */
    public function convertToOrder($id)
    {
        $quotation = Quotation::with(['consultation', 'items.product', 'consultation.user'])->findOrFail($id);

        if ($quotation->status !== 'accepted' || $quotation->consultation->current_quotation_id !== $quotation->id) {
            return redirect()->back()->with('error', 'Chỉ báo giá hiện hành đã được khách duyệt mới tạo được đơn hàng.');
        }

        $consultation = $quotation->consultation;

        DB::beginTransaction();
        try {
            $orderCode = 'DH-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $consultation->user_id ?? auth()->id(),
                'customer_name' => $consultation->customer_name,
                'customer_phone' => $consultation->customer_phone,
                'customer_email' => $consultation->customer_email,
                'shipping_address' => $consultation->address . ($consultation->district ? ', ' . $consultation->district : '') . ', ' . $consultation->city,
                'city' => $consultation->city,
                'district' => $consultation->district,
                'total_amount' => $quotation->total_amount,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
                'order_status' => 'confirmed', // Xác nhận chuyển sang may xưởng
                'quotation_id' => $quotation->id,
                'notes' => "Đơn hàng tạo từ Báo giá may đo [{$quotation->quotation_code}] (Lịch khảo sát: {$consultation->code}). Yêu cầu cọc {$quotation->deposit_percent}%: " . number_format($quotation->deposit_amount, 0, ',', '.') . " đ.",
                'stock_restored' => false,
            ]);

            // Sao chép nguyên vẹn từng ô cửa sang order_items
            foreach ($quotation->items as $item) {
                $neededStock = 0;
                if ($item->product) {
                    $neededStock = $item->product->calculateStockNeeded(
                        $item->quantity,
                        $item->width,
                        $item->calculated_units
                    );

                    // Trừ tồn kho xưởng
                    $item->product->decrement('stock', $neededStock);
                }

                $optionsJson = [];
                if (!empty($item->options_detail)) {
                    $optionsJson['details'] = $item->options_detail;
                }
                $optionsJson['sewing_style'] = $item->consultationWindow?->sewing_style_label ?? 'May tiêu chuẩn';
                $optionsJson['motor_type'] = $item->consultationWindow?->motor_type_label ?? 'Ray tiêu chuẩn';
                $optionsJson['fabric_color'] = $item->consultationWindow?->fabric_color ?? 'Tiêu chuẩn';

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'room_name' => $item->room_name,
                    'width' => $item->width,
                    'height' => $item->height,
                    'install_type' => $item->install_type,
                    'calculated_units' => $item->calculated_units,
                    'unit_price' => $item->unit_price,
                    'options_json' => $optionsJson,
                    'subtotal' => $item->subtotal,
                    'stock_deducted' => $neededStock,
                ]);
            }

            // Đánh dấu báo giá đã chuyển đổi và hoàn tất khảo sát
            $quotation->update([
                'status' => 'converted',
                'order_id' => $order->id,
            ]);

            $consultation->update([
                'status' => 'completed',
                'admin_note' => ($consultation->admin_note ? $consultation->admin_note . "\n" : '') . "Đã chuyển thành Đơn may đo #{$order->order_code}.",
            ]);

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)->with('success', "🎉 Đã chuyển đổi Báo giá [{$quotation->quotation_code}] thành Đơn may đo [#{$order->order_code}] thành công! Tồn kho vải tại xưởng đã được khấu trừ chuẩn xác.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi tạo đơn hàng: ' . $e->getMessage());
        }
    }
}
