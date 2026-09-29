<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tài khoản') — CurtainLux</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Dedicated CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @stack('styles')
</head>
<body>
<div class="auth-split">
    <!-- Left Column: CurtainLux Promo -->
    <aside class="auth-split__promo">
        <div class="promo__content">
            <div class="promo__header">
                <a href="{{ route('shop.index') }}" class="logo">
                    <i class="fa-solid fa-person-shelter"></i>
                    <span>CURTAIN LUX</span>
                </a>
                <span style="font-size: 12px; font-weight: 700; color: var(--accent-cta); background: var(--accent-cta-light); padding: 4px 12px; border-radius: 999px;">
                    Thành Viên VIP
                </span>
            </div>

            <h1 class="promo__heading">Đặc Quyền Hội Viên Rèm Cửa Cao Cấp</h1>

            <div class="promo__benefits-card">
                <ul class="benefits-list">
                    <li><i class="fa-solid fa-circle-check"></i> Miễn phí 100% khảo sát & mang mẫu vải đo tận nhà</li>
                    <li><i class="fa-solid fa-circle-check"></i> Bảo hành động cơ thông minh và phụ kiện ray 5 năm</li>
                    <li><i class="fa-solid fa-circle-check"></i> Chiết khấu thêm 10% cho toàn bộ công trình nhà mới</li>
                    <li><i class="fa-solid fa-circle-check"></i> Lưu lại kích thước từng ô cửa để bảo hành & giặt là</li>
                </ul>
            </div>

            <div>
                <a href="{{ route('shop.index') }}" style="color: var(--primary-hover); font-weight: 700; text-decoration: none; font-size: 14px;">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ catalogue
                </a>
            </div>
        </div>
    </aside>

    <!-- Right Column: Form View -->
    <main class="auth-split__form">
        @yield('content')
    </main>
</div>

<!-- Dedicated Auth JS -->
<script src="{{ asset('js/auth.js') }}"></script>
@stack('scripts')
</body>
</html>
