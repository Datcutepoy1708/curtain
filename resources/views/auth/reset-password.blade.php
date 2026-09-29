@extends('layouts.auth')

@section('title', 'Thiết Lập Mật Khẩu Mới')

@section('content')
<div class="auth-card">
    <div class="auth-header" style="text-align: center; margin-bottom: 24px;">
        <div style="width: 58px; height: 58px; background: var(--bg-subtle); color: var(--accent-cta); border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 14px;">
            <i class="fa-solid fa-lock-open"></i>
        </div>
        <h2 class="auth-title">Đặt Lại Mật Khẩu Mới</h2>
        <p class="auth-subtitle">Vui lòng nhập mật khẩu bảo mật mới cho tài khoản của bạn</p>
    </div>

    @if($errors->any())
        <div class="alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email" class="form-label">Email tài khoản *</label>
            <div class="input-container">
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required readonly style="background: var(--bg-subtle); cursor: not-allowed;">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Mật khẩu mới *</label>
            <div class="input-container">
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Tối thiểu 6 ký tự">
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password')" aria-label="Hiện/ẩn mật khẩu">
                    <i id="eye-password" class="fa-regular fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới *</label>
            <div class="input-container">
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu mới">
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password_confirmation')" aria-label="Hiện/ẩn mật khẩu">
                    <i id="eye-password_confirmation" class="fa-regular fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-check"></i> Lưu Mật Khẩu Mới
        </button>
    </form>

    <div style="margin-top: 24px; text-align: center; font-size: 13px;">
        <a href="{{ route('login') }}" style="color: var(--text-muted); text-decoration: none;">
            &larr; Hủy bỏ và quay lại đăng nhập
        </a>
    </div>
</div>
@endsection
