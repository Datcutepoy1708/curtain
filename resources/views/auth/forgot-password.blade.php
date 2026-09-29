@extends('layouts.auth')

@section('title', $isAdmin ? 'Quên Mật Khẩu Quản Trị' : 'Quên Mật Khẩu Tài Khoản')

@section('content')
<div class="auth-card" style="{{ $isAdmin ? 'border-top: 4px solid var(--accent-cta);' : '' }}">
    <div class="auth-header" style="text-align: center; margin-bottom: 24px;">
        <div style="width: 58px; height: 58px; background: {{ $isAdmin ? 'linear-gradient(135deg, #2d2621 0%, #b8935c 100%)' : 'var(--bg-subtle)' }}; color: {{ $isAdmin ? '#fff' : 'var(--accent-cta)' }}; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 14px;">
            <i class="fa-solid fa-key"></i>
        </div>
        <h2 class="auth-title">{{ $isAdmin ? 'Khôi Phục Mật Khẩu Quản Trị' : 'Quên Mật Khẩu?' }}</h2>
        <p class="auth-subtitle">
            {{ $isAdmin ? 'Nhập email nhân viên/quản trị viên để nhận liên kết xác thực đặt lại mật khẩu' : 'Nhập địa chỉ email đã đăng ký để thiết lập lại mật khẩu tài khoản' }}
        </p>
    </div>

    @if(session('info'))
        <div class="alert-success" style="margin-bottom: 20px; flex-direction: column; align-items: flex-start;">
            <div style="display: flex; align-items: center; gap: 8px; font-weight: 700;">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('status') }}</span>
            </div>
            <p style="margin: 8px 0 0; font-size: 13px; line-height: 1.45;">{{ session('info') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email" class="form-label">Email Đã Đăng Ký *</label>
            <div class="input-container">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="example@curtainlux.vn">
            </div>
        </div>

        <button type="submit" class="btn-submit" style="{{ $isAdmin ? 'background: linear-gradient(135deg, #2d2621 0%, #3d352e 100%); border: 1px solid var(--accent-cta); color: #ffffff;' : '' }}">
            <i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu Thiết Lập Lại
        </button>
    </form>

    <div style="margin-top: 24px; text-align: center; font-size: 13px;">
        @if($isAdmin)
            <a href="{{ route('admin.login') }}" style="color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Quay lại đăng nhập Quản Trị
            </a>
        @else
            <a href="{{ route('login') }}" style="color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-left"></i> Quay lại đăng nhập Khách Hàng
            </a>
        @endif
    </div>
</div>
@endsection
