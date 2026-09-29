<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Tra cứu trạng thái thanh toán realtime phục vụ Auto-polling JS
     */
    public function checkStatus(string $orderCode): JsonResponse
    {
        $order = Order::where('order_code', strtoupper(trim($orderCode)))->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng tương ứng.',
            ], 404);
        }

        $isPaid = ($order->payment_status === 'paid');

        return response()->json([
            'success' => true,
            'orderCode' => $order->order_code,
            'is_paid' => $isPaid,
            'status' => $isPaid ? 'PAID' : 'PENDING',
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
            'total_amount' => (float) $order->total_amount,
            'updated_at' => $order->updated_at->format('d/m/Y H:i:s'),
        ]);
    }

    /**
     * Giả lập thanh toán SePay / VietQR Sandbox (Tương tự Complexus)
     */
    public function sandboxSimulate(Request $request, string $orderCode): JsonResponse
    {
        $order = Order::where('order_code', strtoupper(trim($orderCode)))->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng.',
            ], 404);
        }

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng này đã được thanh toán trước đó.',
                'orderCode' => $order->order_code,
                'payment_status' => 'paid',
            ]);
        }

        // Cập nhật trạng thái thanh toán thành công
        $order->payment_status = 'paid';
        if ($order->order_status === 'pending') {
            $order->order_status = 'confirmed';
        }
        $order->notes = trim(($order->notes ? $order->notes . "\n" : '') . '[Hệ thống]: Đã đối soát & thanh toán thành công qua SePay Sandbox (' . now()->format('d/m/Y H:i:s') . ')');
        $order->save();

        // Gửi email Thư Tri Ân Khách Hàng khi đã thanh toán thành công
        if (!empty($order->customer_email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($order->customer_email)
                    ->send(new \App\Mail\OrderThankYouMail($order));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Không thể gửi email tri ân sau thanh toán: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Giả lập chuyển khoản SePay thành công! Hệ thống đã tự động đối soát đơn hàng.',
            'orderCode' => $order->order_code,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
            'paid_amount' => (float) $order->total_amount,
            'paid_at' => now()->format('d/m/Y H:i:s'),
        ]);
    }

    /**
     * Trang tra cứu đơn hàng may rèm
     */
    public function tracking(Request $request)
    {
        $keyword = trim($request->input('keyword', ''));
        $orders = collect();

        if (!empty($keyword)) {
            $orders = Order::where('order_code', $keyword)
                ->orWhere('customer_phone', $keyword)
                ->with(['items.product'])
                ->latest()
                ->get();
        }

        return view('shop.order-tracking', compact('keyword', 'orders'));
    }

    /**
     * Webhook nhận thông báo chuyển khoản tự động từ SePay (Ngân hàng)
     */
    public function sepayWebhook(Request $request): JsonResponse
    {
        // 1. Kiểm tra API Key cấu hình từ SePay (nếu có cấu hình trong .env)
        $expectedApiKey = env('SEPAY_WEBHOOK_APIKEY');
        if (!empty($expectedApiKey)) {
            $authHeader = $request->header('Authorization') ?: $request->header('api-key');
            $receivedKey = str_ireplace('Apikey ', '', trim($authHeader ?? ''));
            if (empty($receivedKey)) {
                $receivedKey = $request->input('apiKey') ?: $request->input('apikey');
            }

            if ($receivedKey !== $expectedApiKey) {
                \Illuminate\Support\Facades\Log::warning('SePay Webhook rejected: Invalid API Key', [
                    'received' => $receivedKey,
                    'ip' => $request->ip(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Invalid API Key',
                ], 401);
            }
        }

        // 2. Thu thập dữ liệu giao dịch từ SePay
        $transferType = strtolower($request->input('transferType', 'in'));
        if ($transferType !== 'in') {
            return response()->json([
                'success' => true,
                'message' => 'Ignored outgoing transaction',
            ]);
        }

        $transferAmount = (float) $request->input('transferAmount', 0);
        $content = (string) $request->input('content', '');
        $description = (string) $request->input('description', '');
        $referenceCode = (string) $request->input('referenceCode', '');
        $gateway = (string) $request->input('gateway', 'Bank');
        $accountNumber = (string) $request->input('accountNumber', '');
        $fullText = $content . ' ' . $description;

        // 3. Trích xuất mã đơn hàng (Định dạng: DH-YYYYMMDD-XXXX hoặc DH...)
        $matchedOrder = null;

        // Ưu tiên khớp chính xác mã dạng DH-20260918-ABCD
        if (preg_match('/(DH-\d{8}-[A-Za-z0-9]{4})/i', $fullText, $matches)) {
            $matchedOrder = Order::where('order_code', strtoupper($matches[1]))->first();
        }

        // Nếu khách hàng viết liền không dấu gạch ngang (DH20260918ABCD)
        if (!$matchedOrder && preg_match('/DH\d{8}[A-Za-z0-9]{4}/i', str_replace('-', '', $fullText), $matchesNoDash)) {
            $cleaned = strtoupper($matchesNoDash[0]);
            $reformatted = substr($cleaned, 0, 2) . '-' . substr($cleaned, 2, 8) . '-' . substr($cleaned, 10, 4);
            $matchedOrder = Order::where('order_code', $reformatted)->first();
        }

        // Quét tất cả đơn hàng chưa thanh toán gần nhất
        if (!$matchedOrder) {
            $pendingOrders = Order::whereIn('payment_status', ['unpaid', 'pending'])
                ->latest()
                ->take(50)
                ->get();

            foreach ($pendingOrders as $order) {
                if (stripos($fullText, $order->order_code) !== false || stripos(str_replace('-', '', $fullText), str_replace('-', '', $order->order_code)) !== false) {
                    $matchedOrder = $order;
                    break;
                }
            }
        }

        if (!$matchedOrder) {
            \Illuminate\Support\Facades\Log::info("SePay Webhook: Không tìm thấy đơn hàng tương ứng với nội dung: [{$content}]");
            return response()->json([
                'success' => false,
                'message' => 'No matching order found for this transaction memo',
            ], 200);
        }

        // 4. Nếu đơn hàng đã được đối soát trước đó
        if ($matchedOrder->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'message' => 'Order was already paid previously',
                'orderCode' => $matchedOrder->order_code,
            ]);
        }

        // 5. Cập nhật trạng thái thanh toán thành công
        $oldPaymentStatus = $matchedOrder->payment_status;
        $matchedOrder->payment_status = 'paid';
        if ($matchedOrder->order_status === 'pending') {
            $matchedOrder->order_status = 'confirmed';
        }

        $nowStr = now()->format('d/m/Y H:i:s');
        $noteMsg = "[SePay Webhook]: Nhận " . number_format($transferAmount) . "đ từ STK {$accountNumber} ({$gateway}) lúc {$nowStr}. Mã tham chiếu: {$referenceCode}.";
        $matchedOrder->notes = trim(($matchedOrder->notes ? $matchedOrder->notes . "\n" : '') . $noteMsg);
        $matchedOrder->save();

        // Gửi email Thư Tri Ân Khách Hàng khi đã thanh toán thành công qua SePay
        if (!empty($matchedOrder->customer_email)) {
            try {
                \Illuminate\Support\Facades\Mail::to($matchedOrder->customer_email)
                    ->send(new \App\Mail\OrderThankYouMail($matchedOrder));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Không thể gửi email tri ân SePay webhook: ' . $e->getMessage());
            }
        }

        // 6. Ghi lại nhật ký kiểm toán hệ thống
        if (class_exists(\App\Models\AuditLog::class)) {
            \App\Models\AuditLog::record(
                'orders',
                'payment',
                "Thanh toán SePay tự động thành công cho đơn hàng #{$matchedOrder->order_code}: +" . number_format($transferAmount) . " VNĐ",
                $matchedOrder->order_code,
                ['payment_status' => $oldPaymentStatus],
                ['payment_status' => 'paid', 'amount' => $transferAmount, 'gateway' => $gateway, 'ref' => $referenceCode]
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Order #{$matchedOrder->order_code} marked as paid successfully",
            'orderCode' => $matchedOrder->order_code,
            'amount' => $transferAmount,
        ]);
    }
}
