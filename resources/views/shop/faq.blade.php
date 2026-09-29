<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hỏi Đáp Thường Gặp (FAQ) — CurtainLux</title>
    <meta name="description" content="Giải đáp mọi thắc mắc về đo đạc tận nhà, chất liệu vải rèm cản sáng, thời gian may đo, chính sách bảo hành và thanh toán tại CurtainLux.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/faq.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat-widget.css') }}">
</head>
<body>

    @include('shop.partials.header')

    <div class="faq-page-wrapper">
        <div class="faq-container">

            <!-- Hero Header -->
            <div class="faq-hero-card">
                <div class="faq-badge-top">
                    <i class="fa-solid fa-circle-question"></i> Trung Tâm Hỗ Trợ Khách Hàng
                </div>
                <h1>Câu Hỏi Thường Gặp (FAQ)</h1>
                <p>
                    Tổng hợp các thắc mắc phổ biến nhất về quy trình mang mẫu tận nhà, chất liệu vải rèm cản sáng, tiến độ may đo và chính sách bảo hành chính hãng từ CurtainLux.
                </p>

                <!-- Search Input -->
                <div class="faq-search-box">
                    <i class="fa-solid fa-magnifying-glass faq-search-icon"></i>
                    <input type="text" 
                           id="faqSearchInput" 
                           class="faq-search-input" 
                           placeholder="Tìm nhanh câu hỏi (ví dụ: cản sáng, đo đạc, bảo hành, thanh toán)..."
                           autocomplete="off">
                </div>
            </div>

            <!-- Category Pills -->
            <div class="faq-category-nav">
                <a href="{{ route('faq.index') }}" 
                   class="faq-cat-pill {{ !$currentCategory ? 'active' : '' }}">
                    <i class="fa-solid fa-border-all"></i> Tất Cả Câu Hỏi
                </a>
                @foreach($categories as $code => $cat)
                    <a href="{{ route('faq.index', ['category' => $code]) }}" 
                       class="faq-cat-pill {{ $currentCategory === $code ? 'active' : '' }}">
                        <i class="fa-solid {{ $cat['icon'] }}"></i> {{ $cat['name'] }}
                    </a>
                @endforeach
            </div>

            <!-- Accordion List -->
            @if($faqs->count() > 0)
                <div class="faq-accordion-wrap">
                    @foreach($faqs as $index => $faq)
                        <div class="faq-item {{ $index === 0 ? 'is-open' : '' }}" 
                             data-question="{{ $faq->question }}" 
                             data-answer="{{ $faq->answer }}">
                            <button type="button" class="faq-question-btn">
                                <div class="faq-q-left">
                                    <span class="faq-q-icon">
                                        <i class="fa-solid {{ $faq->category_icon }}"></i>
                                    </span>
                                    <span>{{ $faq->question }}</span>
                                </div>
                                <span class="faq-chevron">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </button>
                            <div class="faq-answer-body">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 50px 20px; background: #fff; border-radius: 16px; border: 1px dashed var(--border-line);">
                    <i class="fa-regular fa-folder-open" style="font-size: 36px; color: #a8a29e; margin-bottom: 12px;"></i>
                    <h3 style="font-size: 1.1rem; color: var(--text-main);">Chưa tìm thấy câu hỏi phù hợp</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">Vui lòng chọn danh mục khác hoặc gửi câu hỏi trực tiếp cho chúng tôi.</p>
                    <a href="{{ route('faq.index') }}" class="faq-cat-pill active">Xem Tất Cả Câu Hỏi</a>
                </div>
            @endif

            <!-- Bottom Support CTA -->
            <div class="faq-contact-card">
                <div class="faq-contact-info">
                    <h3>Bạn Chưa Tìm Thấy Câu Trả Lời?</h3>
                    <p>Đội ngũ chuyên viên thiết kế và may đo CurtainLux luôn sẵn sàng hỗ trợ trực tiếp 24/7.</p>
                </div>
                <div class="faq-contact-actions">
                    <a href="javascript:void(0)" onclick="if(window.CurtainChatWidget){CurtainChatWidget.open();}" class="btn-faq-chat">
                        <i class="fa-solid fa-comments"></i> Chat Ngay Với Tư Vấn Viên
                    </a>
                    <a href="tel:0903112233" class="btn-faq-call">
                        <i class="fa-solid fa-phone"></i> 0903 112 233
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Floating Live Chatbot Widget -->
    @include('shop.partials.chat-widget')

    <!-- Scripts -->
    <script src="{{ asset('js/shop.js') }}"></script>
    <script src="{{ asset('js/faq.js') }}"></script>
    <script src="{{ asset('js/chat-widget.js') }}"></script>
</body>
</html>
