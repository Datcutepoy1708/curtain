@extends('admin.layouts.app')

@section('title', 'Mã Giảm Giá & Phiếu Mua Hàng')
@section('page-title', 'Quản Lý Mã Giảm Giá (Coupons)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--on-surface); margin: 0;">Danh Sách Mã Ưu Đãi Giảm Giá</h2>
            <p style="color: var(--on-surface-variant); font-size: 0.85rem; margin: 4px 0 0;">Thiết lập khuyến mãi chiết khấu phần trăm (%) hoặc giảm tiền trực tiếp cho khách hàng.</p>
        </div>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Tạo Mã Giảm Giá Mới
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="margin-bottom: 0; padding: 14px 20px;">
        <form action="{{ route('admin.discounts.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm mã code (Vd: REM2026, SALEMUAHE)..."
                   style="min-width: 220px; flex: 1; padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">

            <select name="status" style="padding: 8px 12px; border-radius: var(--radius-md); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 13px; outline: none;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang áp dụng</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đang tạm ngưng</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
        </form>
    </div>

    <!-- Discounts Data Table -->
    <div class="card" style="margin-bottom: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã Voucher</th>
                        <th>Mô Tả / Chiến Dịch</th>
                        <th>Mức Giảm</th>
                        <th>Đơn Tối Thiểu</th>
                        <th>Lượt Sử Dụng</th>
                        <th>Hạn Áp Dụng</th>
                        <th>Trạng Thái</th>
                        <th style="text-align: right;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discounts as $d)
                        <tr>
                            <td>
                                <span class="badge badge-purple" style="font-size: 13px; font-weight: 800; font-family: monospace; letter-spacing: 0.05em; padding: 4px 10px;">
                                    {{ $d->code }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 13px; color: var(--on-surface);">{{ $d->description ?: 'Không có mô tả' }}</strong>
                            </td>
                            <td>
                                @if($d->discount_type === 'percent')
                                    <strong style="color: #16a34a; font-size: 14px;">-{{ (int)$d->discount_value }}%</strong>
                                    @if($d->max_discount_amount)
                                        <small style="color: var(--on-surface-variant); display: block;">Tối đa {{ number_format($d->max_discount_amount, 0, ',', '.') }}₫</small>
                                    @endif
                                @else
                                    <strong style="color: #16a34a; font-size: 14px;">-{{ number_format($d->discount_value, 0, ',', '.') }}₫</strong>
                                @endif
                            </td>
                            <td>
                                {{ $d->min_order_value > 0 ? number_format($d->min_order_value, 0, ',', '.') . '₫' : 'Không giới hạn' }}
                            </td>
                            <td>
                                <strong>{{ $d->used_count }}</strong> / {{ $d->usage_limit ? $d->usage_limit : '∞' }}
                            </td>
                            <td>
                                @if($d->end_date)
                                    <div style="font-size: 12px;">Đến {{ $d->end_date->format('d/m/Y') }}</div>
                                    @if(now()->gt($d->end_date))
                                        <span class="badge badge-danger" style="font-size: 10px;">Đã hết hạn</span>
                                    @endif
                                @else
                                    <span style="color: var(--on-surface-variant); font-size: 12px;">Vô thời hạn</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.discounts.toggle', $d->id) }}" method="POST" style="margin: 0; display: inline;">
                                    @csrf
                                    <button type="submit" class="badge {{ $d->status === 'active' ? 'badge-success' : 'badge-neutral' }}" style="border: none; cursor: pointer; padding: 5px 10px;">
                                        {{ $d->status === 'active' ? 'Đang kích hoạt' : 'Tạm ngưng' }}
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="{{ route('admin.discounts.edit', $d->id) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px;" title="Chỉnh sửa mã">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('admin.discounts.destroy', $d->id) }}" method="POST" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; padding: 4px 8px;" onclick="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                                Chưa có mã giảm giá nào. Nhấp vào nút "Tạo Mã Giảm Giá Mới" để tạo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding: 16px 20px;">
            {{ $discounts->links() }}
        </div>
    </div>
</div>
@endsection
