<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra Cứu Tiến Độ Đơn May Rèm | CurtainLux</title>
    <meta name="description" content="Tra cứu tình trạng xử lý đơn hàng, tiến độ may đo tại xưởng và lịch giao lắp rèm cửa CurtainLux.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- External Shop CSS -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">

    <style>
        .tracking-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px 80px;
        }

        .tracking-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .tracking-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main, #1e293b);
            margin-bottom: 8px;
        }

        .tracking-header p {
            color: var(--text-muted, #64748b);
            font-size: 15px;
            max-width: 580px;
            margin: 0 auto;
        }

        .tracking-search-card {
            background: #ffffff;
            border: 1px solid var(--border-line, #e2e8f0);
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            margin-bottom: 35px;
        }

        .search-form-row {
            display: flex;
            gap: 12px;
        }

        @media (max-width: 600px) {
            .search-form-row {
                flex-direction: column;
            }
        }

        .search-input-wrap {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input-wrap i {
            position: absolute;
            left: 16px;
            color: var(--text-muted, #94a3b8);
            font-size: 16px;
        }

        .search-input-wrap input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border: 1.5px solid var(--border-line, #e2e8f0);
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-input-wrap input:focus {
            border-color: var(--accent-cta, #b8935c);
        }

        .btn-search-tracking {
            background: var(--accent-cta, #b8935c);
            color: #ffffff;
            border: none;
            padding: 0 28px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 50px;
        }

        .btn-search-tracking:hover {
            background: var(--accent-cta-hover, #a4824e);
        }

        .order-result-card {
            background: #ffffff;
            border: 1px solid var(--border-line, #e2e8f0);
            border-radius: 14px;
            padding: 26px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-line, #e2e8f0);
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .order-badge-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main, #1e293b);
        }

        .order-badge-title span {
            color: var(--accent-cta, #b8935c);
            font-family: monospace;
            font-size: 20px;
        }

        /* TIMELINE TRACKER */
        .status-timeline {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 25px 0 30px;
            position: relative;
        }

        @media (max-width: 700px) {
            .status-timeline {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        .timeline-step {
            text-align: center;
            position: relative;
        }

        .step-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 14px;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            transition: all 0.3s;
        }

        .timeline-step.active .step-dot {
            background: var(--accent-cta, #b8935c);
            color: #ffffff;
            border-color: var(--accent-cta, #b8935c);
            box-shadow: 0 0 0 4px rgba(184, 147, 92, 0.2);
        }

        .timeline-step.completed .step-dot {
            background: #10b981;
            color: #ffffff;
            border-color: #10b981;
        }

        .step-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted, #64748b);
        }

        .timeline-step.active .step-label {
            color: var(--text-main, #1e293b);
            font-weight: 700;
        }

        .order-info-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            font-size: 14px;
            margin-bottom: 18px;
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
        }

        @media (max-width: 600px) {
            .order-info-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    @include('shop.partials.header')

    <div class="tracking-wrapper">
        <div class="tracking-header">
            <h1><i class="fa-solid fa-truck-fast" style="color: var(--accent-cta);"></i> Tra Cứu Tiến Độ Đơn May Rèm</h1>
            <p>Nhập Mã đơn hàng (ví dụ: <code>DH-20260917-XXXX</code>) hoặc Số điện thoại đặt hàng để kiểm tra trạng thái may đo và lịch lắp đặt thực tế.</p>
        </div>

        <!-- Search Card -->
        <div class="tracking-search-card">
            <form action="{{ route('order.tracking') }}" method="GET">
                <div class="search-form-row">
                    <div class="search-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Nhập mã đơn hàng hoặc số điện thoại..." required autofocus>
                    </div>
                    <button type="submit" class="btn-search-tracking">
                        <i class="fa-solid fa-magnifying-glass"></i> Tra Cứu Ngay
                    </button>
                </div>
            </form>
        </div>

        <!-- Search Results -->
        @if(!empty($keyword))
            @if($orders->count() > 0)
                <div>
                    <h2 style="font-size: 18px; font-weight: 800; margin-bottom: 16px; color: var(--text-main);">
                        Tìm thấy {{ $orders->count() }} đơn hàng may rèm cho: <em>"{{ $keyword }}"</em>
                    </h2>

                    @foreach($orders as $order)
                        @php
                            // Step indexing: pending (1), confirmed (2), manufacturing (3), shipping/installed (4), completed (5)
                            $stepIndex = match($order->order_status) {
                                'pending' => 1,
                                'confirmed' => 2,
                                'manufacturing' => 3,
                                'shipping', 'installed' => 4,
                                'completed' => 5,
                                default => 1,
                            };
                        @endphp

                        <div class="order-result-card">
                            <div class="result-header">
                                <div>
                                    <div class="order-badge-title">
                                        Mã Đơn: <span>{{ $order->order_code }}</span>
                                    </div>
                                    <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                                        Ngày đặt: {{ $order->created_at->format('H:i - d/m/Y') }} &bull; Người nhận: <strong>{{ $order->customer_name }}</strong>
                                    </div>
                                </div>

                                <div>
                                    <span style="display: inline-block; padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 700; background: {{ $order->payment_status === 'paid' ? '#dcfce7' : '#fef3c7' }}; color: {{ $order->payment_status === 'paid' ? '#15803d' : '#b45309' }};">
                                        {{ $order->payment_status === 'paid' ? '✓ Đã thanh toán' : '⏳ Chờ thanh toán' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Stepper -->
                            <div class="status-timeline">
                                <div class="timeline-step {{ $stepIndex > 1 ? 'completed' : ($stepIndex == 1 ? 'active' : '') }}">
                                    <div class="step-dot">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                    <div class="step-label">Tiếp nhận đơn</div>
                                </div>

                                <div class="timeline-step {{ $stepIndex > 2 ? 'completed' : ($stepIndex == 2 ? 'active' : '') }}">
                                    <div class="step-dot">
                                        <i class="fa-solid fa-ruler-combined"></i>
                                    </div>
                                    <div class="step-label">Chốt số đo</div>
                                </div>

                                <div class="timeline-step {{ $stepIndex > 3 ? 'completed' : ($stepIndex == 3 ? 'active' : '') }}">
                                    <div class="step-dot">
                                        <i class="fa-solid fa-scissors"></i>
                                    </div>
                                    <div class="step-label">May tại xưởng</div>
                                </div>

                                <div class="timeline-step {{ $stepIndex > 4 ? 'completed' : ($stepIndex == 4 ? 'active' : '') }}">
                                    <div class="step-dot">
                                        <i class="fa-solid fa-truck-ramp-box"></i>
                                    </div>
                                    <div class="step-label">Giao & lắp đặt</div>
                                </div>

                                <div class="timeline-step {{ $stepIndex >= 5 ? 'completed' : '' }}">
                                    <div class="step-dot">
                                        <i class="fa-solid fa-shield-check"></i>
                                    </div>
                                    <div class="step-label">Hoàn tất & BH</div>
                                </div>
                            </div>

                            <!-- Details breakdown -->
                            <div class="order-info-list">
                                <div><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</div>
                                <div><strong>Địa chỉ lắp:</strong> {{ $order->shipping_address }}</div>
                                <div>
                                    <strong>Hình thức:</strong> 
                                    {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận (COD)' : 'Chuyển khoản VietQR' }}
                                </div>
                                <div>
                                    <strong>Tổng tiền:</strong> 
                                    <strong style="color: var(--accent-cta); font-size: 16px;">
                                        {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                                    </strong>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                <a href="{{ route('order.success', ['orderCode' => $order->order_code]) }}" class="btn-read-more" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; padding: 8px 16px; font-size: 13px; font-weight: 700; border: 1px solid var(--border-line); border-radius: 6px;">
                                    <i class="fa-solid fa-qrcode"></i> Xem Chi Tiết / Cổng Thanh Toán &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="background: #ffffff; border: 1px solid var(--border-line); border-radius: 14px; padding: 60px 20px; text-align: center;">
                    <i class="fa-solid fa-box-open" style="font-size: 48px; color: var(--text-light); margin-bottom: 16px;"></i>
                    <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 8px;">Không tìm thấy đơn hàng phù hợp</h3>
                    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">
                        Không có đơn hàng nào khớp với từ khóa "<strong>{{ $keyword }}</strong>". Vui lòng kiểm tra lại mã đơn hàng hoặc số điện thoại.
                    </p>
                    <a href="tel:0912345678" class="btn-book-survey" style="display: inline-flex; text-decoration: none;">
                        <i class="fa-solid fa-phone"></i> Gọi Hotline Hỗ Trợ: 0912.345.678
                    </a>
                </div>
            @endif
        @endif
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} CurtainLux - Thương Hiệu Rèm Cửa & Nội Thất Vải Tinh Tế. Hotline: 0912.345.678</p>
    </footer>
</body>
</html>
