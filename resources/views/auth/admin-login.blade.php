@extends('layouts.auth')

@section('title', 'Đăng nhập Quản trị viên')

@section('content')
<div class="auth-card" style="border-top: 4px solid var(--accent-cta);">
    <div class="auth-header" style="text-align: center; margin-bottom: 28px;">
        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #2d2621 0%, #b8935c 100%); color: #fff; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 16px; box-shadow: 0 8px 20px rgba(184, 147, 92, 0.25);">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h2 class="auth-title">Cổng Đăng Nhập Quản Trị</h2>
        <p class="auth-subtitle">Dành riêng cho Ban Quản Lý & Nhân Viên Nghiệp Vụ CurtainLux</p>
    </div>

    @if(session('success'))
        <div class="alert-success" style="margin-bottom: 18px;">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email" class="form-label">Email Quản Trị *</label>
            <div class="input-container">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="admin@curtainlux.vn">
            </div>
        </div>

        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <label for="password" class="form-label" style="margin-bottom: 0;">Mật khẩu quản trị *</label>
                <a href="{{ route('admin.password.request') }}" style="font-size: 12px; color: var(--accent-cta); text-decoration: none; font-weight: 600;">
                    Quên mật khẩu?
                </a>
            </div>
            <div class="input-container">
                <input id="password" type="password" name="password" value="" placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password')" aria-label="Hiện/ẩn mật khẩu">
                    <i id="eye-password" class="fa-regular fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #2d2621 0%, #3d352e 100%); border: 1px solid var(--accent-cta); color: #ffffff;">
            <i class="fa-solid fa-lock"></i> Đăng Nhập Quản Trị Hệ Thống
        </button>
    </form>

    <div style="margin-top: 24px; text-align: center; font-size: 13px;">
        <a href="{{ route('shop.index') }}" style="color: var(--text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ khách hàng
        </a>
    </div>
</div>
@endsection
