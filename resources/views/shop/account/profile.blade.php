@extends('shop.account.layout')

@section('title', 'Hồ Sơ Cá Nhân')
@section('breadcrumb-active', 'Hồ Sơ Của Tôi')

@section('account-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- KPI Summary Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
        <div style="background: #fff; border: 1px solid var(--border-line); border-radius: 12px; padding: 18px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Tổng Đơn Hàng</div>
            <div style="font-size: 24px; font-weight: 800; color: var(--primary, #b8935c); margin: 6px 0 2px;">{{ $ordersCount }}</div>
            <div style="font-size: 12px; color: var(--text-muted);">Đơn may đo rèm cửa</div>
        </div>

        <div style="background: #fff; border: 1px solid var(--border-line); border-radius: 12px; padding: 18px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Lịch Đo Đạc</div>
            <div style="font-size: 24px; font-weight: 800; color: #2563eb; margin: 6px 0 2px;">{{ $consultationsCount }}</div>
            <div style="font-size: 12px; color: var(--text-muted);">Cuộc hẹn khảo sát tận nhà</div>
        </div>

        <div style="background: #fff; border: 1px solid var(--border-line); border-radius: 12px; padding: 18px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Trạng Thái Tài Khoản</div>
            <div style="font-size: 15px; font-weight: 800; color: #16a34a; margin: 10px 0 2px;">
                <i class="fa-solid fa-circle-check"></i> Đang hoạt động
            </div>
            <div style="font-size: 12px; color: var(--text-muted);">Đã xác thực bảo mật</div>
        </div>
    </div>

    <!-- Personal Info Card -->
    <div class="account-content-card">
        <div class="content-header">
            <h2 class="content-title">
                <i class="fa-solid fa-address-card" style="color: var(--primary, #b8935c);"></i> Thông Tin Cá Nhân
            </h2>
        </div>

        <form action="{{ route('customer.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Họ và Tên *</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Số Điện Thoại Liên Hệ</label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="0912345678">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Địa Chỉ Email (Không thể thay đổi)</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled style="background: #f8fafc; color: #64748b; cursor: not-allowed;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Link Ảnh Đại Diện (Avatar URL)</label>
                    <input type="url" name="avatar" class="form-control" value="{{ old('avatar', $user->avatar) }}" placeholder="https://images.unsplash.com/...">
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Địa Chỉ Nhận Hàng & Lắp Đặt Mặc Định</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố">
            </div>

            <button type="submit" class="btn-book-survey" style="font-size: 14px; cursor: pointer; border: none;">
                <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi Hồ Sơ
            </button>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="account-content-card">
        <div class="content-header">
            <h2 class="content-title">
                <i class="fa-solid fa-shield-halved" style="color: #0284c7;"></i> Đổi Mật Khẩu Đăng Nhập
            </h2>
        </div>

        @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecdd3; color: #b91c1c; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customer.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="max-width: 450px; display: flex; flex-direction: column; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mật Khẩu Hiện Tại *</label>
                    <input type="password" name="current_password" class="form-control" required placeholder="Nhập mật khẩu cũ">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Mật Khẩu Mới *</label>
                    <input type="password" name="password" class="form-control" required placeholder="Tối thiểu 6 ký tự">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Xác Nhận Mật Khẩu Mới *</label>
                    <input type="password" name="password_confirmation" class="form-control" required placeholder="Nhập lại mật khẩu mới">
                </div>
            </div>

            <button type="submit" class="btn-wishlist" style="background: var(--text-main); color: #fff; border: none; font-size: 14px; cursor: pointer;">
                <i class="fa-solid fa-key"></i> Cập Nhật Mật Khẩu
            </button>
        </form>
    </div>

</div>
@endsection
