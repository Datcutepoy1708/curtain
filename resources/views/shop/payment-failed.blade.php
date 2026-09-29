<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giao Dịch Chưa Hoàn Tất | CurtainLux</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <style>
        .failed-card { max-width: 680px; margin: 40px auto; background: #fff; border-radius: 16px; border: 1px solid #fee2e2; padding: 40px 32px; text-align: center; box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.08); }
        .failed-icon { width: 80px; height: 80px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 38px; margin: 0 auto 20px; box-shadow: 0 0 0 8px rgba(220, 38, 38, 0.12); }
        .reason-box { background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 18px 24px; text-align: left; margin: 24px 0; font-size: 14px; line-height: 1.6; }
        .switch-method-box { background: #f8fafc; border: 1px solid var(--border-line); border-radius: 12px; padding: 20px; margin: 24px 0; text-align: left; }
    </style>
</head>
<body>
    @include('shop.partials.header')

    <div class="container" style="padding-top: 20px;">
        <div class="failed-card">
            <div class="failed-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h1 style="font-size: 24px; font-weight: 800; color: #b91c1c; margin-bottom: 8px;">
                Thanh Toán Không Thành Công
            </h1>
            <p style="font-size: 14px; color: var(--text-muted);">
                Đơn đặt may rèm <strong>{{ $order->order_code }}</strong> của bạn chưa được thanh toán thành công qua cổng trực tuyến.
            </p>

            <div class="reason-box">
                <div style="font-weight: 700; color: #991b1b; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-circle-xmark"></i> Lý do phản hồi từ cổng VNPAY (Mã lỗi: {{ $errorCode }}):
                </div>
                <div style="color: #7f1d1d;">{{ $errorMessage }}</div>
            </div>

            <!-- Alternative Actions -->
            <div class="switch-method-box">
                <div style="font-weight: 700; font-size: 14px; color: var(--text-main); margin-bottom: 12px;">
                    <i class="fa-solid fa-rotate-left" style="color: var(--accent-cta);"></i> Bạn có thể chọn phương án tiếp theo:
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Retry VNPAY -->
                    <a href="{{ route('payment.vnpay', ['orderCode' => $order->order_code]) }}" 
                       style="background: #005baa; color: #fff; padding: 12px 18px; border-radius: 8px; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px;">
                        <i class="fa-solid fa-rotate-right"></i> Thử Thanh Toán Lại Qua VNPAY
                    </a>

                    @php
                        $bankTransferEnabled = \App\Models\Setting::get('enable_bank_transfer', '1') !== '0';
                    @endphp

                    @if($bankTransferEnabled)
                        <!-- Switch to VietQR -->
                        <form action="{{ route('payment.change-method', ['orderCode' => $order->order_code]) }}" method="POST" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="payment_method" value="bank_transfer">
                            <button type="submit" style="width: 100%; background: #2563eb; color: #fff; border: none; padding: 12px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px;">
                                <i class="fa-solid fa-qrcode"></i> Đổi Sang Chuyển Khoản Ngân Hàng (VietQR)
                            </button>
                        </form>
                    @endif

                    <!-- Switch to COD -->
                    <form action="{{ route('payment.change-method', ['orderCode' => $order->order_code]) }}" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="payment_method" value="cod">
                        <button type="submit" style="width: 100%; background: #fff; color: var(--text-main); border: 1px solid var(--border-line); padding: 12px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px;">
                            <i class="fa-solid fa-hand-holding-dollar" style="color: var(--accent-cta);"></i> Đổi Sang Thanh Toán Khi Nhận Hàng & Lắp Đặt (COD)
                        </button>
                    </form>
                </div>
            </div>

            <div style="font-size: 12px; color: var(--text-muted);">
                Cần trợ giúp ngay? Hotline hỗ trợ xưởng may: <strong style="color: var(--accent-cta);">0912.345.678</strong>
            </div>
        </div>
    </div>

    @include('shop.partials.footer')
</body>
</html>
