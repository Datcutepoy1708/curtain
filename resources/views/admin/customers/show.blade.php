@extends('admin.layouts.app')

@section('title', 'Hồ Sơ Khách Hàng: ' . $customer->name)
@section('page-title', 'Hồ Sơ & Lịch Sử Đặt Rèm: ' . $customer->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách khách
        </a>
        <form action="{{ route('admin.customers.toggle', $customer->id) }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn {{ $customer->status === 'active' ? 'btn-danger' : 'btn-primary' }} btn-sm">
                {{ $customer->status === 'active' ? 'Khóa tài khoản này' : 'Mở khóa tài khoản' }}
            </button>
        </form>
    </div>

    <!-- Customer Details Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-address-card" style="color: var(--brand);"></i>
                Thông Tin Khách Hàng
            </div>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <small style="color: var(--on-surface-variant); font-size: 12px;">Họ và tên:</small>
                    <div style="font-size: 16px; font-weight: 700;">{{ $customer->name }}</div>
                </div>
                <div>
                    <small style="color: var(--on-surface-variant); font-size: 12px;">Địa chỉ Email:</small>
                    <div style="font-size: 14px;">{{ $customer->email }}</div>
                </div>
                <div>
                    <small style="color: var(--on-surface-variant); font-size: 12px;">Số điện thoại:</small>
                    <div style="font-size: 14px; font-weight: 600; color: var(--brand);">{{ $customer->phone ?: 'Chưa cung cấp' }}</div>
                </div>
                <div>
                    <small style="color: var(--on-surface-variant); font-size: 12px;">Ngày đăng ký:</small>
                    <div style="font-size: 14px;">{{ $customer->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders History -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-cart-flatbed" style="color: #16a34a;"></i>
                Lịch Sử Đặt Rèm ({{ $customer->orders->count() }} đơn hàng)
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Ngày Đặt</th>
                        <th>Số Vị Trí Cửa</th>
                        <th>Tổng Tiền</th>
                        <th>Thanh Toán</th>
                        <th>Trạng Thái</th>
                        <th style="text-align: right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->orders as $o)
                        <tr>
                            <td>
                                <strong style="color: var(--brand); font-size: 13px;">{{ $o->order_code }}</strong>
                            </td>
                            <td>{{ $o->created_at->format('d/m/Y') }}</td>
                            <td>{{ $o->items->count() }} khung cửa</td>
                            <td><strong style="color: #d97706;">{{ $o->formatted_total }}</strong></td>
                            <td>
                                <span class="badge {{ $o->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                                    {{ $o->payment_status_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $o->status_badge_class }}">
                                    {{ $o->status_label }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.orders.show', $o->id) }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 8px;">
                                    Xem đơn &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 24px; color: var(--on-surface-variant);">
                                Khách hàng chưa có đơn đặt rèm nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Consultations History -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-calendar-days" style="color: #7c3aed;"></i>
                Lịch Hẹn Khảo Sát & Đo Rèm Tận Nhà
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã Hẹn</th>
                        <th>Ngày Hẹn</th>
                        <th>Địa Chỉ</th>
                        <th>Trạng Thái</th>
                        <th style="text-align: right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->consultations as $c)
                        <tr>
                            <td><strong style="color: #7c3aed;">{{ $c->code }}</strong></td>
                            <td>{{ $c->preferred_date->format('d/m/Y') }} ({{ $c->preferred_time ?: 'Giờ HC' }})</td>
                            <td>{{ $c->address }}, {{ $c->city }}</td>
                            <td>
                                @php $badge = $c->status_badge; @endphp
                                <span style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 999px;">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('admin.consultations.show', $c->id) }}" class="btn btn-secondary btn-sm" style="font-size: 11px; padding: 4px 8px;">
                                    Chi tiết &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 24px; color: var(--on-surface-variant);">
                                Khách hàng chưa đăng ký lịch khảo sát nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
