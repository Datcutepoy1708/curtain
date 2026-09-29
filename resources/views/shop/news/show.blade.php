<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} | CurtainLux Cẩm Nang</title>
    <meta name="description" content="{{ $article->excerpt ?: Str::limit(strip_tags($article->content), 160) }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- External Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat-widget.css') }}">
</head>
<body>

    @include('shop.partials.header')

    <!-- Main Container -->
    <main class="shop-container" style="padding-top: 24px; padding-bottom: 70px;">
        <!-- Breadcrumbs -->
        <nav class="breadcrumb-trail" aria-label="Breadcrumb">
            <a href="{{ route('shop.index') }}"><i class="fa-solid fa-house"></i> Trang Chủ</a>
            <i class="fa-solid fa-angle-right"></i>
            <a href="{{ route('news.index') }}">Cẩm Nang Chọn Rèm</a>
            <i class="fa-solid fa-angle-right"></i>
            <span>{{ Str::limit($article->title, 45) }}</span>
        </nav>

        <div class="news-detail-layout">
            <!-- Article Main Area -->
            <article class="article-content-wrapper">
                <header class="article-header">
                    <div class="article-category-badge">
                        @if($article->category == 'guide')
                            <i class="fa-solid fa-ruler-combined"></i> Cẩm Nang Thực Tế
                        @elseif($article->category == 'trends')
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Xu Hướng Kiến Trúc
                        @else
                            <i class="fa-solid fa-newspaper"></i> Tin Tức & Khuyến Mãi
                        @endif
                    </div>

                    <h1 class="article-main-title">{{ $article->title }}</h1>

                    <div class="article-meta-bar">
                        <div class="meta-item author">
                            <i class="fa-solid fa-user-pen"></i>
                            <span>{{ $article->author_name }}</span>
                        </div>
                        <div class="meta-item date">
                            <i class="fa-regular fa-calendar"></i>
                            <span>{{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="meta-item views">
                            <i class="fa-regular fa-eye"></i>
                            <span>{{ $article->views }} lượt đọc</span>
                        </div>
                    </div>

                    @if($article->excerpt)
                        <div class="article-lead-box">
                            <i class="fa-solid fa-quote-left quote-icon"></i>
                            <p>{{ $article->excerpt }}</p>
                        </div>
                    @endif
                </header>

                <!-- Featured Image -->
                @if($article->thumbnail)
                    <figure class="article-featured-media">
                        <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}">
                    </figure>
                @endif

                <!-- Article Body Content -->
                <div class="article-rich-text">
                    {!! $article->content !!}
                </div>

                <!-- In-Article Consultation Callout Banner -->
                <div class="article-callout-banner">
                    <div class="callout-icon">
                        <i class="fa-solid fa-person-shelter"></i>
                    </div>
                    <div class="callout-info">
                        <h3>Bạn Đang Cần Tư Vấn Mẫu Rèm Tương Tự Cho Không Gian?</h3>
                        <p>Kỹ thuật viên của chúng tôi sẽ mang mẫu vải tận nơi, đo đạc kích thước từng ô cửa và lên dự toán chi phí chi tiết <strong>hoàn toàn miễn phí</strong>.</p>
                        <div class="callout-actions">
                            <button type="button" onclick="openSurveyModal()" class="btn-cta-gold">
                                <i class="fa-solid fa-calendar-check"></i> Đặt Lịch Đo Cửa Miễn Phí
                            </button>
                            <a href="tel:0912345678" class="btn-cta-outline">
                                <i class="fa-solid fa-phone"></i> Hotline: 0912.345.678
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Article Footer / Share -->
                <div class="article-footer-bar">
                    <div class="article-tags">
                        <span class="tag-title"><i class="fa-solid fa-tags"></i> Từ khóa:</span>
                        <a href="{{ route('news.index', ['search' => 'rèm cửa']) }}" class="tag-link">#rem_cua_dep</a>
                        <a href="{{ route('news.index', ['search' => 'nội thất']) }}" class="tag-link">#noi_that_japandi</a>
                        <a href="{{ route('news.index', ['search' => 'chống nắng']) }}" class="tag-link">#chong_nang_hieu_qua</a>
                    </div>
                    <a href="{{ route('news.index') }}" class="btn-back-news">
                        <i class="fa-solid fa-arrow-left"></i> Quay lại danh mục cẩm nang
                    </a>
                </div>
            </article>

            <!-- Sidebar -->
            <aside class="news-sidebar-col">
                <!-- Survey CTA Box -->
                <div class="news-cta-card">
                    <div class="cta-icon"><i class="fa-solid fa-ruler"></i></div>
                    <h3>Khảo Sát Ô Cửa Tận Nơi</h3>
                    <p>CurtainLux hỗ trợ mang đầy đủ mẫu vải tận nhà, đo đạc và tư vấn thiết kế miễn phí 100% trong 24h.</p>
                    <button type="button" onclick="openSurveyModal()" class="btn-cta-gold">
                        <i class="fa-solid fa-calendar-check"></i> Đặt Lịch Ngay
                    </button>
                    <span class="cta-hotline">Hotline: <strong>0912.345.678</strong></span>
                </div>

                <!-- Related Articles -->
                <div class="news-widget-card">
                    <div class="widget-header">
                        <h3><i class="fa-solid fa-book-bookmark" style="color: var(--primary);"></i> Bài Viết Cùng Chuyên Mục</h3>
                    </div>
                    <div class="widget-list">
                        @forelse($relatedNews as $rel)
                            <a href="{{ route('news.show', $rel->slug) }}" class="recent-article-item">
                                <img src="{{ $rel->thumbnail ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=150&q=80' }}" alt="{{ $rel->title }}">
                                <div class="recent-content">
                                    <h4>{{ $rel->title }}</h4>
                                    <span class="recent-date"><i class="fa-regular fa-clock"></i> {{ $rel->published_at ? $rel->published_at->format('d/m/Y') : $rel->created_at->format('d/m/Y') }}</span>
                                </div>
                            </a>
                        @empty
                            <p style="font-size: 13px; color: var(--text-muted); padding: 12px 0;">Chưa có bài viết liên quan khác.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <!-- Home Survey Booking Modal -->
    <div id="surveyModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="fa-solid fa-calendar-check"></i> Đặt Lịch Khảo Sát & Mang Mẫu Tận Nhà</h3>
                <button type="button" class="btn-close-modal" onclick="closeSurveyModal()">&times;</button>
            </div>
            <form action="{{ route('consultation.store') }}" method="POST" class="modal-body">
                @csrf
                <p class="modal-desc">Chuyên viên CurtainLux sẽ mang đầy đủ cây mẫu vải thực tế đến tận nơi, tư vấn phối màu hợp không gian và đo đạc chuẩn xác <strong>hoàn toàn miễn phí</strong>.</p>
                
                <div class="modal-field">
                    <label>Họ và Tên Của Bạn *</label>
                    <input type="text" name="customer_name" required placeholder="Ví dụ: Anh Hoàng / Chị Mai">
                </div>

                <div class="form-group-row">
                    <div class="modal-field">
                        <label>Số Điện Thoại (Zalo) *</label>
                        <input type="tel" name="customer_phone" required placeholder="09xx xxx xxx">
                    </div>
                    <div class="modal-field">
                        <label>Số Lượng Cửa Cần Làm</label>
                        <input type="number" name="estimated_windows" value="2" min="1" max="50">
                    </div>
                </div>

                <div class="modal-field">
                    <label>Địa Chỉ Nhà Cần Khảo Sát *</label>
                    <input type="text" name="address" required placeholder="Số nhà, tên đường, khu đô thị...">
                </div>

                <div class="form-group-row">
                    <div class="modal-field">
                        <label>Ngày Hẹn Mong Muốn *</label>
                        <input type="date" name="preferred_date" required value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>
                    <div class="modal-field">
                        <label>Khung Giờ Thuận Tiện</label>
                        <select name="preferred_time">
                            <option value="Sáng (08:30 - 11:30)">Buổi Sáng (08:30 - 11:30)</option>
                            <option value="Chiều (14:00 - 17:30)">Buổi Chiều (14:00 - 17:30)</option>
                            <option value="Tối (18:00 - 20:30)">Buổi Tối (18:00 - 20:30)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-submit-survey">
                    <i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu Khảo Sát Miễn Phí
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} CurtainLux - Thương Hiệu Rèm Cửa & Nội Thất Vải Tinh Tế. Hotline: 0912.345.678</p>
    </footer>

    <!-- Floating Live Chatbot Widget -->
    @include('shop.partials.chat-widget')

    <script src="{{ asset('js/shop.js') }}"></script>
    <script src="{{ asset('js/chat-widget.js') }}"></script>
</body>
</html>
