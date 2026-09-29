@extends('layouts.auth')

@section('title', 'Đăng nhập tài khoản')

@section('content')
<div class="auth-card">
    <nav class="auth-tabs" role="tablist">
        <a href="{{ route('login') }}" class="active">Đăng Nhập</a>
        <a href="{{ route('register') }}">Đăng Ký</a>
    </nav>

    <div class="auth-header">
        <h2 class="auth-title">Chào mừng trở lại</h2>
        <p class="auth-subtitle">Đăng nhập để theo dõi đơn may rèm và lịch khảo sát tại nhà</p>
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

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email" class="form-label">Email tài khoản *</label>
            <div class="input-container">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com" required autocomplete="email">
            </div>
        </div>

        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <label for="password" class="form-label" style="margin-bottom: 0;">Mật khẩu *</label>
                <a href="{{ route('password.request') }}" style="font-size: 12px; color: var(--accent-cta); text-decoration: none; font-weight: 600;">
                    Quên mật khẩu?
                </a>
            </div>
            <div class="input-container">
                <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password')" aria-label="Hiện/ẩn mật khẩu">
                    <i id="eye-password" class="fa-regular fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập
        </button>
    </form>

    <!-- Social Authentication Section -->
    <div class="social-divider">
        <span>Hoặc đăng nhập nhanh bằng</span>
    </div>

    <div class="social-buttons-grid">
        <a href="{{ route('auth.social.redirect', 'google') }}" class="btn-social btn-social-google" title="Đăng nhập qua Google">
            <svg width="18" height="18" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Google</span>
        </a>

        <a href="{{ route('auth.social.redirect', 'facebook') }}" class="btn-social btn-social-facebook" title="Đăng nhập qua Facebook">
            <i class="fa-brands fa-facebook" style="font-size: 18px;"></i>
            <span>Facebook</span>
        </a>

        <a href="{{ route('auth.social.redirect', 'zalo') }}" class="btn-social btn-social-zalo" title="Đăng nhập qua Zalo">
            <span style="font-weight: 900; letter-spacing: -0.5px; font-size: 14px;">Zalo</span>
        </a>
    </div>

    <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border-line); text-align: center; font-size: 13px; color: var(--text-muted);">
        Bạn là cán bộ / quản trị viên? 
        <a href="{{ route('admin.login') }}" style="color: var(--accent-cta); font-weight: 700; text-decoration: none;">
            Cổng Đăng Nhập Quản Trị &rarr;
        </a>
    </div>
</div>
@endsection
