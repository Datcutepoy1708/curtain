@extends('shop.account.layout')

@section('title', 'Đơn Hàng May Rèm Của Tôi')
@section('breadcrumb-active', 'Đơn Hàng Đã Đặt')

@section('account-content')
<div class="account-content-card">
    <div class="content-header">
        <div>
            <h2 class="content-title">
                <i class="fa-solid fa-cart-flatbed" style="color: var(--primary, #b8935c);"></i> Đơn Hàng May Rèm Của Tôi
            </h2>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                Quản lý và theo dõi tiến độ thi công từng bộ rèm theo thời gian thực
            </div>
        </div>
    </div>

    <!-- Status Filters -->
    <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px;">
        @php $curStatus = request('status', 'all'); @endphp
        <a href="{{ route('customer.orders') }}" 
           style="padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid var(--border-line); background: {{ $curStatus === 'all' ? 'var(--primary, #b8935c)' : '#fff' }}; color: {{ $curStatus === 'all' ? '#fff' : 'var(--text-main)' }};">
            Tất cả
        </a>
        <a href="{{ route('customer.orders', ['status' => 'pending']) }}" 
           style="padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid var(--border-line); background: {{ $curStatus === 'pending' ? '#d97706' : '#fff' }}; color: {{ $curStatus === 'pending' ? '#fff' : 'var(--text-main)' }};">
            Chờ xác nhận
        </a>
        <a href="{{ route('customer.orders', ['status' => 'manufacturing']) }}" 
           style="padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid var(--border-line); background: {{ $curStatus === 'manufacturing' ? '#7c3aed' : '#fff' }}; color: {{ $curStatus === 'manufacturing' ? '#fff' : 'var(--text-main)' }};">
            Đang gia công tại xưởng
        </a>
        <a href="{{ route('customer.orders', ['status' => 'completed']) }}" 
           style="padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid var(--border-line); background: {{ $curStatus === 'completed' ? '#16a34a' : '#fff' }}; color: {{ $curStatus === 'completed' ? '#fff' : 'var(--text-main)' }};">
            Đã hoàn thành
        </a>
        <a href="{{ route('customer.orders', ['status' => 'cancelled']) }}" 
           style="padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 700; text-decoration: none; border: 1px solid var(--border-line); background: {{ $curStatus === 'cancelled' ? '#dc2626' : '#fff' }}; color: {{ $curStatus === 'cancelled' ? '#fff' : 'var(--text-main)' }};">
            Đã hủy
        </a>
    </div>

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 20px;">
            @foreach($orders as $o)
                <div style="border: 1px solid var(--border-line); border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <!-- Order Card Header -->
                    <div style="background: #f8fafc; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid var(--border-line);">
                        <div>
                            <span style="font-size: 12px; color: var(--text-muted);">Mã đơn hàng:</span>
                            <a href="{{ route('customer.order.detail', $o->order_code) }}" style="font-weight: 800; color: var(--primary, #b8935c); font-size: 15px; text-decoration: none; margin-left: 4px;">
                                {{ $o->order_code }}
                            </a>
                            <span style="font-size: 12px; color: var(--text-muted); margin-left: 12px;">
                                <i class="fa-regular fa-clock"></i> {{ $o->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        <div style="display: flex; gap: 8px; align-items: center;">
                            <!-- Payment Status Badge -->
                            <span style="font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 999px; 
                                  background: {{ $o->payment_status === 'paid' ? '#dcfce7' : ($o->payment_status === 'failed' ? '#fee2e2' : '#fef3c7') }}; 
                                  color: {{ $o->payment_status === 'paid' ? '#15803d' : ($o->payment_status === 'failed' ? '#b91c1c' : '#b45309') }};">
                                <i class="fa-solid fa-credit-card"></i> {{ $o->payment_status_label }}
                            </span>

                            <!-- Order Status Badge -->
                            <span style="font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 999px;
                                  background: {{ $o->order_status === 'completed' ? '#dcfce7' : ($o->order_status === 'cancelled' ? '#fee2e2' : '#eff6ff') }};
                                  color: {{ $o->order_status === 'completed' ? '#15803d' : ($o->order_status === 'cancelled' ? '#b91c1c' : '#1d4ed8') }};">
                                {{ $o->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div style="padding: 16px 20px;">
                        @foreach($o->items as $item)
                            <div style="display: flex; gap: 14px; align-items: center; padding: 8px 0; {{ !$loop->last ? 'border-bottom: 1px dashed var(--border-line);' : '' }}">
                                <img src="{{ $item->product?->image ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=150&q=80' }}" 
                                     alt="{{ $item->product_name }}" style="width: 56px; height: 56px; border-radius: 6px; object-fit: cover; border: 1px solid var(--border-line);">
                                <div style="flex: 1;">
                                    <div style="font-weight: 700; font-size: 14px; color: var(--text-main);">{{ $item->product_name }}</div>
                                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                                        <strong>{{ $item->room_label ?: 'Ô cửa tiêu chuẩn' }}</strong> &bull; Rộng {{ $item->width }}cm &times; Cao {{ $item->height }}cm &bull; Số lượng: {{ $item->quantity }} bộ
                                    </div>
                                </div>
                                <div style="font-weight: 700; font-size: 14px; color: var(--accent-cta);">
                                    {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Card Footer -->
                    <div style="background: #fafafa; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-top: 1px solid var(--border-line);">
                        <div>
                            <span style="font-size: 13px; color: var(--text-muted);">Tổng thanh toán:</span>
                            <strong style="font-size: 18px; color: var(--accent-cta); margin-left: 6px;">{{ $o->formatted_total }}</strong>
                        </div>

                        <div style="display: flex; gap: 8px; align-items: center;">
                            <!-- Link to detail -->
                            <a href="{{ route('customer.order.detail', $o->order_code) }}" class="btn-wishlist" style="padding: 6px 14px; font-size: 13px; text-decoration: none;">
                                <i class="fa-solid fa-route"></i> Xem Tiến Độ & Chi Tiết
                            </a>

                            <!-- Pay now if pending and not paid -->
                            @if($o->payment_status === 'pending' && $o->order_status !== 'cancelled')
                                <a href="{{ route('order.success', $o->order_code) }}" class="btn-book-survey" style="padding: 6px 14px; font-size: 13px; text-decoration: none;">
                                    <i class="fa-solid fa-qrcode"></i> Thanh Toán Ngay
                                </a>
                            @endif

                            <!-- Cancel order if pending -->
                            @if($o->canBeCancelledByCustomer())
                                <form action="{{ route('customer.order.cancel', $o->order_code) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn may rèm {{ $o->order_code }}? Lượng phôi rèm sẽ được hoàn lại kho.');">
                                    @csrf
                                    <button type="submit" style="background: #fee2e2; color: #dc2626; border: 1px solid #fecdd3; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                        <i class="fa-solid fa-xmark"></i> Hủy Đơn
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div style="margin-top: 20px;">
                {{ $orders->links('vendor.pagination.custom') }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
            <i class="fa-solid fa-cart-shopping" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Không tìm thấy đơn hàng nào</h3>
            <p style="font-size: 13px; margin-bottom: 20px;">Quý khách chưa có đơn đặt may rèm nào theo trạng thái này.</p>
            <a href="{{ route('shop.index') }}" class="btn-book-survey" style="display: inline-flex; text-decoration: none;">
                <i class="fa-solid fa-store"></i> Khám Phá Mẫu Rèm & Đặt May
            </a>
        </div>
    @endif
</div>
@endsection
