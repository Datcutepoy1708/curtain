@extends('admin.layouts.app')

@section('title', 'Cài Đặt Hệ Thống')
@section('page-title', 'Cấu Hình & Cài Đặt Hệ Thống CurtainLux')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <form action="{{ route('admin.settings.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf

        <!-- Store Information -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-store" style="color: var(--brand);"></i>
                    Thông Tin Showroom & Cửa Hàng
                </div>
            </div>
            <div class="card-body" style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Tên Thương Hiệu:</label>
                        <input type="text" name="store_name" value="{{ $settings['store_name'] ?? 'CurtainLux - Rèm Cửa Cao Cấp' }}"
                               style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Hotline Tư Vấn / Đặt Lịch:</label>
                        <input type="text" name="store_hotline" value="{{ $settings['store_hotline'] ?? '0988.123.456' }}"
                               style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Địa Chỉ Showroom Trưng Bày:</label>
                    <input type="text" name="store_address" value="{{ $settings['store_address'] ?? 'Số 168 Đường Nguyễn Văn Trỗi, Phường 8, Quận Phú Nhuận, TP. Hồ Chí Minh' }}"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Email Liên Hệ:</label>
                        <input type="email" name="store_email" value="{{ $settings['store_email'] ?? 'contact@curtainlux.vn' }}"
                               style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Giờ Mở Cửa / Tiếp Khách:</label>
                        <input type="text" name="store_hours" value="{{ $settings['store_hours'] ?? '08:00 - 20:30 (Cả Thứ 7 & Chủ Nhật)' }}"
                               style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Transfer Details & Toggle Switch -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div class="card-title">
                    <i class="fa-solid fa-building-columns" style="color: #16a34a;"></i>
                    Phương Thức Thanh Toán Ngân Hàng (VietQR)
                </div>
                <!-- Toggle Switch -->
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0; background: #f8fafc; padding: 6px 14px; border-radius: 999px; border: 1px solid var(--border);">
                    <span style="font-size: 13px; font-weight: 800; color: {{ ($settings['enable_bank_transfer'] ?? '1') !== '0' ? '#16a34a' : '#dc2626' }};">
                        {{ ($settings['enable_bank_transfer'] ?? '1') !== '0' ? 'ĐANG BẬT' : 'ĐÃ TẮT' }}
                    </span>
                    <input type="checkbox" name="enable_bank_transfer" value="1" {{ ($settings['enable_bank_transfer'] ?? '1') !== '0' ? 'checked' : '' }}
                           style="width: 20px; height: 20px; accent-color: #16a34a; cursor: pointer;">
                </label>
            </div>
            <div class="card-body" style="display: flex; flex-direction: column; gap: 16px;">
                <div style="background: #f8fafc; padding: 12px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border); font-size: 13px; color: var(--on-surface-variant); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-info" style="color: #2563eb; font-size: 16px;"></i>
                    <span><strong>Công tắc Bật/Tắt:</strong> Khi <strong>TẮT</strong>, tùy chọn Chuyển khoản ngân hàng (VietQR) sẽ tự động bị ẩn khỏi trang đặt hàng của khách hàng.</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Tên Ngân Hàng:</label>
                        <input type="text" name="bank_name" value="{{ $settings['bank_name'] ?? 'MBBank (Quân Đội)' }}"
                               style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Số Tài Khoản:</label>
                        <input type="text" name="bank_account_number" value="{{ $settings['bank_account_number'] ?? '' }}" placeholder="Số tài khoản nhận thanh toán"
                               style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px; font-weight: 700;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mã ngân hàng VietQR (ví dụ MB):</label>
                    <input type="text" name="bank_id" value="{{ $settings['bank_id'] ?? '' }}" placeholder="MB" maxlength="12" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: var(--radius-sm);">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Chủ Tài Khoản (Viết hoa không dấu):</label>
                    <input type="text" name="bank_account_name" value="{{ $settings['bank_account_name'] ?? '' }}" placeholder="Tên chủ tài khoản nhận cọc"
                           style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px; font-weight: 700;">
                </div>
            </div>
        </div>


        <!-- Installation & Warranty Policy -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-shield-halved" style="color: #7c3aed;"></i>
                    Chính Sách Đo Đạc, Lắp Đặt & Bảo Hành
                </div>
            </div>
            <div class="card-body" style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Chính Sách Khảo Sát & Đo Đạc:</label>
                    <textarea name="policy_survey" rows="2"
                              style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px; line-height: 1.5;">{{ $settings['policy_survey'] ?? 'Miễn phí 100% dịch vụ mang cây mẫu vải tận nhà và tư vấn kích thước lọt lòng/phủ bì trong nội thành.' }}</textarea>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Chính Sách Bảo Hành Rèm:</label>
                    <textarea name="policy_warranty" rows="2"
                              style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--on-surface); font-size: 14px; line-height: 1.5;">{{ $settings['policy_warranty'] ?? 'Bảo hành 3 năm cho phụ kiện ray trượt, động cơ thông minh; bảo hành 2 năm độ bền màu vải và đường may sóng rèm.' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 15px;">
                <i class="fa-solid fa-floppy-disk"></i> Lưu Toàn Bộ Cài Đặt Hệ Thống
            </button>
        </div>
    </form>
</div>
@endsection
