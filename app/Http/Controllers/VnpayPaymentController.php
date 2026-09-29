<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VnpayPaymentController extends Controller
{
    /**
     * Hiển thị Cổng thanh toán trực tuyến VNPAY Sandbox Simulator
     */
    public function showGateway($orderCode)
    {
        $order = Order::where('order_code', strtoupper(trim($orderCode)))
            ->with('items.product')
            ->firstOrFail();

        $tmnCode = Setting::get('vnpay_tmn_code', 'CURTAINLUX_TEST');

        return view('shop.vnpay-sandbox', [
            'order' => $order,
            'tmnCode' => $tmnCode,
            'amount' => (int) $order->total_amount,
            'orderInfo' => 'Thanh toan don may rem cao cap ' . $order->order_code,
        ]);
    }

    /**
     * Tiếp nhận phản hồi từ cổng thanh toán VNPAY (Callback / IPN)
     */
    public function callback(Request $request)
    {
        $orderCode = $request->input('vnp_TxnRef');
        $responseCode = $request->input('vnp_ResponseCode', '99');
        $transactionNo = $request->input('vnp_TransactionNo') ?: ('VNP' . date('YmdHis') . strtoupper(Str::random(4)));
        $bankCode = $request->input('vnp_BankCode', 'NCB');

        $order = Order::where('order_code', strtoupper(trim($orderCode)))->first();

        if (!$order) {
            return redirect()->route('shop.index')->with('error', 'Không tìm thấy thông tin đơn hàng thanh toán.');
        }

        if ($responseCode === '00') {
            // Giao dịch thành công
            $order->update([
                'payment_status' => 'paid',
                'order_status' => ($order->order_status === 'pending') ? 'confirmed' : $order->order_status,
                'payment_method' => 'vnpay',
                'payment_gateway' => 'vnpay',
                'transaction_id' => $transactionNo,
                'paid_at' => now(),
            ]);

            return redirect()->route('payment.vnpay.success', ['orderCode' => $order->order_code])
                ->with('success', 'Xác thực thanh toán trực tuyến qua VNPAY thành công!');
        }

        // Giao dịch thất bại hoặc bị hủy
        $errorMessages = [
            '24' => 'Giao dịch đã bị khách hàng hủy bỏ trên cổng thanh toán VNPAY.',
            '51' => 'Tài khoản của bạn không đủ số dư để thực hiện giao dịch thanh toán.',
            '11' => 'Đã hết thời gian chờ thanh toán (Timeout).',
            '12' => 'Thẻ / Tài khoản của khách hàng bị khóa hoặc chưa đăng ký Internet Banking.',
            '75' => 'Ngân hàng phát hành thẻ đang trong quá trình bảo trì.',
        ];

        $errorMessage = $errorMessages[$responseCode] ?? "Giao dịch thanh toán trực tuyến không thành công (Mã phản hồi: {$responseCode}).";

        $order->update([
            'payment_status' => 'failed',
        ]);

        return redirect()->route('payment.vnpay.failed', ['orderCode' => $order->order_code])
            ->with([
                'error_code' => $responseCode,
                'error_message' => $errorMessage,
            ]);
    }

    /**
     * Màn hình Thanh toán Trực Tuyến Thành Công
     */
    public function success($orderCode)
    {
        $order = Order::where('order_code', strtoupper(trim($orderCode)))
            ->with(['items.product', 'user'])
            ->firstOrFail();

        return view('shop.payment-success', compact('order'));
    }

    /**
     * Màn hình Thanh toán Thất Bại / Bị Hủy
     */
    public function failed($orderCode)
    {
        $order = Order::where('order_code', strtoupper(trim($orderCode)))
            ->with(['items.product'])
            ->firstOrFail();

        $errorCode = session('error_code', '24');
        $errorMessage = session('error_message', 'Giao dịch thanh toán trực tuyến chưa hoàn tất hoặc bị hủy bỏ.');

        return view('shop.payment-failed', compact('order', 'errorCode', 'errorMessage'));
    }

    /**
     * Đổi phương thức thanh toán sang COD hoặc VietQR từ màn hình lỗi
     */
    public function changeMethod(Request $request, $orderCode)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cod,bank_transfer',
        ]);

        $order = Order::where('order_code', strtoupper(trim($orderCode)))->firstOrFail();

        $order->update([
            'payment_method' => $validated['payment_method'],
            'payment_gateway' => $validated['payment_method'],
            'payment_status' => 'pending',
        ]);

        if ($validated['payment_method'] === 'bank_transfer') {
            return redirect()->route('order.success', $order->order_code)
                ->with('success', 'Đã chuyển sang phương thức Chuyển khoản VietQR MBBank.');
        }

        return redirect()->route('order.success', $order->order_code)
            ->with('success', 'Đã chuyển sang hình thức thanh toán khi nhận hàng & lắp đặt (COD).');
    }
}
