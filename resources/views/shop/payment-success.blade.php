<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Trực Tuyến Thành Công | CurtainLux</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <style>
        .success-card { max-width: 680px; margin: 40px auto; background: #fff; border-radius: 16px; border: 1px solid var(--border-line); padding: 40px 32px; text-align: center; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
        .success-icon { width: 80px; height: 80px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 38px; margin: 0 auto 20px; box-shadow: 0 0 0 8px rgba(34, 197, 94, 0.15); }
        .receipt-box { background: #f8fafc; border: 1px dashed var(--border-line); border-radius: 12px; padding: 20px; text-align: left; margin: 24px 0; font-size: 14px; line-height: 2; }
        .receipt-row { display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding: 4px 0; }
        .btn-group { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 24px; }
    </style>
</head>
<body>
    @include('shop.partials.header')

    <div class="container" style="padding-top: 20px;">
        <div class="success-card">
            <div class="success-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: #15803d; margin-bottom: 8px;">
                Thanh Toán Trực Tuyến Thành Công!
            </h1>
            <p style="font-size: 14px; color: var(--text-muted);">
                Giao dịch của quý khách đã được Cổng thanh toán <strong>VNPAY</strong> xác thực thành công. Xưởng may CurtainLux đã bắt đầu đưa đơn hàng vào công đoạn đo may.
            </p>

            <div class="receipt-box">
                <div class="receipt-row">
                    <span style="color: var(--text-muted);">Mã đơn hàng:</span>
                    <strong style="color: var(--primary, #b8935c); font-size: 15px;">{{ $order->order_code }}</strong>
                </div>
                <div class="receipt-row">
                    <span style="color: var(--text-muted);">Mã chuẩn chi VNPAY:</span>
                    <strong style="color: #0284c7;">{{ $order->transaction_id ?: 'VNP' . date('YmdHis') }}</strong>
                </div>
                <div class="receipt-row">
                    <span style="color: var(--text-muted);">Số tiền đã thanh toán:</span>
                    <strong style="color: #16a34a; font-size: 16px;">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</strong>
                </div>
                <div class="receipt-row">
                    <span style="color: var(--text-muted);">Phương thức:</span>
                    <span>Cổng Thanh Toán Trực Tuyến VNPAY (Sandbox)</span>
                </div>
                <div class="receipt-row">
                    <span style="color: var(--text-muted);">Thời gian giao dịch:</span>
                    <span>{{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i:s') : date('d/m/Y H:i:s') }}</span>
                </div>
                <div class="receipt-row">
                    <span style="color: var(--text-muted);">Trạng thái đơn:</span>
                    <span style="background: #dcfce7; color: #16a34a; padding: 2px 10px; border-radius: 999px; font-weight: 700; font-size: 12px;">
                        Đã xác nhận & lên lịch may
                    </span>
                </div>
            </div>

            <div class="btn-group">
                <a href="{{ route('order.tracking', ['query' => $order->order_code]) }}" class="btn-book-survey" style="text-decoration: none;">
                    <i class="fa-solid fa-route"></i> Theo Dõi Tiến Độ Đơn Hàng
                </a>
                @auth
                    <a href="{{ route('customer.orders') }}" class="btn-wishlist" style="text-decoration: none;">
                        <i class="fa-solid fa-clock-rotate-left"></i> Lịch Sử Đơn Của Tôi
                    </a>
                @endauth
                <a href="{{ route('shop.index') }}" class="btn-wishlist" style="text-decoration: none;">
                    <i class="fa-solid fa-store"></i> Về Trang Chủ
                </a>
            </div>
        </div>
    </div>

    @include('shop.partials.footer')
</body>
</html>
