@extends('admin.layouts.app')

@section('title', 'Bảng Điều Khiển Tổng Quan')
@section('page-title', 'Bảng Điều Khiển Tổng Quan')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- ── Top Header Quick Bar ── -->
    @php
        $currentUser = auth()->user();
        $canStats = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('statistics.view'));
        $canOrders = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('orders.view'));
        $canConsult = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('consultations.view'));
        $canProducts = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('products.view'));
    @endphp
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--on-surface); margin: 0;">Xin chào, {{ $currentUser->name ?? 'Quản trị viên' }}!</h2>
            <p style="font-size: 0.85rem; color: var(--on-surface-variant); margin: 4px 0 0;">Tổng hợp các chỉ số công việc, tiến độ đo đạc và đơn đặt rèm hôm nay.</p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            @if($canStats)
            <a href="{{ route('admin.statistics') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-chart-line"></i> Báo Cáo Doanh Thu
            </a>
            @endif
            @if($canOrders)
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-boxes-packing"></i> Xử Lý Đơn Hàng
            </a>
            @endif
            @if(!$canStats && $canConsult)
            <a href="{{ route('admin.consultations.index') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-calendar-check"></i> Xem Lịch Đo Đạc
            </a>
            @endif
        </div>
    </div>

    <!-- ── Stat KPI Cards Grid ── -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        @if($canStats)
        <!-- Doanh thu thực tế -->
        <div class="card" style="margin-bottom: 0; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase; letter-spacing: 0.05em;">Tổng Doanh Thu</span>
                    <div style="font-size: 24px; font-weight: 800; color: var(--on-surface); margin: 6px 0 4px;">
                        {{ number_format($overview['totalRevenue'], 0, ',', '.') }}₫
                    </div>
                    <small style="color: #16a34a; font-weight: 600; font-size: 12px;">
                        <i class="fa-solid fa-arrow-trend-up"></i> +{{ $overview['revenueGrowthPercent'] }}% so với tháng trước
                    </small>
                </div>
                <div class="kpi-icon-wrap revenue">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
        </div>
        @endif

        @if($canOrders)
        <!-- Đơn đặt rèm -->
        <div class="card" style="margin-bottom: 0; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase; letter-spacing: 0.05em;">Đơn Đặt Rèm</span>
                    <div style="font-size: 24px; font-weight: 800; color: var(--brand); margin: 6px 0 4px;">
                        {{ $overview['totalOrders'] }}
                    </div>
                    <small style="color: #d97706; font-weight: 600; font-size: 12px;">
                        <i class="fa-solid fa-clock"></i> {{ $overview['pendingOrders'] }} đơn cần cắt may / lắp
                    </small>
                </div>
                <div class="kpi-icon-wrap orders">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>
        </div>
        @endif

        @if($canConsult)
        <!-- Lịch khảo sát tận nhà -->
        <div class="card" style="margin-bottom: 0; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase; letter-spacing: 0.05em;">Lịch Đo Đạc Tận Nhà</span>
                    <div style="font-size: 24px; font-weight: 800; color: #7c3aed; margin: 6px 0 4px;">
                        {{ $overview['totalConsultations'] }}
                    </div>
                    <small style="color: #7c3aed; font-weight: 600; font-size: 12px;">
                        <i class="fa-solid fa-calendar-check"></i> {{ $overview['pendingConsultations'] }} cuộc hẹn chờ phân công
                    </small>
                </div>
                <div class="kpi-icon-wrap consultations">
                    <i class="fa-solid fa-person-shelter"></i>
                </div>
            </div>
        </div>
        @endif

        @if($canProducts)
        <!-- Cảnh báo vải / tồn kho -->
        <div class="card" style="margin-bottom: 0; padding: 20px; border-color: {{ $overview['lowStockCount'] > 0 ? '#fca5a5' : 'var(--border)' }};">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase; letter-spacing: 0.05em;">Vải & Phụ Kiện Tồn Thấp</span>
                    <div style="font-size: 24px; font-weight: 800; color: {{ $overview['lowStockCount'] > 0 ? '#dc2626' : 'var(--on-surface)' }}; margin: 6px 0 4px;">
                        {{ $overview['lowStockCount'] }}
                    </div>
                    <small style="color: {{ $overview['lowStockCount'] > 0 ? '#dc2626' : '#16a34a' }}; font-weight: 600; font-size: 12px;">
                        {{ $overview['lowStockCount'] > 0 ? 'Cần đặt xưởng dệt bổ sung' : 'Vải khả dụng dồi dào' }}
                    </small>
                </div>
                <div class="kpi-icon-wrap stock">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if($canStats || $canProducts)
    <!-- ── Middle Row: Revenue Sparkline Chart + Quick Stats ── -->
    <div style="display: grid; grid-template-columns: {{ $canStats && $canProducts ? '2fr 1fr' : '1fr' }}; gap: 24px;">
        @if($canStats)
        <!-- Sparkline Trend Chart Card -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-chart-area" style="color: var(--brand);"></i>
                    Biểu Đồ Doanh Thu Thực Tế (30 Ngày Qua)
                </div>
                <span style="font-size: 12px; color: var(--on-surface-variant);">Tự động cập nhật theo đơn hàng</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="dashboardRevenueChart"
                            data-chart-labels="{{ json_encode($chartDates) }}"
                            data-chart-values="{{ json_encode($chartRevenues) }}">
                    </canvas>
                </div>
            </div>
        </div>
        @endif

        @if($canProducts)
        <!-- Category Breakdown Card -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-layer-group" style="color: #d97706;"></i>
                    Danh Mục Ngành Hàng
                </div>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm" style="font-size: 11px;">Quản lý</a>
            </div>
            <div class="card-body" style="padding: 12px 20px;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($categories as $cat)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px dashed var(--border);">
                            <div>
                                <strong style="font-size: 13px; color: var(--on-surface);">{{ $cat->name }}</strong>
                                <div style="font-size: 11px; color: var(--on-surface-variant);">{{ $cat->products_count }} mẫu rèm đang bán</div>
                            </div>
                            <span class="badge badge-info" style="font-size: 11px;">{{ $cat->products_count }} sản phẩm</span>
                        </div>
                    @empty
                        <p style="color: var(--on-surface-variant); font-size: 13px;">Chưa có danh mục nào.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    @if($canOrders || $canConsult)
    <!-- ── Bottom Row: Recent Orders & Consultations ── -->
    <div style="display: grid; grid-template-columns: {{ $canOrders && $canConsult ? '3fr 2fr' : '1fr' }}; gap: 24px;">
        @if($canOrders)
        <!-- Recent Orders Table -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--brand);"></i>
                    Đơn Đặt Rèm Mới Nhất
                </div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">Xem tất cả đơn &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã Đơn</th>
                            <th>Khách Hàng</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    <strong style="color: var(--brand); font-size: 13px;">{{ $order->order_code }}</strong>
                                    <div style="font-size: 11px; color: var(--on-surface-variant);">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <strong style="font-size: 13px;">{{ $order->customer_name }}</strong>
                                    <div style="font-size: 11px; color: var(--on-surface-variant);"><i class="fa-solid fa-phone"></i> {{ $order->customer_phone }}</div>
                                </td>
                                <td>
                                    <strong style="color: #d97706; font-size: 13px;">{{ $order->formatted_total }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $order->status_badge_class }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 8px;">
                                        Xem &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: var(--on-surface-variant);">
                                    Chưa có đơn hàng nào được đặt.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($canConsult)
        <!-- Pending Consultations List -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-calendar-check" style="color: #7c3aed;"></i>
                    Lịch Hẹn Đo Cần Xử Lý
                </div>
                <a href="{{ route('admin.consultations.index') }}" class="btn btn-secondary btn-sm">Xem lịch &rarr;</a>
            </div>
            <div class="card-body" style="padding: 14px 20px;">
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @forelse($recentConsultations as $c)
                        @php $badge = $c->status_badge; @endphp
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <strong style="font-size: 13px; color: var(--on-surface);">{{ $c->customer_name }}</strong>
                                    <span style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px;">
                                        {{ $badge['label'] }}
                                    </span>
                                </div>
                                <div style="font-size: 12px; color: var(--on-surface-variant); margin-top: 2px;">
                                    <i class="fa-solid fa-phone"></i> {{ $c->customer_phone }} — <i class="fa-solid fa-location-dot"></i> {{ $c->district ?: $c->city }}
                                </div>
                                <div style="font-size: 11px; color: var(--brand); margin-top: 2px;">
                                    <i class="fa-solid fa-clock"></i> Hẹn: {{ $c->preferred_date->format('d/m/Y') }} ({{ $c->preferred_time ?: 'Giờ HC' }})
                                </div>
                            </div>
                            <a href="{{ route('admin.consultations.show', $c->id) }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 8px;">
                                Đo &rarr;
                            </a>
                        </div>
                    @empty
                        <p style="color: var(--on-surface-variant); font-size: 13px; text-align: center; padding: 20px;">
                            Hiện không có lịch hẹn khảo sát nào.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
    </div>
</div>
@endsection
