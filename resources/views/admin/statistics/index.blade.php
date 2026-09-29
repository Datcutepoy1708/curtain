@extends('admin.layouts.app')

@section('title', 'Báo Cáo Thống Kê Doanh Thu')
@section('page-title', 'Báo Cáo Thống Kê & Doanh Số Bán Rèm')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- ── Filter Toolbar & Excel Export Bar ── -->
    <div class="card" style="margin-bottom: 0; padding: 16px 20px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <!-- Left: Preset Time Filters -->
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span style="font-size: 13px; font-weight: 700; color: var(--on-surface-variant); margin-right: 4px;">
                    <i class="fa-solid fa-calendar-days" style="color: var(--brand);"></i> Mốc thời gian:
                </span>
                <a href="{{ route('admin.statistics', ['period' => 'today']) }}" 
                   class="btn btn-sm {{ $period === 'today' ? 'btn-primary' : 'btn-secondary' }}" 
                   style="font-weight: 600; padding: 6px 14px;">
                    Trong ngày
                </a>
                <a href="{{ route('admin.statistics', ['period' => 'this_week']) }}" 
                   class="btn btn-sm {{ $period === 'this_week' ? 'btn-primary' : 'btn-secondary' }}" 
                   style="font-weight: 600; padding: 6px 14px;">
                    Trong tuần
                </a>
                <a href="{{ route('admin.statistics', ['period' => '15days']) }}" 
                   class="btn btn-sm {{ $period === '15days' ? 'btn-primary' : 'btn-secondary' }}" 
                   style="font-weight: 600; padding: 6px 14px;">
                    15 ngày qua
                </a>
                <a href="{{ route('admin.statistics', ['period' => '30days']) }}" 
                   class="btn btn-sm {{ $period === '30days' ? 'btn-primary' : 'btn-secondary' }}" 
                   style="font-weight: 600; padding: 6px 14px;">
                    30 ngày qua
                </a>
            </div>

            <!-- Right: Custom Date Range Form & Excel Export Button -->
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <form action="{{ route('admin.statistics') }}" method="GET" style="display: flex; align-items: center; gap: 8px; margin: 0;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <label style="font-size: 12.5px; color: var(--on-surface-variant); margin: 0;">Từ:</label>
                        <input type="date" name="start_date" value="{{ $startDateStr }}" class="form-control" style="padding: 5px 10px; font-size: 13px; width: auto;" required>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <label style="font-size: 12.5px; color: var(--on-surface-variant); margin: 0;">Đến:</label>
                        <input type="date" name="end_date" value="{{ $endDateStr }}" class="form-control" style="padding: 5px 10px; font-size: 13px; width: auto;" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 6px 14px; font-weight: 700;">
                        <i class="fa-solid fa-filter"></i> Lọc dữ liệu
                    </button>
                </form>

                <a href="{{ route('admin.statistics.export-excel', request()->all()) }}" 
                   class="btn btn-success btn-sm" 
                   style="background: #15803d; border-color: #15803d; color: #ffffff; font-weight: 700; padding: 6px 16px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(21, 128, 61, 0.25);" 
                   title="Xuất bảng kê chi tiết ra file Excel (CSV UTF-8 BOM)">
                    <i class="fa-solid fa-file-excel" style="font-size: 14px;"></i> Xuất Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="card" style="margin-bottom: 0; padding: 20px;">
            <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Tổng Thực Thu</span>
            <div style="font-size: 24px; font-weight: 800; color: #16a34a; margin: 6px 0 4px;">
                {{ number_format($totalRevenue, 0, ',', '.') }}₫
            </div>
            <small style="color: var(--on-surface-variant); font-size: 12px;">Theo mốc lọc đã chọn</small>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 20px;">
            <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Tổng Đơn Đặt Rèm</span>
            <div style="font-size: 24px; font-weight: 800; color: var(--brand); margin: 6px 0 4px;">
                {{ $totalOrders }}
            </div>
            <small style="color: var(--on-surface-variant); font-size: 12px;">Trong kỳ báo cáo</small>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 20px;">
            <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Đơn Đã Giao & Lắp Xong</span>
            <div style="font-size: 24px; font-weight: 800; color: #0284c7; margin: 6px 0 4px;">
                {{ $completedOrders }}
            </div>
            <small style="color: #16a34a; font-size: 12px; font-weight: 600;">Tỷ lệ thành công: {{ $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 100 }}%</small>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 20px;">
            <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Giá Trị Đơn Trung Bình</span>
            <div style="font-size: 24px; font-weight: 800; color: #d97706; margin: 6px 0 4px;">
                {{ number_format($avgOrderValue, 0, ',', '.') }}₫
            </div>
            <small style="color: var(--on-surface-variant); font-size: 12px;">Trung bình mỗi đơn trong kỳ</small>
        </div>
    </div>

    <!-- ── Interactive Charts Section (Chart.js Modern Charts) ── -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Chart 1: Revenue & Orders Dual-Axis Trend -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-chart-line" style="color: var(--brand);"></i>
                    Xu Hướng Doanh Thu & Số Đơn Đặt Rèm ({{ $period === 'today' ? 'Trong Ngày Hôm Nay' : ($period === 'this_week' ? 'Trong Tuần Này' : ($period === '15days' ? '15 Ngày Gần Nhất' : ($period === '30days' ? '30 Ngày Gần Nhất' : 'Từ ' . date('d/m/Y', strtotime($startDateStr)) . ' đến ' . date('d/m/Y', strtotime($endDateStr))))) }})
                </div>
                <span style="font-size: 12px; color: var(--on-surface-variant);">Tự động đồng bộ</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="statsRevenueTrendChart"
                            data-chart-labels="{{ json_encode($trendLabels) }}"
                            data-chart-revenue="{{ json_encode($trendRevenues) }}"
                            data-chart-orders="{{ json_encode($trendOrders) }}">
                    </canvas>
                </div>
            </div>
        </div>

        <!-- Chart 2: Category Revenue Breakdown Doughnut -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-chart-pie" style="color: #7c3aed;"></i>
                    Tỷ Trọng Doanh Thu Danh Mục
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container-doughnut">
                    <canvas id="statsCategoryDoughnutChart"
                            data-chart-labels="{{ json_encode($catChartLabels) }}"
                            data-chart-values="{{ json_encode($catChartValues) }}">
                    </canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown and Top Products Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Status Breakdown with Visual Chart -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-list-check" style="color: var(--brand);"></i>
                    Phân Bổ Trạng Thái Đơn Hàng
                </div>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; align-items: center;">
                    <div style="height: 180px;">
                        <canvas id="statsOrderStatusChart"
                                data-chart-labels="{{ json_encode($statusChartLabels) }}"
                                data-chart-values="{{ json_encode($statusChartValues) }}">
                        </canvas>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
                            <span><i class="fa-solid fa-hourglass-start" style="color: #d97706; width: 16px;"></i> Chờ duyệt</span>
                            <strong style="color: #d97706;">{{ $statusCounts['pending'] }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
                            <span><i class="fa-solid fa-check" style="color: #2563eb; width: 16px;"></i> Đã duyệt / Cắt</span>
                            <strong style="color: #2563eb;">{{ $statusCounts['confirmed'] }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
                            <span><i class="fa-solid fa-scissors" style="color: #7c3aed; width: 16px;"></i> Gia công rèm</span>
                            <strong style="color: #7c3aed;">{{ $statusCounts['manufacturing'] }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
                            <span><i class="fa-solid fa-truck" style="color: #0284c7; width: 16px;"></i> Đang giao & lắp</span>
                            <strong style="color: #0284c7;">{{ $statusCounts['shipping'] }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
                            <span><i class="fa-solid fa-circle-check" style="color: #16a34a; width: 16px;"></i> Hoàn thành</span>
                            <strong style="color: #16a34a;">{{ $statusCounts['completed'] }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
                            <span><i class="fa-solid fa-ban" style="color: #dc2626; width: 16px;"></i> Đã hủy</span>
                            <strong style="color: #dc2626;">{{ $statusCounts['cancelled'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Curtain Models -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-fire" style="color: #ea580c;"></i>
                    Top Mẫu Rèm Được Đặt Nhiều Nhất
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên Mẫu Rèm</th>
                            <th>Số Lượng</th>
                            <th style="text-align: right;">Doanh Số</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $item)
                            <tr>
                                <td>
                                    <strong style="font-size: 13px;">{{ $item->product_name }}</strong>
                                </td>
                                <td>{{ $item->total_qty }} bộ/m²</td>
                                <td style="text-align: right; font-weight: 700; color: #16a34a;">
                                    {{ number_format($item->total_sales, 0, ',', '.') }}₫
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 24px; color: var(--on-surface-variant);">
                                    Chưa có đơn hàng nào được ghi nhận.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Category Performance Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-layer-group" style="color: var(--brand);"></i>
                Bảng Thống Kê Doanh Thu Chi Tiết Theo Danh Mục Rèm
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tên Danh Mục Rèm</th>
                        <th>Số Mẫu Rèm Đang Bán</th>
                        <th>Doanh Số Tích Lũy</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryBreakdown as $cat)
                        <tr>
                            <td>
                                <strong style="font-size: 13.5px; color: var(--on-surface);">{{ $cat['name'] }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $cat['products_count'] }} mẫu</span>
                            </td>
                            <td>
                                <strong style="color: var(--brand); font-size: 14px;">{{ number_format($cat['revenue'], 0, ',', '.') }}₫</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 20px;">Chưa có dữ liệu danh mục.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
