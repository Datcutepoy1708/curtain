<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Quyền Truy Cập Bị Giới Hạn | CurtainLux</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/error-page.css') }}">
</head>
<body class="error-page-body">
    <div class="error-container">
        <div class="error-card">
            <div class="error-badge badge-danger">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Truy Cập Bị Từ Chối (403 Forbidden)
            </div>

            <div class="error-illustration">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    <circle cx="12" cy="16" r="1"></circle>
                </svg>
            </div>

            <h1 class="error-title">Khu Vực Hạn Chế Phân Quyền</h1>
            
            <p class="error-description">
                Tài khoản của bạn chưa được cấp thẩm quyền truy cập vào phân hệ này. Vui lòng liên hệ Quản trị viên hệ thống để nâng cấp quyền hoặc phân công nghiệp vụ tương ứng.
            </p>

            <div class="error-actions">
                <a href="{{ route('shop.index') }}" class="btn-err-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Về Trang Chủ CurtainLux
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
                <span>Hỗ trợ nhanh:</span>
                <a href="{{ route('shop.index') }}">Mẫu Rèm Xu Hướng</a>
                <a href="{{ route('wishlist.index') }}">Danh Sách Yêu Thích</a>
                <a href="{{ route('cart.index') }}">Giỏ Hàng & Dự Toán</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/error-page.js') }}"></script>
</body>
</html>
