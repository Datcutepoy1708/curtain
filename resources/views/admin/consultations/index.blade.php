@extends('admin.layouts.app')

@section('title', 'Quản Lý Lịch Hẹn Khảo Sát Tận Nhà')
@section('page-title', 'Danh Sách Lịch Hẹn Khảo Sát Tận Nhà')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;">
    <div>
        <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface);">Lịch Khảo Sát & Đo Rèm Tận Nhà</h2>
        <p style="color: var(--on-surface-variant); font-size: 0.85rem;">Theo dõi danh sách khách hàng đăng ký mang cây mẫu vải thực tế và đo đạc tại nhà.</p>
    </div>

    <form method="GET" action="{{ route('admin.consultations.index') }}" style="display: flex; gap: 10px; align-items: center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên, SĐT, mã hẹn..." 
               style="padding: 8px 14px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 0.85rem; outline: none;">
        <select name="status" onchange="this.form.submit()" 
                style="padding: 8px 14px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 0.85rem; outline: none;">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>1. Chờ tiếp nhận</option>
            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>2. Đã phân công thợ</option>
            <option value="surveying" {{ request('status') === 'surveying' ? 'selected' : '' }}>3. Đang khảo sát</option>
            <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>4. Đã báo giá</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>5. Hoàn thành</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>6. Đã hủy</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-filter"></i> Lọc
        </button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Mã Lịch Hẹn</th>
                    <th>Khách Hàng</th>
                    <th>Địa Chỉ Khảo Sát</th>
                    <th>Thời Gian Hẹn</th>
                    <th>Trạng Thái</th>
                    <th>Thợ Phụ Trách</th>
                    <th style="text-align: right;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultations as $item)
                    @php $badge = $item->status_badge; @endphp
                    <tr>
                        <td>
                            <strong style="color: var(--brand);">{{ $item->code }}</strong>
                        </td>
                        <td>
                            <strong>{{ $item->customer_name }}</strong>
                            <div style="font-size: 12px; color: var(--on-surface-variant);"><i class="fa-solid fa-phone"></i> {{ $item->customer_phone }}</div>
                        </td>
                        <td style="max-width: 250px;">
                            <div style="font-size: 13px;">{{ $item->address }}</div>
                            <small style="color: var(--on-surface-variant);">{{ $item->district ? $item->district . ', ' : '' }}{{ $item->city }}</small>
                        </td>
                        <td>
                            <div>{{ $item->preferred_date->format('d/m/Y') }}</div>
                            <small style="color: var(--on-surface-variant);">{{ $item->preferred_time ?: 'Giờ hành chính' }}</small>
                        </td>
                        <td>
                            <span style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px;">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td style="font-size: 13px;">
                            {{ $item->staff->name ?? 'Chưa phân công' }}
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('admin.consultations.show', $item->id) }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i> Chi Tiết & Xử Lý
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: var(--on-surface-variant);">
                            Chưa có lịch hẹn khảo sát nào được ghi nhận.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding: 16px 20px;">
        {{ $consultations->links() }}
    </div>
</div>
@endsection
