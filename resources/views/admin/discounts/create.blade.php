@extends('admin.layouts.app')

@section('title', 'Tạo Mã Giảm Giá Mới')
@section('page-title', 'Tạo Mã Giảm Giá Mới')

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
                <i class="fa-solid fa-ticket" style="color: var(--brand);"></i>
                Thông Tin Khuyến Mãi & Mã Voucher
            </div>
        </div>

        <form action="{{ route('admin.discounts.store') }}" method="POST" class="card-body" style="display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mã Coupon (Viết hoa, không dấu) *:</label>
                    <input type="text" name="code" placeholder="Vd: REMXUAN2026" required
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Loại Giảm Giá *:</label>
                    <select name="discount_type" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                        <option value="percent">Giảm theo tỷ lệ phần trăm (%)</option>
                        <option value="fixed">Giảm số tiền trực tiếp (VNĐ)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mức Giảm *:</label>
                    <input type="number" name="discount_value" placeholder="Vd: 10 (cho 10%) hoặc 500000" required step="1"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Giảm Tối Đa (VNĐ - chỉ áp dụng cho %):</label>
                    <input type="number" name="max_discount_amount" placeholder="Vd: 1000000" step="10000"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Đơn Hàng Tối Thiểu (VNĐ):</label>
                    <input type="number" name="min_order_value" value="0" step="10000"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Giới Hạn Lượt Dùng (Bỏ trống nếu vô hạn):</label>
                    <input type="number" name="usage_limit" placeholder="Vd: 100" min="1"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Ngày Bắt Đầu:</label>
                    <input type="date" name="start_date"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Ngày Hết Hạn:</label>
                    <input type="date" name="end_date"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mô Tả Mã Giảm Giá:</label>
                <input type="text" name="description" placeholder="Vd: Tri ân khách hàng đặt rèm phòng khách mùa Tết"
                       style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Trạng Thái Kích Hoạt:</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                    <option value="active">Kích hoạt ngay</option>
                    <option value="inactive">Tạm ngưng chưa áp dụng</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Mã Giảm Giá
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
