<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cẩm Nang Chọn Rèm & Xu Hướng Thiết Kế Nội Thất | CurtainLux</title>
    <meta name="description" content="Khám phá các bí quyết chọn rèm cửa, hướng dẫn tự đo đạc chuẩn xác, phong thủy màu sắc rèm cửa và xu hướng rèm Nhật Bản - Hàn Quốc mới nhất 2026.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- External Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat-widget.css') }}">
</head>
<body>

    @include('shop.partials.header')

    <!-- Hero Banner -->
    <div class="news-hero-section">
        <div class="news-hero-content">
            <span class="news-badge-tag"><i class="fa-solid fa-book-open"></i> KIẾN THỨC NỘI THẤT VẢI</span>
            <h1>Cẩm Nang Chọn Rèm Cửa & Xu Hướng Không Gian 2026</h1>
            <p>Tuyển tập cẩm nang hướng dẫn tự đo đạc, cách phối màu rèm hợp phong thủy bản mệnh và các giải pháp chống nắng, cách nhiệt chuẩn kiến trúc hiện đại.</p>

            <!-- Search Bar -->
            <form action="{{ route('news.index') }}" method="GET" class="news-search-box">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm bài viết (vd: cách đo rèm, rèm cản nhiệt, rèm 2 lớp...)">
                <button type="submit">Tìm Kiếm</button>
            </form>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="shop-container" style="padding-top: 30px; padding-bottom: 60px;">
        <!-- Category Filter Tabs -->
        <div class="news-cat-tabs">
            <a href="{{ route('news.index', ['search' => request('search')]) }}" class="news-tab {{ !request('category') ? 'active' : '' }}">
                Tất Cả ({{ $categoryCounts['all'] ?? 0 }})
            </a>
            <a href="{{ route('news.index', ['category' => 'guide', 'search' => request('search')]) }}" class="news-tab {{ request('category') == 'guide' ? 'active' : '' }}">
                <i class="fa-solid fa-ruler-combined"></i> Hướng Dẫn Đo & Chọn Mẫu ({{ $categoryCounts['guide'] ?? 0 }})
            </a>
            <a href="{{ route('news.index', ['category' => 'trends', 'search' => request('search')]) }}" class="news-tab {{ request('category') == 'trends' ? 'active' : '' }}">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Xu Hướng & Phong Cách ({{ $categoryCounts['trends'] ?? 0 }})
            </a>
            <a href="{{ route('news.index', ['category' => 'news', 'search' => request('search')]) }}" class="news-tab {{ request('category') == 'news' ? 'active' : '' }}">
                <i class="fa-solid fa-newspaper"></i> Tin Tức & Khuyến Mãi ({{ $categoryCounts['news'] ?? 0 }})
            </a>
        </div>

        <div class="news-layout-grid">
            <!-- Left Column: Articles Grid -->
            <div class="news-main-col">
                @if($newsList->count() > 0)
                    <div class="news-cards-grid">
                        @foreach($newsList as $item)
                            <article class="news-card">
                                <div class="news-card-img-wrap">
                                    <a href="{{ route('news.show', $item->slug) }}">
                                        <img src="{{ $item->thumbnail ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=700&q=80' }}" alt="{{ $item->title }}" loading="lazy">
                                    </a>
                                    <span class="news-card-category">
                                        @if($item->category == 'guide')
                                            <i class="fa-solid fa-ruler-combined"></i> Cẩm nang
                                        @elseif($item->category == 'trends')
                                            <i class="fa-solid fa-wand-magic-sparkles"></i> Xu hướng
                                        @else
                                            <i class="fa-solid fa-newspaper"></i> Tin tức
                                        @endif
                                    </span>
                                </div>
                                <div class="news-card-body">
                                    <div class="news-meta-row">
                                        <span><i class="fa-regular fa-calendar"></i> {{ $item->published_at ? $item->published_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}</span>
                                        <span><i class="fa-regular fa-eye"></i> {{ $item->views ?? 0 }} lượt đọc</span>
                                    </div>
                                    <h2 class="news-card-title">
                                        <a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a>
                                    </h2>
                                    <p class="news-card-excerpt">
                                        {{ $item->excerpt ?: Str::limit(strip_tags($item->content), 120) }}
                                    </p>
                                    <div class="news-card-footer">
                                        <a href="{{ route('news.show', $item->slug) }}" class="btn-read-more">
                                            <span>Đọc chi tiết</span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div style="margin-top: 40px;">
                        {{ $newsList->links('vendor.pagination.custom') }}
                    </div>
                @else
                    <div class="news-empty-box">
                        <i class="fa-regular fa-newspaper" style="font-size: 48px; color: var(--text-light); margin-bottom: 16px;"></i>
                        <h3>Không tìm thấy bài viết phù hợp</h3>
                        <p>Thử tìm kiếm với từ khóa khác hoặc quay lại xem tất cả bài viết cẩm nang rèm.</p>
                        <a href="{{ route('news.index') }}" class="btn-book-survey" style="display: inline-flex; margin-top: 16px;">Xem Toàn Bộ Bài Viết</a>
                    </div>
                @endif
            </div>

            <!-- Right Column: Sidebar -->
            <aside class="news-sidebar-col">
                <!-- Free Survey Call-to-action Banner -->
                <div class="news-cta-card">
                    <div class="cta-icon"><i class="fa-solid fa-person-shelter"></i></div>
                    <h3>Khảo Sát Ô Cửa & Tư Vấn Vải Miễn Phí</h3>
                    <p>Kỹ thuật viên mang trọn vẹn hơn 300+ mẫu vải thực tế đến tận công trình hoặc căn hộ của bạn.</p>
                    <button type="button" onclick="openSurveyModal()" class="btn-cta-gold">
                        <i class="fa-solid fa-calendar-check"></i> Đặt Lịch Đo Ngay
                    </button>
                    <span class="cta-hotline">Hotline / Zalo: <strong>0912.345.678</strong></span>
                </div>

                <!-- Trending / Recent Articles Widget -->
                <div class="news-widget-card">
                    <div class="widget-header">
                        <h3><i class="fa-solid fa-fire" style="color: #e06c3a;"></i> Bài Viết Đáng Đọc Nhất</h3>
                    </div>
                    <div class="widget-list">
                        @foreach($recentNews as $recent)
                            <a href="{{ route('news.show', $recent->slug) }}" class="recent-article-item">
                                <img src="{{ $recent->thumbnail ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=150&q=80' }}" alt="{{ $recent->title }}">
                                <div class="recent-content">
                                    <h4>{{ $recent->title }}</h4>
                                    <span class="recent-date"><i class="fa-regular fa-clock"></i> {{ $recent->published_at ? $recent->published_at->format('d/m/Y') : $recent->created_at->format('d/m/Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Featured Categories Box -->
                <div class="news-widget-card">
                    <div class="widget-header">
                        <h3><i class="fa-solid fa-tags" style="color: var(--primary);"></i> Chủ Đề Quan Tâm</h3>
                    </div>
                    <div class="tag-cloud">
                        <a href="{{ route('news.index', ['search' => 'cản sáng']) }}" class="tag-chip">Rèm cản sáng 100%</a>
                        <a href="{{ route('news.index', ['search' => 'phong thủy']) }}" class="tag-chip">Phong thủy rèm cửa</a>
                        <a href="{{ route('news.index', ['search' => 'tự đo']) }}" class="tag-chip">Cách tự đo cửa</a>
                        <a href="{{ route('news.index', ['search' => 'cầu vồng']) }}" class="tag-chip">Rèm cầu vồng Hàn Quốc</a>
                        <a href="{{ route('news.index', ['search' => 'hướng Tây']) }}" class="tag-chip">Chống nắng hướng Tây</a>
                        <a href="{{ route('news.index', ['search' => 'nhật bản']) }}" class="tag-chip">Phong cách Japandi</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>

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
