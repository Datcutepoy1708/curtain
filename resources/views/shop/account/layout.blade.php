<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tài Khoản Của Tôi') | CurtainLux</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <style>
        .account-wrapper { display: grid; grid-template-columns: 280px 1fr; gap: 30px; margin: 30px 0 60px; }
        @media (max-width: 900px) { .account-wrapper { grid-template-columns: 1fr; } }
        
        .account-sidebar { background: #fff; border: 1px solid var(--border-line); border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); height: fit-content; }
        .user-summary { text-align: center; padding-bottom: 20px; border-bottom: 1px solid var(--border-line); margin-bottom: 20px; }
        .user-avatar-img { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary, #b8935c); margin-bottom: 12px; }
        .user-avatar-placeholder { width: 72px; height: 72px; border-radius: 50%; background: #eff6ff; color: #2563eb; font-size: 26px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
        .user-name-title { font-size: 16px; font-weight: 800; color: var(--text-main); }
        .user-email-text { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        .account-menu { display: flex; flex-direction: column; gap: 6px; }
        .account-menu-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; color: var(--text-muted); text-decoration: none; transition: all 0.2s; }
        .account-menu-item:hover, .account-menu-item.active { background: #fdf8f0; color: var(--primary, #b8935c); }
        .account-menu-item.active { font-weight: 700; border-left: 3px solid var(--primary, #b8935c); }

        .account-content-card { background: #fff; border: 1px solid var(--border-line); border-radius: 12px; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); }
        .content-header { padding-bottom: 16px; border-bottom: 1px solid var(--border-line); margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .content-title { font-size: 20px; font-weight: 800; color: var(--text-main); }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border-line); border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s; }
        .form-control:focus { border-color: var(--primary, #b8935c); }
    </style>
</head>
<body>
    @include('shop.partials.header')

    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="margin-top: 20px;">
            <a href="{{ route('shop.index') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <span>/</span>
            <span style="color: var(--text-main); font-weight: 600;">Tài Khoản Khách Hàng</span>
            @hasSection('breadcrumb-active')
                <span>/</span>
                <span style="color: var(--primary, #b8935c); font-weight: 700;">@yield('breadcrumb-active')</span>
            @endif
        </div>

        @if(session('success'))
            <div class="alert-success" style="margin-bottom: 20px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-danger" style="margin-bottom: 20px; background: #fef2f2; border: 1px solid #fecdd3; color: #b91c1c; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="account-wrapper" @guest style="display: block;" @endguest>
            <!-- Sidebar -->
            @auth
            <aside class="account-sidebar">
                <div class="user-summary">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="user-avatar-img">
                    @else
                        <div class="user-avatar-placeholder">{{ auth()->user()->initials }}</div>
                    @endif
                    <div class="user-name-title">{{ auth()->user()->name }}</div>
                    <div class="user-email-text">{{ auth()->user()->email }}</div>
                    <div style="margin-top: 8px;">
                        <span style="background: #e0f2fe; color: #0284c7; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700;">
                            <i class="fa-solid fa-user-shield"></i> Khách Hàng Thân Thiết
                        </span>
                    </div>
                </div>

                <nav class="account-menu">
                    <a href="{{ route('customer.profile') }}" class="account-menu-item {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
                        <i class="fa-solid fa-id-card"></i> Hồ Sơ Của Tôi
                    </a>
                    <a href="{{ route('customer.orders') }}" class="account-menu-item {{ request()->routeIs('customer.orders*') ? 'active' : '' }}">
                        <i class="fa-solid fa-cart-flatbed"></i> Đơn Hàng May Rèm
                    </a>
                    <a href="{{ route('customer.consultations') }}" class="account-menu-item {{ request()->routeIs('customer.consultations') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-check"></i> Lịch Hẹn Khảo Sát
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="account-menu-item">
                        <i class="fa-solid fa-heart" style="color: #ef4444;"></i> Rèm Yêu Thích
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="margin-top: 10px; border-top: 1px solid var(--border-line); padding-top: 10px;">
                        @csrf
                        <button type="submit" class="account-menu-item" style="width: 100%; border: none; background: none; cursor: pointer; color: #dc2626;">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng Xuất
                        </button>
                    </form>
                </nav>
            </aside>
            @endauth

            <!-- Main Account Content -->
            <main class="account-content">
                @yield('account-content')
            </main>
        </div>
    </div>

    @include('shop.partials.footer')
</body>
</html>
