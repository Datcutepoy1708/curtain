@extends('admin.layouts.app')

@section('title', 'Chi Tiết Đơn Đặt Rèm #' . $order->order_code)
@section('page-title', 'Chi Tiết Đơn Đặt Rèm #' . $order->order_code)

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách đơn
        </a>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="badge {{ $order->status_badge_class }}" style="font-size: 13px; padding: 6px 12px;">
                {{ $order->status_label }}
            </span>
            <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }}" style="font-size: 13px; padding: 6px 12px;">
                {{ $order->payment_status_label }}
            </span>
        </div>
    </div>

    <!-- Main Grid -->
    <div style="display: grid; grid-template-columns: 1fr 360px; gap: 24px;">
        
        <!-- Left: Customer & Curtain Items Specifications -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Customer Information Box -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-user-tag" style="color: var(--brand);"></i>
                        Thông Tin Khách Hàng & Nơi Lắp Đặt
                    </div>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <small style="color: var(--on-surface-variant); font-size: 12px; display: block;">Họ tên khách hàng:</small>
                            <strong style="font-size: 15px; color: var(--on-surface);">{{ $order->customer_name }}</strong>
                        </div>
                        <div>
                            <small style="color: var(--on-surface-variant); font-size: 12px; display: block;">Số điện thoại:</small>
                            <strong style="font-size: 15px; color: var(--brand);"><i class="fa-solid fa-phone"></i> {{ $order->customer_phone }}</strong>
                        </div>
                        <div style="grid-column: span 2;">
                            <small style="color: var(--on-surface-variant); font-size: 12px; display: block;">Địa chỉ lắp đặt rèm:</small>
                            <strong style="font-size: 14px;">{{ $order->shipping_address }}, {{ $order->city }}</strong>
                        </div>
                        @if($order->notes)
                            <div style="grid-column: span 2; background: var(--surface-alt); padding: 12px; border-radius: var(--radius-sm); border: 1px dashed var(--border);">
                                <small style="color: var(--on-surface-variant); font-size: 12px; display: block; font-weight: 700;">Ghi chú của khách:</small>
                                <span style="font-size: 13px;">{{ $order->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Curtain Measurement & Order Items Table -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-ruler-combined" style="color: #7c3aed;"></i>
                        Thông Số May Đo Từng Khung Cửa ({{ $order->items->count() }} vị trí)
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Khung Cửa / Vị Trí</th>
                                <th>Mẫu Rèm</th>
                                <th>Kích Thước ($W \times H$)</th>
                                <th>Kiểu Lắp</th>
                                <th>Quy Cách / Phụ Kiện Chọn</th>
                                <th>Đơn Giá</th>
                                <th>Số Lượng</th>
                                <th style="text-align: right;">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong style="color: var(--on-surface); font-size: 13px;">
                                            {{ $item->room_label ?: 'Cửa số ' . $loop->iteration }}
                                        </strong>
                                    </td>
                                    <td>
                                        <strong style="color: var(--brand); font-size: 13px;">{{ $item->product_name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-info" style="font-size: 12px;">
                                            {{ $item->width }} cm &times; {{ $item->height }} cm
                                        </span>
                                        <div style="font-size: 11px; color: var(--on-surface-variant); margin-top: 2px;">
                                            Diện tích: {{ $item->calculated_units }} m²
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-neutral">
                                            {{ $item->mount_type === 'inside' ? 'Lọt lòng khung' : 'Phủ bì tường' }}
                                        </span>
                                    </td>
                                    <td style="max-width: 220px;">
                                        @if($item->selected_options && is_array($item->selected_options) && count($item->selected_options) > 0)
                                            <ul style="margin: 0; padding-left: 16px; font-size: 12px; color: var(--on-surface-variant);">
                                                @foreach($item->selected_options as $groupName => $optVal)
                                                    <li><strong>{{ is_string($groupName) ? $groupName : 'Tùy chọn' }}:</strong> {{ is_array($optVal) ? ($optVal['name'] ?? json_encode($optVal)) : $optVal }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span style="font-size: 12px; color: var(--on-surface-variant);">Quy cách may tiêu chuẩn</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->unit_price, 0, ',', '.') }}₫</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td style="text-align: right; font-weight: 700; color: #d97706;">
                                        {{ $item->formatted_subtotal }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" style="text-align: right; font-weight: 700; font-size: 14px; padding: 16px;">
                                    TỔNG CỘNG ĐƠN HÀNG:
                                </td>
                                <td style="text-align: right; font-weight: 900; font-size: 16px; color: #16a34a; padding: 16px;">
                                    {{ $order->formatted_total }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Order Processing Actions -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Update Order Status Card -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-arrows-rotate" style="color: var(--brand);"></i>
                        Tiến Độ Thực Hiện Rèm
                    </div>
                </div>
                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="card-body">
                    @csrf
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--on-surface);">
                            Chuyển Trạng Thái Đơn:
                        </label>
                        <select name="order_status" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px; outline: none;">
                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>1. Chờ xác nhận kích thước</option>
                            <option value="confirmed" {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>2. Đã xác nhận / Cắt vải</option>
                            <option value="manufacturing" {{ $order->order_status === 'manufacturing' ? 'selected' : '' }}>3. Đang gia công may rèm</option>
                            <option value="shipping" {{ $order->order_status === 'shipping' ? 'selected' : '' }}>4. Đang vận chuyển & lắp đặt</option>
                            <option value="installed" {{ $order->order_status === 'installed' ? 'selected' : '' }}>5. Đã lắp đặt hoàn thiện</option>
                            <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>6. Nghiệm thu & hoàn tất</option>
                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>7. Đã hủy đơn</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                        <i class="fa-solid fa-check"></i> Cập Nhật Tiến Độ
                    </button>
                </form>
            </div>

            <!-- Update Payment Status Card -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-credit-card" style="color: #16a34a;"></i>
                        Tình Trạng Thanh Toán
                    </div>
                </div>
                <form action="{{ route('admin.orders.confirm-payment', $order->id) }}" method="POST" class="card-body">
                    @csrf
                    <div style="margin-bottom: 12px; font-size: 13px;">
                        Phương thức thanh toán: <strong>{{ strtoupper($order->payment_method) }}</strong>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px; color: var(--on-surface);">
                            Trạng thái thanh toán:
                        </label>
                        <select name="payment_status" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px; outline: none;">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                            <option value="partially_paid" {{ $order->payment_status === 'partially_paid' ? 'selected' : '' }}>Đã đặt cọc</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán 100%</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: center; padding: 10px;">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Cập Nhật Thanh Toán
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
