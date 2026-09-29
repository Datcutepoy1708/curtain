<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không Tìm Thấy Trang | CurtainLux</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/error-page.css') }}">
</head>
<body class="error-page-body">
    <div class="error-container">
        <div class="error-card">
            <div class="error-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                Trang Không Tồn Tại (404 Not Found)
            </div>

            <div class="error-illustration">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    <path d="M10 10l4 4m0-4l-4 4"></path>
                </svg>
            </div>

            <h1 class="error-title">Không Tìm Thấy Trang Yêu Cầu</h1>
            
            <p class="error-description">
                Đường dẫn bạn vừa truy cập có thể đã được thay đổi, xóa bỏ hoặc tạm thời không khả dụng trên hệ thống rèm cao cấp CurtainLux.
            </p>

            <div class="error-countdown-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Tự động chuyển về trang chủ sau <span id="countdown" class="countdown-number">5</span> giây
            </div>

            <div class="error-actions">
                <a href="{{ route('shop.index') }}" class="btn-err-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Về Trang Chủ Ngay
                </a>
                <button type="button" class="btn-err-outline" data-action="go-back">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Quay Lại Trang Trước
                </button>
            </div>

            <div class="error-quick-links">
                <span>Khám phá:</span>
                <a href="{{ route('shop.index') }}">Tất Cả Mẫu Rèm</a>
                <a href="{{ route('wishlist.index') }}">Rèm Đã Lưu</a>
                <a href="{{ route('cart.index') }}">Giỏ Hàng & Báo Giá</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/error-page.js') }}"></script>
</body>
</html>
