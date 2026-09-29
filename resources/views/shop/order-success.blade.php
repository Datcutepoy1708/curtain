<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($order->payment_status === 'paid' || $order->payment_method === 'cod')
            Tạo Đơn Hàng Thành Công — {{ $order->order_code }} | CurtainLux
        @else
            Chờ Thanh Toán Chuyển Khoản — {{ $order->order_code }} | CurtainLux
        @endif
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- External Shop CSS -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">

    <style>
        .order-success-wrapper {
            padding: 40px 20px 80px;
            max-width: 860px;
            margin: 0 auto;
        }

        .success-main-card {
            background: #FFFFFF;
            border: 1px solid var(--border-line, #e2e8f0);
            border-radius: 16px;
            padding: 40px 32px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }

        .success-hero-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .success-icon-badge {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            font-size: 32px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .success-icon-badge.state-paid {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #10b981;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.25);
        }

        .success-icon-badge.state-pending {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            color: #0284c7;
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.2);
        }

        .success-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main, #1e293b);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .success-sub {
            font-size: 15px;
            color: var(--text-muted, #64748b);
            line-height: 1.6;
            max-width: 620px;
            margin: 0 auto 20px;
        }

        .order-code-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 8px 20px;
            border-radius: 9999px;
            font-size: 14px;
            color: #475569;
        }

        .order-code-chip strong {
            color: var(--accent-cta, #b8935c);
            font-family: monospace;
            font-size: 17px;
            letter-spacing: 0.5px;
        }

        .btn-copy-chip {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            color: #334155;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-copy-chip:hover {
            background: var(--accent-cta, #b8935c);
            color: #FFFFFF;
            border-color: var(--accent-cta, #b8935c);
        }

        /* PAID BANNER */
        .paid-banner-box {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1.5px solid #22c55e;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 30px;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.15);
            animation: fadeInSlide 0.4s ease;
        }

        .paid-banner-icon {
            width: 44px;
            height: 44px;
            background: #16a34a;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .paid-banner-content h3 {
            font-size: 17px;
            font-weight: 800;
            color: #15803d;
            margin-bottom: 4px;
        }

        .paid-banner-content p {
            font-size: 13.5px;
            color: #166534;
            line-height: 1.5;
            margin: 0;
        }

        /* BANK TRANSFER & VIETQR BOX */
        .bank-box {
            background: #ffffff;
            border: 1.5px solid #bae6fd;
            border-radius: 14px;
            padding: 26px;
            margin-bottom: 30px;
            box-shadow: 0 4px 16px rgba(2, 132, 199, 0.08);
        }

        .bank-box-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #0369a1;
        }

        .bank-box-header h3 {
            font-size: 17px;
            font-weight: 800;
            margin: 0;
        }

        .bank-box-intro {
            font-size: 14px;
            color: var(--text-muted, #64748b);
            margin-bottom: 22px;
            line-height: 1.5;
        }

        .bank-layout-grid {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 26px;
            align-items: center;
            margin-bottom: 22px;
        }

        @media (max-width: 680px) {
            .bank-layout-grid {
                grid-template-columns: 1fr;
                justify-items: center;
            }
        }

        .qr-pane {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .vietqr-image {
            width: 220px;
            height: 220px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .qr-hint-text {
            font-size: 11.5px;
            color: var(--text-muted, #64748b);
            margin-top: 10px;
            line-height: 1.4;
            max-width: 220px;
        }

        .bank-details-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #f8fafc;
            border-radius: 12px;
            padding: 18px 20px;
            border: 1px solid #e2e8f0;
        }

        .bank-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 13.5px;
        }

        .bank-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .bank-label {
            color: #64748b;
            font-weight: 500;
        }

        .bank-value-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bank-value {
            color: #0f172a;
            font-weight: 700;
        }

        .bank-copy-btn {
            background: rgba(184, 147, 92, 0.12);
            border: none;
            color: var(--accent-cta, #b8935c);
            font-size: 12px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .bank-copy-btn:hover {
            background: var(--accent-cta, #b8935c);
            color: #ffffff;
        }

        .bank-ref-highlight {
            color: #0284c7;
            background: #e0f2fe;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 15px;
            letter-spacing: 0.5px;
        }

        /* POLLING STATUS BAR */
        .polling-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 18px;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .polling-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #475569;
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #10b981;
            position: relative;
        }

        .pulse-dot::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            border-radius: 50%;
            background: #10b981;
            animation: pulseRing 1.5s infinite ease-out;
        }

        @keyframes pulseRing {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(2.8); opacity: 0; }
        }

        .btn-check-manual {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-check-manual:hover {
            border-color: var(--accent-cta, #b8935c);
            color: var(--accent-cta, #b8935c);
        }

        /* SANDBOX SIMULATOR BOX (Complexus feature) */
        .sandbox-payment-bar {
            background: #fffbeb;
            border: 1.5px dashed #f59e0b;
            border-radius: 10px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .sandbox-text {
            font-size: 13px;
            color: #92400e;
        }

        .btn-sandbox-simulate {
            background: #f59e0b;
            color: #ffffff;
            border: none;
            padding: 9px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s, transform 0.2s;
        }

        .btn-sandbox-simulate:hover {
            background: #d97706;
            transform: translateY(-1px);
        }

        /* ORDER DETAILS */
        .order-info-section {
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid var(--border-line, #e2e8f0);
            padding: 24px;
            margin-bottom: 28px;
        }

        .order-info-title {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 16px;
            color: var(--text-main, #1e293b);
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-line, #e2e8f0);
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 24px;
            font-size: 13.5px;
            color: #334155;
            margin-bottom: 20px;
        }

        @media (max-width: 640px) {
            .order-info-grid {
                grid-template-columns: 1fr;
            }
        }

        .items-table-mini {
            border-top: 1px solid var(--border-line, #e2e8f0);
            padding-top: 16px;
        }

        .mini-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 13.5px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .mini-item-row:last-child {
            border-bottom: none;
        }

        /* TOAST NOTIFICATION */
        .copy-toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: rgba(15, 23, 42, 0.9);
            color: #ffffff;
            padding: 10px 22px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 99999;
        }

        .copy-toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        @keyframes fadeInSlide {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    @include('shop.partials.header')

    <div class="order-success-wrapper">
        <div class="success-main-card">

            @php
                $isPaidOrCod = ($order->payment_status === 'paid' || $order->payment_method === 'cod');
            @endphp

            <!-- HERO HEADER -->
            <div class="success-hero-header">
                <div id="main-icon-badge" class="success-icon-badge {{ $isPaidOrCod ? 'state-paid' : 'state-pending' }}">
                    <i id="main-icon-el" class="{{ $isPaidOrCod ? 'fa-solid fa-check' : 'fa-solid fa-building-columns' }}"></i>
                </div>

                <h1 id="page-main-title" class="success-title">
                    {{ $isPaidOrCod ? 'Tạo Đơn Hàng May Rèm Thành Công!' : 'Chờ Thanh Toán Chuyển Khoản' }}
                </h1>

                <p id="page-main-sub" class="success-sub">
                    @if($isPaidOrCod)
                        Cảm ơn quý khách <strong>{{ $order->customer_name }}</strong> đã tin tưởng đặt may tại <strong>CurtainLux</strong>.<br>
                        Đơn hàng của bạn đã được tiếp nhận và xưởng may đang chuẩn bị phụ kiện & lên lịch khảo sát.
                    @else
                        Vui lòng mở ứng dụng Ngân hàng bất kỳ và quét mã VietQR bên dưới để hoàn tất chuyển khoản.<br>
                        Ngay sau khi nhận tiền, hệ thống sẽ <strong>tự động đối soát và xác nhận đơn hàng</strong>.
                    @endif
                </p>

                <div class="order-code-chip">
                    <span>Mã đơn may rèm:</span>
                    <strong id="display-order-code">{{ $order->order_code }}</strong>
                    <button type="button" class="btn-copy-chip" onclick="copyText('{{ $order->order_code }}')" title="Sao chép mã đơn">
                        <i class="fa-regular fa-copy"></i> Sao chép
                    </button>
                </div>
            </div>

            <!-- 1. PAID SUCCESS BANNER (Green Checkmark) -->
            <div id="paid-banner" class="paid-banner-box" style="{{ $order->payment_status === 'paid' ? 'display: flex;' : 'display: none;' }}">
                <div class="paid-banner-icon">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="paid-banner-content">
                    <h3 id="paid-banner-title">Đã Nhận Thanh Toán & Tạo Đơn Hàng Thành Công!</h3>
                    <p id="paid-banner-desc">Hệ thống đã tự động đối soát giao dịch chuyển khoản VietQR cho đơn hàng <strong>{{ $order->order_code }}</strong>. Đơn may đo đã được duyệt và chuyển sang bộ phận may xưởng.</p>
                    <div style="margin-top: 8px; font-size: 13px; color: #15803d;">
                        Số tiền đã nhận: <strong id="paid-amount-display">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</strong>
                        <span id="paid-time-display" style="margin-left: 12px;">— Đã thanh toán thành công</span>
                    </div>
                </div>
            </div>

            <!-- 2. BANK TRANSFER INSTRUCTIONS & VIETQR (Complexus System) -->
            @if($order->payment_method === 'bank_transfer')
            <div id="bank-box" class="bank-box" style="{{ $order->payment_status === 'paid' ? 'display: none;' : 'display: block;' }}">
                <div class="bank-box-header">
                    <i class="fa-solid fa-qrcode" style="font-size: 20px;"></i>
                    <h3>Cổng Thanh Toán Chuyển Khoản Tự Động (VietQR)</h3>
                </div>
                <p class="bank-box-intro">
                    Mở ứng dụng ngân hàng và quét mã VietQR bên dưới hoặc nhập chính xác số tài khoản và nội dung chuyển khoản để đơn hàng được kích hoạt ngay:
                </p>

                <div class="bank-layout-grid">
                    <!-- Left: QR Code -->
                    <div class="qr-pane">
                        <img id="vietqr-image" 
                             src="{{ $vietQrData['qr_code_url'] }}" 
                             alt="Mã VietQR Thanh Toán CurtainLux" 
                             class="vietqr-image">
                        <span class="qr-hint-text">
                            <i class="fa-solid fa-camera"></i> Quét mã bằng App ngân hàng bất kỳ (MB, Vietcombank, Techcombank, VPBank...)
                        </span>
                    </div>

                    <!-- Right: Bank Info List -->
                    <div class="bank-details-list">
                        <div class="bank-row">
                            <span class="bank-label">Ngân hàng thụ hưởng:</span>
                            <span class="bank-value">{{ $vietQrData['bank_name'] }}</span>
                        </div>

                        <div class="bank-row">
                            <span class="bank-label">Số tài khoản:</span>
                            <div class="bank-value-group">
                                <span class="bank-value" id="bank-acc-no">{{ $vietQrData['account_no'] }}</span>
                                <button type="button" class="bank-copy-btn" onclick="copyText('{{ $vietQrData['account_no'] }}')">
                                    <i class="fa-regular fa-copy"></i> Sao chép
                                </button>
                            </div>
                        </div>

                        <div class="bank-row">
                            <span class="bank-label">Chủ tài khoản:</span>
                            <span class="bank-value">{{ $vietQrData['account_name'] }}</span>
                        </div>

                        <div class="bank-row">
                            <span class="bank-label">Số tiền chuyển khoản:</span>
                            <div class="bank-value-group">
                                <span class="bank-value" style="color: #dc2626; font-size: 17px;">{{ number_format($vietQrData['amount'], 0, ',', '.') }} ₫</span>
                                <button type="button" class="bank-copy-btn" onclick="copyText('{{ (int)$vietQrData['amount'] }}')">
                                    <i class="fa-regular fa-copy"></i> Sao chép
                                </button>
                            </div>
                        </div>

                        <div class="bank-row">
                            <span class="bank-label">Nội dung chuyển khoản:</span>
                            <div class="bank-value-group">
                                <span class="bank-value bank-ref-highlight" id="bank-ref-text">{{ $vietQrData['memo'] }}</span>
                                <button type="button" class="bank-copy-btn" onclick="copyText('{{ $vietQrData['memo'] }}')">
                                    <i class="fa-regular fa-copy"></i> Sao chép
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auto-polling Realtime Pulse Bar -->
                <div class="polling-bar">
                    <div class="polling-left">
                        <div class="pulse-dot"></div>
                        <span id="polling-status-msg">Hệ thống đang tự động lắng nghe giao dịch chuyển khoản VietQR...</span>
                    </div>
                    <button type="button" id="btn-manual-check" class="btn-check-manual" onclick="checkPaymentStatusManual()">
                        <i class="fa-solid fa-arrows-rotate"></i> Kiểm tra ngay
                    </button>
                </div>

                <!-- Sandbox Simulator Bar (Complexus Feature) -->
                <div class="sandbox-payment-bar">
                    <div class="sandbox-text">
                        <strong><i class="fa-solid fa-flask"></i> Chế độ Thử Nghiệm Sandbox:</strong>
                        <span>Mô phỏng SePay tự động bắn Webhook đối soát khi khách đã chuyển khoản thành công.</span>
                    </div>
                    <button type="button" id="btn-sandbox-simulate" class="btn-sandbox-simulate" onclick="simulateSandboxPayment()">
                        <i class="fa-solid fa-bolt"></i> Giả Lập Chuyển Khoản Thành Công
                    </button>
                </div>
            </div>
            @endif

            <!-- 3. ORDER RECIPIENT & SPECS SUMMARY -->
            <div class="order-info-section">
                <div class="order-info-title">
                    <i class="fa-solid fa-receipt" style="color: var(--accent-cta);"></i>
                    <span>Thông Tin Đơn Hàng & Người Nhận May Rèm</span>
                </div>

                <div class="order-info-grid">
                    <div><strong>Họ và tên:</strong> {{ $order->customer_name }}</div>
                    <div><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</div>
                    <div><strong>Địa chỉ lắp đặt:</strong> {{ $order->shipping_address }}</div>
                    <div><strong>Email:</strong> {{ $order->customer_email ?: 'Chưa cung cấp' }}</div>
                    <div>
                        <strong>Phương thức thanh toán:</strong> 
                        {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng / nghiệm thu lắp đặt' : 'Chuyển khoản Ngân hàng (VietQR)' }}
                    </div>
                    <div>
                        <strong>Trạng thái thanh toán:</strong> 
                        <span id="payment-status-badge" style="font-weight: 700; color: {{ $order->payment_status === 'paid' ? '#10b981' : '#f59e0b' }};">
                            @if($order->payment_status === 'paid')
                                <i class="fa-solid fa-circle-check"></i> Đã thanh toán thành công
                            @else
                                <i class="fa-solid fa-clock"></i> Chờ chuyển khoản
                            @endif
                        </span>
                    </div>
                    @if($order->notes)
                        <div style="grid-column: 1 / -1;">
                            <strong>Ghi chú:</strong> {{ $order->notes }}
                        </div>
                    @endif
                </div>

                <!-- Curtain Items Breakdown -->
                <div class="items-table-mini">
                    <div style="font-weight: 700; font-size: 13.5px; margin-bottom: 10px; color: var(--text-main);">
                        DANH SÁCH Ô CỬA MAY ĐO ({{ $order->items->count() }} bộ):
                    </div>

                    @foreach($order->items as $item)
                        <div class="mini-item-row">
                            <div>
                                <strong style="color: var(--text-main);">{{ $item->product_name }}</strong>
                                <span style="color: var(--accent-cta); font-size: 12px; margin-left: 6px;">
                                    [{{ $item->room_label ?: 'Ô cửa' }}]
                                </span>
                                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                                    Rộng {{ $item->width }}cm &times; Cao {{ $item->height }}cm 
                                    ({{ $item->mount_type === 'inside' ? 'Lọt lòng' : 'Phủ bì' }}) &bull; SL: {{ $item->quantity }}
                                </div>
                            </div>
                            <strong style="color: var(--accent-cta);">
                                {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                            </strong>
                        </div>
                    @endforeach

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px; padding-top: 12px; border-top: 1.5px solid var(--border-line); font-size: 16px; font-weight: 800;">
                        <span>Tổng Chi Phí May Rèm:</span>
                        <span style="color: var(--accent-cta); font-size: 20px;">
                            {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3.5. THƯ TRI ÂN KHÁCH HÀNG (THANK YOU LETTER) -->
            @include('shop.partials.thank-you-letter')

            <!-- 4. ACTION BUTTONS -->
            <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
                <a href="{{ route('shop.index') }}" class="btn-book-survey" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 12px 24px;">
                    <i class="fa-solid fa-store"></i> Tiếp Tục Xem Mẫu Rèm
                </a>
                <a href="{{ route('order.tracking', ['keyword' => $order->order_code]) }}" class="btn-read-more" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 12px 24px; border: 1px solid var(--border-line); border-radius: 8px; color: var(--text-main); font-weight: 700;">
                    <i class="fa-solid fa-truck-fast"></i> Tra Cứu Đơn Hàng
                </a>
            </div>

        </div>
    </div>

    <!-- COPY TOAST NOTIFICATION -->
    <div id="copyToast" class="copy-toast">Đã sao chép vào bộ nhớ tạm!</div>

    <script>
        const orderCode = '{{ $order->order_code }}';
        const isBankTransfer = {{ $order->payment_method === 'bank_transfer' ? 'true' : 'false' }};
        let isPaid = {{ $order->payment_status === 'paid' ? 'true' : 'false' }};
        let pollTimer = null;
        let isChecking = false;

        // Clipboard Copy
        window.copyText = function(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => showToast('Đã sao chép: ' + text));
            } else {
                const temp = document.createElement('textarea');
                temp.value = text;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
                showToast('Đã sao chép: ' + text);
            }
        };

        function showToast(msg) {
            const toast = document.getElementById('copyToast');
            if (!toast) return;
            toast.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2200);
        }

        // Auto-polling every 3 seconds for realtime status
        function startPolling() {
            if (!isBankTransfer || isPaid) return;
            pollTimer = setInterval(() => checkPaymentStatus(false), 3000);
        }

        function checkPaymentStatus(isManual) {
            if (isChecking || isPaid || !orderCode) return;
            isChecking = true;

            const btnManual = document.getElementById('btn-manual-check');
            if (isManual && btnManual) {
                btnManual.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang kiểm tra...';
            }

            fetch('/api/payment/status/' + encodeURIComponent(orderCode), {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(json => {
                isChecking = false;
                if (isManual && btnManual) {
                    btnManual.innerHTML = '<i class="fa-solid fa-arrows-rotate"></i> Kiểm tra ngay';
                }

                if (json.success && json.is_paid) {
                    handlePaymentPaid(json);
                }
            })
            .catch(() => {
                isChecking = false;
                if (isManual && btnManual) {
                    btnManual.innerHTML = '<i class="fa-solid fa-arrows-rotate"></i> Kiểm tra ngay';
                }
            });
        }

        window.checkPaymentStatusManual = function() {
            checkPaymentStatus(true);
        };

        // Realtime Payment Simulator (Complexus Sandbox)
        window.simulateSandboxPayment = function() {
            const btn = document.getElementById('btn-sandbox-simulate');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang giả lập chuyển khoản...';
            }

            fetch('/api/payment/sandbox-simulate/' + encodeURIComponent(orderCode), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(json => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Giả Lập Chuyển Khoản Thành Công';
                }

                if (json.success) {
                    showToast('Giả lập thanh toán SePay thành công!');
                    handlePaymentPaid(json);
                } else {
                    alert(json.message || 'Lỗi khi giả lập thanh toán.');
                }
            })
            .catch(err => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Giả Lập Chuyển Khoản Thành Công';
                }
                alert('Lỗi kết nối máy chủ giả lập.');
            });
        };

        // Handle paid transformation
        function handlePaymentPaid(data) {
            isPaid = true;
            if (pollTimer) clearInterval(pollTimer);

            // 1. Hide the VietQR transfer box
            const bankBox = document.getElementById('bank-box');
            if (bankBox) bankBox.style.display = 'none';

            // 2. Transform Hero Header
            const mainBadge = document.getElementById('main-icon-badge');
            const mainIcon = document.getElementById('main-icon-el');
            if (mainBadge && mainIcon) {
                mainBadge.className = 'success-icon-badge state-paid';
                mainIcon.className = 'fa-solid fa-check';
            }

            const titleEl = document.getElementById('page-main-title');
            if (titleEl) titleEl.textContent = 'Tạo Đơn Hàng May Rèm Thành Công!';

            const subEl = document.getElementById('page-main-sub');
            if (subEl) {
                subEl.innerHTML = 'Thanh toán chuyển khoản VietQR đã được <strong>xác nhận thành công</strong>!<br>Đơn hàng đã được duyệt và chuyển sang bộ phận xưởng may CurtainLux.';
            }

            // 3. Show Paid Banner
            const paidBanner = document.getElementById('paid-banner');
            if (paidBanner) {
                paidBanner.style.display = 'flex';
            }

            // 4. Update status badge
            const badgeEl = document.getElementById('payment-status-badge');
            if (badgeEl) {
                badgeEl.style.color = '#10b981';
                badgeEl.innerHTML = '<i class="fa-solid fa-circle-check"></i> Đã thanh toán thành công';
            }

            // 5. Update thank-you letter status badge
            const letterPill = document.querySelector('#thankYouLetterSection .pill-status');
            if (letterPill) {
                letterPill.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #10b981;"></i> <span style="color: #065f46; font-weight: 700;">Đã Thanh Toán Hoàn Tất</span>';
            }
        }

        // Initialize auto-polling
        document.addEventListener('DOMContentLoaded', startPolling);
    </script>
</body>
</html>
