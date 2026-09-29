@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Mã Giảm Giá: ' . $discount->code)
@section('page-title', 'Chỉnh Sửa Mã Giảm Giá')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách mã
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-pen-to-square" style="color: var(--brand);"></i>
                Chỉnh Sửa Voucher: <span style="color: var(--brand); font-weight: 800;">{{ $discount->code }}</span>
            </div>
        </div>

        <form action="{{ route('admin.discounts.update', $discount->id) }}" method="POST" class="card-body" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mã Coupon (Viết hoa, không dấu) *:</label>
                    <input type="text" name="code" value="{{ old('code', $discount->code) }}" required
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Loại Giảm Giá *:</label>
                    <select name="discount_type" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                        <option value="percent" {{ old('discount_type', $discount->discount_type) === 'percent' ? 'selected' : '' }}>Giảm theo tỷ lệ phần trăm (%)</option>
                        <option value="fixed" {{ old('discount_type', $discount->discount_type) === 'fixed' ? 'selected' : '' }}>Giảm số tiền trực tiếp (VNĐ)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mức Giảm *:</label>
                    <input type="number" name="discount_value" value="{{ old('discount_value', (int)$discount->discount_value) }}" required step="1"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Giảm Tối Đa (VNĐ - chỉ áp dụng cho %):</label>
                    <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount', $discount->max_discount_amount ? (int)$discount->max_discount_amount : '') }}" step="10000"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Đơn Hàng Tối Thiểu (VNĐ):</label>
                    <input type="number" name="min_order_value" value="{{ old('min_order_value', (int)$discount->min_order_value) }}" step="10000"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Giới Hạn Lượt Dùng (Bỏ trống nếu vô hạn):</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $discount->usage_limit) }}" min="1"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Ngày Bắt Đầu:</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $discount->start_date ? $discount->start_date->format('Y-m-d') : '') }}"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Ngày Hết Hạn:</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $discount->end_date ? $discount->end_date->format('Y-m-d') : '') }}"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mô Tả Mã Giảm Giá:</label>
                <input type="text" name="description" value="{{ old('description', $discount->description) }}" placeholder="Vd: Tri ân khách hàng đặt rèm phòng khách"
                       style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Trạng Thái Kích Hoạt:</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                    <option value="active" {{ old('status', $discount->status) === 'active' ? 'selected' : '' }}>Kích hoạt áp dụng</option>
                    <option value="inactive" {{ old('status', $discount->status) === 'inactive' ? 'selected' : '' }}>Tạm ngưng chưa áp dụng</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                    <i class="fa-solid fa-floppy-disk"></i> Cập Nhật Mã Giảm Giá
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
