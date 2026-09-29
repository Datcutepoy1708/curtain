<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\CurtainOptionGroup;
use App\Models\CurtainOptionValue;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    private function getSessionId()
    {
        if (!session()->has('cart_session_id')) {
            session()->put('cart_session_id', Str::uuid()->toString());
        }
        return session()->get('cart_session_id');
    }

    public function index()
    {
        $sessionId = $this->getSessionId();
        $cartItems = CartItem::where('session_id', $sessionId)
            ->with('product.category')
            ->latest()
            ->get();

        $totalAmount = $cartItems->sum('subtotal');

        return view('shop.cart', compact('cartItems', 'totalAmount'));
    }

    public function add(Request $request)
    {
        // 1. Kiểm tra sản phẩm tồn tại và đang kích hoạt
        $product = Product::where('id', $request->input('product_id'))
            ->where('is_active', true)
            ->with('category')
            ->firstOrFail();

        // Lấy giới hạn may đo theo từng sản phẩm
        $minWidth = (float) ($product->min_width ?: 30);
        $maxWidth = (float) ($product->max_width ?: 1000);
        $minHeight = (float) ($product->min_height ?: 30);
        $maxHeight = (float) ($product->max_height ?: 600);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'room_label' => 'nullable|string|max:100',
            'width' => "required|numeric|min:{$minWidth}|max:{$maxWidth}",
            'height' => "required|numeric|min:{$minHeight}|max:{$maxHeight}",
            'mount_type' => 'required|in:inside,outside',
            'options' => 'nullable|array',
            'quantity' => 'nullable|integer|min:1|max:100',
        ], [
            'width.min' => "Chiều rộng rèm tối thiểu cho sản phẩm này là {$minWidth} cm.",
            'width.max' => "Chiều rộng rèm tối đa cho sản phẩm này là {$maxWidth} cm.",
            'height.min' => "Chiều cao rèm tối thiểu cho sản phẩm này là {$minHeight} cm.",
            'height.max' => "Chiều cao rèm tối đa cho sản phẩm này là {$maxHeight} cm.",
        ]);

        // 2. Kiểm tra nhóm tùy chọn bắt buộc (is_required)
        $applicableGroups = CurtainOptionGroup::where('is_active', true)
            ->where(function ($q) use ($product) {
                $q->where('applies_to', 'all');
                if (str_contains($product->category->slug ?? '', 'vai')) {
                    $q->orWhere('applies_to', 'fabric');
                }
            })
            ->get();

        $submittedOptions = $request->input('options', []);
        if (!is_array($submittedOptions)) {
            $submittedOptions = [];
        }

        foreach ($applicableGroups as $group) {
            if ($group->is_required) {
                $valId = $submittedOptions[$group->code] ?? null;
                if (!$valId) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => "Vui lòng chọn tùy chọn bắt buộc: {$group->name}",
                        ], 422);
                    }
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['options' => "Vui lòng chọn tùy chọn bắt buộc: {$group->name}"]);
                }
            }
        }

        // 3. Kiểm tra toàn bộ option values gửi lên phải hợp lệ & đang kích hoạt
        $selectedValIds = array_values(array_filter($submittedOptions));
        $validOptionValues = collect();
        if (!empty($selectedValIds)) {
            $validOptionValues = CurtainOptionValue::whereIn('id', $selectedValIds)
                ->where('is_active', true)
                ->whereHas('group', function ($q) {
                    $q->where('is_active', true);
                })
                ->with('group')
                ->get();

            if ($validOptionValues->count() !== count($selectedValIds)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Một số tùy chọn phụ kiện không hợp lệ hoặc đã ngừng hoạt động.',
                    ], 422);
                }
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['options' => 'Một số tùy chọn phụ kiện không hợp lệ hoặc đã ngừng hoạt động.']);
            }
        }

        $width = floatval($validated['width']);   // cm
        $height = floatval($validated['height']); // cm
        $quantity = intval($validated['quantity'] ?? 1);
        $widthMeters = $width / 100.0;
        $heightMeters = $height / 100.0;
        $area = $widthMeters * $heightMeters;

        // Tính units tính tiền theo đơn vị sản phẩm
        $calculatedUnits = 1.0;
        if ($product->price_unit === 'sqm') {
            $calculatedUnits = max($area, floatval($product->min_area ?? 1.0));
        } elseif ($product->price_unit === 'meter') {
            $calculatedUnits = max($widthMeters, 1.0);
        } else {
            $calculatedUnits = 1.0;
        }

        // KIỂM TRA TỒN KHO: Chặn đặt vượt quá tồn kho thực tế của xưởng
        $stockNeeded = $product->calculateStockNeeded($quantity, $width, $calculatedUnits);
        if ($product->stock < $stockNeeded) {
            $msg = "Rất tiếc! Mẫu rèm '{$product->name}' hiện chỉ còn {$product->stock} {$product->stock_unit_label}. Số lượng bạn chọn cần {$stockNeeded} {$product->stock_unit_label}, không đủ tồn kho khả dụng tại xưởng.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'available_stock' => $product->stock,
                    'stock_unit' => $product->stock_unit_label,
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantity' => $msg]);
        }

        $baseUnitPrice = $product->effective_price;
        $baseTotal = $calculatedUnits * $baseUnitPrice;

        // Tính phụ phí từ options đã chọn (xác thực từ DB)
        $selectedOptionsData = [];
        $extraOptionsCost = 0;

        foreach ($validOptionValues as $opt) {
            $cost = 0;
            if ($opt->price_impact_type === 'per_meter') {
                $cost = $opt->extra_price * $widthMeters;
            } elseif ($opt->price_impact_type === 'per_sqm') {
                $cost = $opt->extra_price * $calculatedUnits;
            } else {
                $cost = $opt->extra_price; // fixed
            }

            $extraOptionsCost += $cost;
            $selectedOptionsData[$opt->group->code] = [
                'group_name' => $opt->group->name,
                'option_id' => $opt->id,
                'name' => $opt->name,
                'extra_price' => $opt->extra_price,
                'impact_type' => $opt->price_impact_type,
                'calculated_cost' => $cost,
            ];
        }

        $itemSubtotal = ($baseTotal + $extraOptionsCost) * $quantity;

        CartItem::create([
            'session_id' => $this->getSessionId(),
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'room_label' => $validated['room_label'] ?: 'Ô cửa tiêu chuẩn',
            'width' => $width,
            'height' => $height,
            'mount_type' => $validated['mount_type'],
            'calculated_units' => round($calculatedUnits, 2),
            'unit_price' => $baseUnitPrice,
            'selected_options' => $selectedOptionsData,
            'subtotal' => $itemSubtotal,
            'quantity' => $quantity,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã thêm rèm cho '{$validated['room_label']}' vào giỏ hàng!",
                'cart_count' => CartItem::where('session_id', $this->getSessionId())->count(),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm rèm tùy biến vào giỏ hàng thành công!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $sessionId = $this->getSessionId();
        $cartItem = CartItem::where('id', $id)
            ->where('session_id', $sessionId)
            ->with('product')
            ->firstOrFail();

        $newQty = (int) $validated['quantity'];
        $product = $cartItem->product;

        // Kiểm tra tồn kho theo đơn vị tính rèm
        $stockNeeded = $product->calculateStockNeeded($newQty, $cartItem->width, $cartItem->calculated_units);
        if ($product->stock < $stockNeeded) {
            $msg = "Mẫu '{$product->name}' hiện chỉ còn {$product->stock} {$product->stock_unit_label}. Không thể tăng lên {$newQty} bộ (yêu cầu {$stockNeeded} {$product->stock_unit_label}).";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'current_quantity' => $cartItem->quantity,
                    'available_stock' => $product->stock,
                    'stock_unit' => $product->stock_unit_label,
                ], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Tính lại thành tiền của ô cửa rèm này
        $unitPriceWithOpts = $cartItem->quantity > 0 ? ($cartItem->subtotal / $cartItem->quantity) : $cartItem->unit_price;
        $cartItem->quantity = $newQty;
        $cartItem->subtotal = round($unitPriceWithOpts * $newQty);
        $cartItem->save();

        $allCartItems = CartItem::where('session_id', $sessionId)->get();
        $totalAmount = $allCartItems->sum('subtotal');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật số lượng thành {$newQty} bộ.",
                'item_id' => $cartItem->id,
                'quantity' => $cartItem->quantity,
                'subtotal' => $cartItem->subtotal,
                'subtotal_formatted' => number_format($cartItem->subtotal, 0, ',', '.') . ' ₫',
                'total_amount' => $totalAmount,
                'total_amount_formatted' => number_format($totalAmount, 0, ',', '.') . ' ₫',
                'cart_count' => $allCartItems->count(),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã cập nhật số lượng bộ rèm.');
    }

    public function remove($id)
    {
        $sessionId = $this->getSessionId();
        CartItem::where('id', $id)->where('session_id', $sessionId)->delete();

        return redirect()->route('cart.index')->with('success', 'Đã xóa bộ rèm khỏi giỏ hàng.');
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:150',
            'shipping_address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'required|in:cod,bank_transfer,vnpay',
        ], [
            'customer_name.required' => 'Vui lòng nhập họ và tên người nhận.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng và lắp đặt.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán được chọn không hợp lệ.',
        ]);

        // Kiểm tra cài đặt hệ thống: Bật/Tắt thanh toán ngân hàng
        $bankTransferEnabled = Setting::get('enable_bank_transfer', '1') !== '0';
        if ($validated['payment_method'] === 'bank_transfer' && !$bankTransferEnabled) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['payment_method' => 'Phương thức chuyển khoản ngân hàng hiện đang bảo trì. Vui lòng chọn phương thức thanh toán tiền mặt khi nhận hàng (COD).']);
        }

        $sessionId = $this->getSessionId();
        $cartItems = CartItem::where('session_id', $sessionId)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống, không thể tiến hành đặt may.');
        }

        // Chặn mua vượt tồn: Kiểm tra toàn bộ sản phẩm trong giỏ hàng trước khi trừ tồn
        foreach ($cartItems as $item) {
            $product = $item->product;
            $needed = $product->calculateStockNeeded($item->quantity, $item->width, $item->calculated_units);
            if ($product->stock < $needed) {
                return redirect()->route('cart.index')->with('error', "Mẫu rèm '{$product->name}' ({$item->room_label}) chỉ còn {$product->stock} {$product->stock_unit_label}. Bạn yêu cầu {$needed} {$product->stock_unit_label}, vui lòng điều chỉnh lại số lượng.");
            }
        }

        $totalAmount = $cartItems->sum('subtotal');

        $order = DB::transaction(function () use ($validated, $cartItems, $totalAmount, $sessionId) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'shipping_address' => $validated['shipping_address'],
                'city' => 'Hồ Chí Minh',
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_gateway' => $validated['payment_method'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'stock_restored' => false,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($cartItems as $item) {
                $stockToDeduct = $item->product->calculateStockNeeded($item->quantity, $item->width, $item->calculated_units);

                // Khấu trừ tồn kho ngay khi tạo đơn hàng
                $item->product->decrement('stock', $stockToDeduct);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'Rèm may đo cao cấp',
                    'room_label' => $item->room_label,
                    'width' => $item->width,
                    'height' => $item->height,
                    'mount_type' => $item->mount_type,
                    'calculated_units' => $item->calculated_units,
                    'unit_price' => $item->unit_price,
                    'selected_options' => $item->selected_options,
                    'quantity' => $item->quantity,
                    'stock_deducted' => $stockToDeduct,
                    'subtotal' => $item->subtotal,
                ]);
            }

            // Xóa rèm trong giỏ hàng sau khi đặt thành công
            CartItem::where('session_id', $sessionId)->delete();

            return $order;
        });

        // Gửi thư cảm ơn & xác nhận đơn hàng qua email (nếu khách hàng nhập email)
        if (!empty($order->customer_email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($order->customer_email)
                    ->send(new \App\Mail\OrderThankYouMail($order));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Không thể gửi thư cảm ơn qua email: ' . $e->getMessage());
            }
        }

        // Nếu chọn thanh toán VNPAY -> Chuyển hướng tới Cổng VNPAY Sandbox
        if ($order->payment_method === 'vnpay') {
            return redirect()->route('payment.vnpay', ['orderCode' => $order->order_code]);
        }

        $flashMsg = ($order->payment_method === 'bank_transfer')
            ? 'Vui lòng hoàn tất chuyển khoản VietQR để xưởng may CurtainLux tiến hành đo may!'
            : 'Đặt may rèm thành công! Kỹ thuật viên CurtainLux sẽ liên hệ khảo sát trong 15 phút.';

        return redirect()->route('order.success', ['orderCode' => $order->order_code])
            ->with('success', $flashMsg);
    }

    public function orderSuccess($orderCode)
    {
        $order = Order::where('order_code', strtoupper(trim($orderCode)))
            ->with(['items.product'])
            ->firstOrFail();

        $bankId = \App\Models\Setting::get('bank_id') ?: env('BANK_ID', 'MB');
        $accountNo = \App\Models\Setting::get('bank_account_number') ?: env('BANK_ACCOUNT_NO', '0043817082005');
        $accountName = \App\Models\Setting::get('bank_account_name') ?: env('BANK_ACCOUNT_NAME', 'NGUYEN TIEN DAT');
        $bankName = ($bankId === 'MB') ? 'Ngân hàng Quân Đội (MBBank)' : "Ngân hàng {$bankId}";
        $amount = (int) $order->total_amount;
        $memo = $order->order_code;

        $qrCodeUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?" . http_build_query([
            'amount' => $amount,
            'addInfo' => $memo,
            'accountName' => $accountName,
        ]);

        $vietQrData = [
            'bank_id' => $bankId,
            'bank_name' => $bankName,
            'account_no' => $accountNo,
            'account_name' => $accountName,
            'amount' => $amount,
            'memo' => $memo,
            'qr_code_url' => $qrCodeUrl,
        ];

        return view('shop.order-success', compact('order', 'vietQrData'));
    }
}
