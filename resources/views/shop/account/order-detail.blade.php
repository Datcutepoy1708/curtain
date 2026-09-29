@extends('shop.account.layout')

@section('title', 'Tiến Độ Đơn May: ' . $order->order_code)
@section('breadcrumb-active', 'Chi Tiết Đơn ' . $order->order_code)

@section('account-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Navigation Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('customer.orders') }}" class="btn-wishlist" style="text-decoration: none; font-size: 13px;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách đơn
        </a>

        <div style="display: flex; gap: 10px; align-items: center;">
            <!-- Pay now if pending -->
            @if($order->payment_status === 'pending' && $order->order_status !== 'cancelled')
                <a href="{{ route('order.success', $order->order_code) }}" class="btn-book-survey" style="font-size: 13px; text-decoration: none;">
                    <i class="fa-solid fa-qrcode"></i> Thanh Toán VietQR / VNPAY
                </a>
            @endif

            <!-- Cancel order if pending -->
            @if($order->canBeCancelledByCustomer())
                <form action="{{ route('customer.order.cancel', $order->order_code) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng {{ $order->order_code }}? Tồn kho rèm sẽ được hoàn trả.');">
                    @csrf
                    <button type="submit" style="background: #fee2e2; color: #dc2626; border: 1px solid #fecdd3; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-xmark"></i> Hủy Đơn Hàng
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Stepper Card: 5-step Custom Curtain Timeline -->
    <div class="account-content-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 10px;">
            <div>
                <span style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Tiến độ may đo & thi công</span>
                <h2 style="font-size: 20px; font-weight: 800; color: var(--text-main); margin-top: 2px;">
                    Mã Đơn: <span style="color: var(--primary, #b8935c);">{{ $order->order_code }}</span>
                </h2>
            </div>
            <div>
                <span style="font-size: 13px; font-weight: 700; padding: 4px 14px; border-radius: 999px;
                      background: {{ $order->order_status === 'completed' ? '#dcfce7' : ($order->order_status === 'cancelled' ? '#fee2e2' : '#eff6ff') }};
                      color: {{ $order->order_status === 'completed' ? '#15803d' : ($order->order_status === 'cancelled' ? '#b91c1c' : '#1d4ed8') }};">
                    {{ $order->status_label }}
                </span>
            </div>
        </div>

        @php
            $steps = [
                'pending' => ['step' => 1, 'label' => 'Tiếp Nhận Đơn', 'desc' => 'Chuyên viên kiểm tra kích thước & tồn kho'],
                'confirmed' => ['step' => 2, 'label' => 'Chốt Số Đo & Cắt Vải', 'desc' => 'Xuất cuộn vải, pha cắt phôi theo kích thước'],
                'manufacturing' => ['step' => 3, 'label' => 'May Tại Xưởng', 'desc' => 'Dập mí, ráp phụ kiện, luồn ray, dập lỗ oze'],
                'shipping' => ['step' => 4, 'label' => 'Vận Chuyển & Lắp Đặt', 'desc' => 'Thợ kỹ thuật giao rèm và khoan gắn tận nhà'],
                'completed' => ['step' => 5, 'label' => 'Hoàn Tất & Nghiệm Thu', 'desc' => 'Bàn giao, kích hoạt bảo hành 2 năm'],
            ];

            $statusRank = [
                'pending' => 1,
                'confirmed' => 2,
                'manufacturing' => 3,
                'shipping' => 4,
                'installed' => 4,
                'completed' => 5,
                'cancelled' => 0,
            ];

            $currentRank = $statusRank[$order->order_status] ?? 1;
        @endphp

        @if($order->order_status === 'cancelled')
            <div style="background: #fee2e2; border: 1px solid #fecdd3; border-radius: 8px; padding: 18px 20px; color: #b91c1c; display: flex; align-items: center; gap: 14px;">
                <i class="fa-solid fa-ban" style="font-size: 28px;"></i>
                <div>
                    <div style="font-weight: 800; font-size: 15px;">Đơn hàng này đã bị hủy</div>
                    <div style="font-size: 13px; margin-top: 2px;">{{ $order->notes ?: 'Đơn hàng không tiếp tục may đo. Lượng vải tồn kho đã được hoàn lại.' }}</div>
                </div>
            </div>
        @else
            <!-- 5-Step Stepper Component -->
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin: 10px 0; position: relative;">
                @foreach($steps as $key => $s)
                    @php
                        $isCompleted = $s['step'] <= $currentRank;
                        $isCurrent = $s['step'] === $currentRank;
                    @endphp
                    <div style="text-align: center; position: relative; z-index: 1;">
                        <div style="width: 42px; height: 42px; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;
                                    background: {{ $isCompleted ? 'var(--primary, #b8935c)' : '#f1f5f9' }};
                                    color: {{ $isCompleted ? '#fff' : '#94a3b8' }};
                                    box-shadow: {{ $isCurrent ? '0 0 0 4px rgba(184, 147, 92, 0.2)' : 'none' }};
                                    border: 2px solid {{ $isCompleted ? 'var(--primary, #b8935c)' : '#cbd5e1' }};">
                            @if($s['step'] < $currentRank || $order->order_status === 'completed')
                                <i class="fa-solid fa-check"></i>
                            @else
                                {{ $s['step'] }}
                            @endif
                        </div>
                        <div style="font-size: 13px; font-weight: 700; color: {{ $isCompleted ? 'var(--text-main)' : '#94a3b8' }}; margin-bottom: 2px;">
                            {{ $s['label'] }}
                        </div>
                        <div style="font-size: 11px; color: var(--text-muted); line-height: 1.3;">
                            {{ $s['desc'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Order Items Details Card -->
    <div class="account-content-card">
        <div class="content-header">
            <h3 class="content-title">
                <i class="fa-solid fa-ruler-combined" style="color: var(--primary, #b8935c);"></i> Chi Tiết Các Bộ Rèm Trong Đơn ({{ $order->items->count() }} ô cửa)
            </h3>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($order->items as $item)
                <div style="border: 1px solid var(--border-line); border-radius: 10px; padding: 18px; display: grid; grid-template-columns: 80px 1fr auto; gap: 16px; align-items: center;">
                    <img src="{{ $item->product?->image ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=200&q=80' }}" 
                         alt="{{ $item->product_name }}" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-line);">

                    <div>
                        <div style="font-size: 15px; font-weight: 800; color: var(--text-main);">
                            @if($item->product)
                                <a href="{{ route('shop.show', $item->product->slug) }}" style="color: inherit; text-decoration: none;">
                                    {{ $item->product_name }}
                                </a>
                            @else
                                {{ $item->product_name }}
                            @endif
                        </div>

                        <div style="background: #f8fafc; padding: 4px 10px; border-radius: 4px; display: inline-block; margin: 4px 0; font-size: 12px; font-weight: 700; color: var(--accent-cta);">
                            <i class="fa-solid fa-tag"></i> Vị trí: {{ $item->room_label ?: 'Ô cửa tiêu chuẩn' }}
                        </div>

                        <div style="font-size: 13px; color: var(--text-muted); line-height: 1.6;">
                            <div>Kích thước may đo: Rộng <strong>{{ $item->width }} cm</strong> &times; Cao <strong>{{ $item->height }} cm</strong> ({{ $item->mount_type === 'inside' ? 'Lọt lòng' : 'Phủ bì' }})</div>
                            <div>Quy đổi diện tích/vải: <strong>{{ $item->calculated_units }} {{ $item->product?->unit_label ?: 'm²' }}</strong> &bull; Đã xuất kho: <strong>{{ $item->stock_deducted }} {{ $item->product?->stock_unit_label ?: 'đơn vị' }}</strong></div>

                            @if(!empty($item->selected_options) && is_array($item->selected_options))
                                <div style="margin-top: 4px; display: flex; flex-wrap: wrap; gap: 4px;">
                                    @foreach($item->selected_options as $opt)
                                        <span style="background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                            {{ $opt['name'] }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                        <div>
                            <div style="font-size: 12px; color: var(--text-muted);">Số lượng: {{ $item->quantity }} bộ</div>
                            <div style="font-size: 18px; font-weight: 800; color: var(--accent-cta); margin-top: 2px;">
                                {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                            </div>
                        </div>

                        <!-- Review Button for completed/delivered orders -->
                        @if(in_array($order->order_status, ['confirmed', 'manufacturing', 'shipping', 'installed', 'completed']) && $item->product)
                            <a href="{{ route('shop.show', $item->product->slug) }}#review-section" 
                               class="btn-wishlist" style="font-size: 12px; padding: 6px 12px; text-decoration: none; color: #b8935c; border-color: #b8935c;">
                                <i class="fa-solid fa-star" style="color: #f59e0b;"></i> Đánh Giá Mẫu Này
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Order & Payment Info Cards Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Shipping & Customer Info -->
        <div class="account-content-card">
            <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-location-dot" style="color: var(--primary, #b8935c);"></i> Địa Chỉ Lắp Đặt May Đo
            </h3>
            <div style="font-size: 13px; line-height: 1.8; color: var(--text-main);">
                <div><strong>Người nhận:</strong> {{ $order->customer_name }}</div>
                <div><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</div>
                @if($order->customer_email)
                    <div><strong>Email:</strong> {{ $order->customer_email }}</div>
                @endif
                <div><strong>Địa chỉ:</strong> {{ $order->shipping_address }}, {{ $order->city }}</div>
                @if($order->notes)
                    <div style="margin-top: 6px; background: #f8fafc; padding: 8px 12px; border-radius: 6px; font-size: 12px; color: var(--text-muted);">
                        <strong>Ghi chú đơn:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Info -->
        <div class="account-content-card">
            <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-receipt" style="color: #16a34a;"></i> Thông Tin Thanh Toán
            </h3>
            <div style="font-size: 13px; line-height: 1.8; color: var(--text-main);">
                <div><strong>Hình thức:</strong> 
                    @if($order->payment_method === 'bank_transfer')
                        Chuyển Khoản Ngân Hàng (VietQR MBBank)
                    @elseif($order->payment_method === 'vnpay')
                        Cổng Trực Tuyến VNPAY (Sandbox)
                    @else
                        Thanh toán khi nhận & lắp đặt (COD)
                    @endif
                </div>
                <div><strong>Trạng thái thanh toán:</strong> 
                    <span style="font-weight: 700; color: {{ $order->payment_status === 'paid' ? '#16a34a' : '#d97706' }};">
                        {{ $order->payment_status_label }}
                    </span>
                </div>
                @if($order->transaction_id)
                    <div><strong>Mã giao dịch:</strong> <code>{{ $order->transaction_id }}</code></div>
                @endif
                @if($order->paid_at)
                    <div><strong>Thời gian nhận tiền:</strong> {{ $order->paid_at->format('d/m/Y H:i') }}</div>
                @endif
                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border-line); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 14px; font-weight: 700;">Tổng Tiền Đơn May:</span>
                    <strong style="font-size: 20px; color: var(--accent-cta);">{{ $order->formatted_total }}</strong>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
