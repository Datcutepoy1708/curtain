@extends('admin.layouts.app')

@section('title', 'Quản Lý Khách Hàng')
@section('page-title', 'Danh Sách Khách Hàng')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="card" style="margin-bottom: 0; padding: 18px;">
            <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Tổng Khách Hàng</span>
            <div style="font-size: 24px; font-weight: 800; color: var(--brand); margin: 6px 0 2px;">{{ $totalCustomers }}</div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Tài khoản khách hàng</div>
        </div>

        <div class="card" style="margin-bottom: 0; padding: 18px;">
            <span style="font-size: 11px; font-weight: 700; color: var(--on-surface-variant); text-transform: uppercase;">Đang Hoạt Động</span>
            <div style="font-size: 24px; font-weight: 800; color: #16a34a; margin: 6px 0 2px;">{{ $activeCustomers }}</div>
            <div style="font-size: 11px; color: var(--on-surface-variant);">Trạng thái bình thường</div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card" style="margin-bottom: 0; padding: 14px 20px;">
        <form action="{{ route('admin.customers.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm theo họ tên, email, SĐT..."
                   style="min-width: 240px; flex: 1; padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">

            <select name="status" style="padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">
                <option value="">-- Trạng thái tài khoản --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Đã khóa</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="card" style="margin-bottom: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Khách Hàng</th>
                        <th>Email & Số Điện Thoại</th>
                        <th>Số Đơn Hàng</th>
                        <th>Lịch Đo Đạc</th>
                        <th>Ngày Tham Gia</th>
                        <th>Trạng Thái</th>
                        <th style="text-align: right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">
                                        {{ $c->initials }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.customers.show', $c->id) }}" style="font-weight: 700; color: var(--on-surface); text-decoration: none; font-size: 13px;">
                                            {{ $c->name }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 13px;">{{ $c->email }}</div>
                                <div style="font-size: 11px; color: var(--on-surface-variant);"><i class="fa-solid fa-phone"></i> {{ $c->phone ?: 'Chưa cập nhật' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $c->orders_count }} đơn đặt rèm</span>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $c->consultations_count }} lịch đo</span>
                            </td>
                            <td style="font-size: 12px; color: var(--on-surface-variant);">
                                {{ $c->created_at->format('d/m/Y') }}
                            </td>
                            <td>
                                <form action="{{ route('admin.customers.toggle', $c->id) }}" method="POST" style="margin: 0; display: inline;">
                                    @csrf
                                    <button type="submit" class="badge {{ $c->status === 'active' ? 'badge-success' : 'badge-danger' }}" style="border: none; cursor: pointer; padding: 4px 10px;">
                                        {{ $c->status === 'active' ? 'Hoạt động' : 'Đã khóa' }}
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.customers.show', $c->id) }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 10px;">
                                    Lịch sử &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                                Không tìm thấy khách hàng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 16px 20px;">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
