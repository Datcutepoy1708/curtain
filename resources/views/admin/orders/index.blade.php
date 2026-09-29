@extends('admin.layouts.app')

@section('title', 'Quản Lý Đơn Hàng Rèm')
@section('page-title', 'Danh Sách & Xử Lý Đơn Đặt Rèm')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- KPI Summary Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px;">
        <div class="card" style="margin-bottom: 0; padding: 16px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Tổng Đơn Hàng</div>
            <div style="font-size: 22px; font-weight: 800; color: var(--brand); margin: 4px 0;">{{ $totalOrders }}</div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Toàn hệ thống</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 16px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Chờ Xác Nhận</div>
            <div style="font-size: 22px; font-weight: 800; color: #d97706; margin: 4px 0;">{{ $pendingOrders }}</div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Cần gọi xác nhận kích thước</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 16px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Đang May & Lắp Đặt</div>
            <div style="font-size: 22px; font-weight: 800; color: #7c3aed; margin: 4px 0;">{{ $manufacturingOrders }}</div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Xưởng đang gia công</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 16px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Đã Hoàn Tất</div>
            <div style="font-size: 22px; font-weight: 800; color: #16a34a; margin: 4px 0;">{{ $completedOrders }}</div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Đã nghiệm thu xong</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 16px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Đơn Đã Hủy</div>
            <div style="font-size: 22px; font-weight: 800; color: #dc2626; margin: 4px 0;">{{ $cancelledOrders }}</div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Khách đổi ý</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 16px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Doanh Thu Đã Thu</div>
            <div style="font-size: 20px; font-weight: 800; color: #16a34a; margin: 4px 0;">{{ number_format($paidRevenue, 0, ',', '.') }}₫</div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Đã thanh toán (MB / VietQR)</div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card" style="margin-bottom: 0; padding: 16px 20px;">
        <form action="{{ route('admin.orders.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Mã đơn (DH-...), tên khách, SĐT..."
                   style="min-width: 220px; flex: 1; padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">

            <select name="status" style="padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã duyệt / Cắt may</option>
                <option value="manufacturing" {{ request('status') === 'manufacturing' ? 'selected' : '' }}>Đang gia công may</option>
                <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao & lắp</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>

            <select name="payment_status" style="padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">
                <option value="">-- Thanh toán --</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán đủ</option>
                <option value="partially_paid" {{ request('payment_status') === 'partially_paid' ? 'selected' : '' }}>Đã cọc</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
            </select>

            <div style="display: flex; align-items: center; gap: 6px;">
                <input type="date" name="date_from" value="{{ request('date_from') }}" title="Từ ngày"
                       style="padding: 7px 10px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 12px;">
                <span style="color: var(--on-surface-variant);">-</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}" title="Đến ngày"
                       style="padding: 7px 10px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 12px;">
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Lọc đơn
            </button>

            @if(request()->anyFilled(['keyword', 'status', 'payment_status', 'date_from', 'date_to']))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">Đặt lại</a>
            @endif
        </form>
    </div>

    <!-- Bulk Action Form & Orders Table -->
    <form action="{{ route('admin.orders.bulk-action') }}" method="POST">
        @csrf

        <!-- Bulk Action Bar -->
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; padding: 10px 16px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; cursor: pointer; margin: 0;">
                    <input type="checkbox" id="selectAll" style="width: 16px; height: 16px; cursor: pointer;">
                    <span>Chọn tất cả (<span id="selectedCount">0</span> đã chọn)</span>
                </label>
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <select name="bulk_status" style="font-size: 12px; padding: 6px 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface);" required>
                    <option value="">-- Đổi trạng thái hàng loạt --</option>
                    <option value="confirmed">Xác nhận duyệt may</option>
                    <option value="manufacturing">Đang gia công xưởng</option>
                    <option value="shipping">Bàn giao cho thợ đi lắp</option>
                    <option value="completed">Nghiệm thu hoàn tất</option>
                    <option value="cancelled">Hủy đơn hàng loạt</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Bạn có chắc chắn muốn cập nhật trạng thái các đơn hàng đã chọn?')">
                    Áp dụng
                </button>
            </div>
        </div>

        <!-- Main Orders Table -->
        <div class="card" style="margin-bottom: 0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 36px; text-align: center;">#</th>
                            <th>Mã Đơn Hàng</th>
                            <th>Khách Hàng</th>
                            <th>Địa Chỉ Giao / Lắp</th>
                            <th>Số Lượng Cửa</th>
                            <th>Tổng Tiền</th>
                            <th>Thanh Toán</th>
                            <th>Trạng Thái</th>
                            <th style="text-align: right;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="order_ids[]" value="{{ $o->id }}" class="item-checkbox" style="width: 16px; height: 16px; cursor: pointer;">
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $o->id) }}" style="font-weight: 800; color: var(--brand); text-decoration: none; font-size: 13px;">
                                        {{ $o->order_code }}
                                    </a>
                                    <div style="font-size: 11px; color: var(--on-surface-variant);">{{ $o->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <strong style="font-size: 13px;">{{ $o->customer_name }}</strong>
                                    <div style="font-size: 11px; color: var(--on-surface-variant);"><i class="fa-solid fa-phone"></i> {{ $o->customer_phone }}</div>
                                </td>
                                <td style="max-width: 240px;">
                                    <div style="font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $o->shipping_address }}">
                                        {{ $o->shipping_address }}
                                    </div>
                                    <small style="color: var(--on-surface-variant);">{{ $o->city }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-neutral">{{ $o->items->count() }} phòng/cửa</span>
                                </td>
                                <td>
                                    <strong style="color: #d97706; font-size: 14px;">{{ $o->formatted_total }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $o->payment_status === 'paid' ? 'badge-success' : ($o->payment_status === 'partially_paid' ? 'badge-info' : 'badge-warning') }}">
                                        {{ $o->payment_status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $o->status_badge_class }}">
                                        {{ $o->status_label }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.orders.show', $o->id) }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px;">
                                        <i class="fa-solid fa-eye"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                                    Không tìm thấy đơn đặt rèm nào phù hợp với bộ lọc.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px;">
                {{ $orders->links() }}
            </div>
        </div>
    </form>
</div>
@endsection
