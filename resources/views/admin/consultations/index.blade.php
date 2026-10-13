@extends('admin.layouts.app')

@section('title', 'Quản Lý Lịch Hẹn Khảo Sát & Tư Vấn')
@section('page-title', 'Danh Sách Lịch Hẹn Khảo Sát & Tư Vấn Toàn Quốc')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; gap: 16px; flex-wrap: wrap;">
    <div>
        <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface);">Lịch Khảo Sát & Tư Vấn Rèm Cửa</h2>
        <p style="color: var(--on-surface-variant); font-size: 0.85rem;">Theo dõi điều phối lịch khảo sát tại nhà và tư vấn từ xa (Video call + Gửi hộp mẫu vải).</p>
    </div>

    <form method="GET" action="{{ route('admin.consultations.index') }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên, SĐT, mã hẹn, tỉnh..." 
               style="padding: 8px 14px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 0.85rem; outline: none;">
        
        <select name="service_mode" onchange="this.form.submit()" 
                style="padding: 8px 14px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 0.85rem; outline: none;">
            <option value="">-- Mọi hình thức phục vụ --</option>
            <option value="home_survey" {{ request('service_mode') === 'home_survey' ? 'selected' : '' }}>🏠 Khảo sát tại nhà</option>
            <option value="remote_consultation" {{ request('service_mode') === 'remote_consultation' ? 'selected' : '' }}>📹 Tư vấn từ xa</option>
        </select>

        <select name="status" onchange="this.form.submit()" 
                style="padding: 8px 14px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 0.85rem; outline: none;">
            <option value="">-- Mọi trạng thái --</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>1. Chờ tiếp nhận</option>
            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>2. Đã phân công thợ</option>
            <option value="surveying" {{ request('status') === 'surveying' ? 'selected' : '' }}>3. Đang khảo sát / Đo đạc</option>
            <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>4. Đã lập báo giá</option>
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
                    <th>Hình Thức</th>
                    <th>Khách Hàng</th>
                    <th>Địa Chỉ & Tỉnh Thành</th>
                    <th>Thời Gian Hẹn</th>
                    <th>Trạng Thái Tiến Trình</th>
                    <th>Nhân Viên Phụ Trách</th>
                    <th style="text-align: right;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultations as $item)
                    @php
                        $badge = $item->status_badge;
                        $modeBadge = $item->service_mode_badge;
                    @endphp
                    <tr>
                        <td>
                            <strong style="color: var(--brand);">{{ $item->code }}</strong>
                        </td>
                        <td>
                            <span style="background: {{ $modeBadge['bg'] }}; color: {{ $modeBadge['color'] }}; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-solid {{ $modeBadge['icon'] }}"></i> {{ $modeBadge['label'] }}
                            </span>
                            @if($item->isRemote())
                                <div style="display: flex; flex-direction: column; gap: 2px; margin-top: 4px;">
                                    @if($item->sample_box_requested)
                                        @php $boxBadge = $item->sample_box_badge; @endphp
                                        <span style="background: {{ $boxBadge['bg'] }}; color: {{ $boxBadge['color'] }}; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; width: fit-content;">
                                            <i class="fa-solid fa-box-open"></i> {{ $boxBadge['label'] }}
                                        </span>
                                    @endif
                                    @if($item->fit_guarantee_eligible)
                                        <span style="background: #dcfce7; color: #15803d; font-size: 10px; font-weight: 800; padding: 2px 6px; border-radius: 4px; width: fit-content;">
                                            <i class="fa-solid fa-shield-check"></i> Bảo hiểm vừa vặn
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $item->customer_name }}</strong>
                            <div style="font-size: 12px; color: var(--on-surface-variant);">
                                <i class="fa-solid fa-phone"></i> {{ $item->customer_phone }}
                            </div>
                        </td>
                        <td style="max-width: 240px;">
                            <div style="font-size: 13px;">{{ $item->address }}</div>
                            <small style="color: var(--brand); font-weight: 700;">
                                <i class="fa-solid fa-location-dot"></i> {{ $item->province_name ?: $item->city }}
                            </small>
                        </td>
                        <td>
                            <div>{{ $item->preferred_date ? $item->preferred_date->format('d/m/Y') : 'Chưa định' }}</div>
                            <small style="color: var(--on-surface-variant);">{{ $item->preferred_time ?: 'Giờ hành chính' }}</small>
                        </td>
                        <td>
                            <span style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px;">
                                {{ $badge['label'] }}
                            </span>
                            @if($item->isRemote() && $item->measurement_status !== 'pending')
                                @php $measBadge = $item->measurement_badge; @endphp
                                <div style="margin-top: 4px;">
                                    <span style="background: {{ $measBadge['bg'] }}; color: {{ $measBadge['color'] }}; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">
                                        {{ $measBadge['label'] }}
                                    </span>
                                </div>
                            @endif
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
                        <td colspan="8" style="padding: 40px; text-align: center; color: var(--on-surface-variant);">
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
