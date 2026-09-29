<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Báo Giá & Tính Kích Thước Rèm | CurtainLux</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- External Shop CSS -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat-widget.css') }}">
</head>
<body>

    @include('shop.partials.header')

    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('shop.index') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <span>/</span>
            <a href="{{ route('shop.index', ['category' => $product->category->slug ?? '']) }}">{{ $product->category->name ?? 'Rèm Cửa' }}</a>
            <span>/</span>
            <span style="color: var(--text-main); font-weight: 600;">{{ $product->name }}</span>
        </div>

        <!-- Product Main Detail & Dimension Calculator -->
        <div class="product-detail-grid">
            <!-- Left: Gallery & Specs -->
            <div class="detail-gallery">
                @php
                    $allImages = $product->all_images;
                    $totalImages = count($allImages);
                @endphp

                <!-- Main Image Wrap with Slide Controls -->
                <div class="detail-gallery-main-wrap" id="galleryMainWrap">
                    <img src="{{ $allImages[0] ?? ($product->image ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=800&q=80') }}" 
                         alt="{{ $product->name }}" class="detail-img-main" id="mainProductImage">

                    @if($totalImages > 1)
                        <button type="button" class="gallery-slider-btn prev" id="btnPrevSlide" aria-label="Ảnh trước" title="Xem ảnh trước">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button type="button" class="gallery-slider-btn next" id="btnNextSlide" aria-label="Ảnh tiếp theo" title="Xem ảnh tiếp theo">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <div class="gallery-slide-counter" id="slideCounter">
                            1 / {{ $totalImages }}
                        </div>
                    @endif
                </div>

                <!-- Thumbnails Strip Underneath -->
                @if($totalImages > 1)
                    <div class="detail-gallery-thumbs" id="galleryThumbs">
                        @foreach($allImages as $idx => $imgUrl)
                            <button type="button" 
                                    class="detail-thumb-btn {{ $idx === 0 ? 'active' : '' }}" 
                                    data-slide-index="{{ $idx }}" 
                                    data-target-src="{{ $imgUrl }}" 
                                    title="Xem ảnh {{ $idx + 1 }} của {{ $product->name }}">
                                <img src="{{ $imgUrl }}" alt="{{ $product->name }} - Ảnh thu nhỏ {{ $idx + 1 }}">
                            </button>
                        @endforeach
                    </div>
                @endif

                <div style="background: var(--bg-card); border: 1px solid var(--border-line); border-radius: var(--radius-md); padding: 20px;">
                    <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 12px; color: var(--text-main);">
                        <i class="fa-solid fa-circle-info" style="color: var(--accent-cta);"></i> Thông Số & Đặc Tính Kỹ Thuật
                    </h3>
                    <table class="specs-table">
                        <tr>
                            <td>Danh mục:</td>
                            <td><strong>{{ $product->category->name ?? 'Rèm Cửa' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Cách tính giá:</td>
                            <td>
                                <strong style="color: var(--accent-cta);">
                                    {{ $product->price_unit === 'meter' ? 'Tính theo Mét Ngang hoàn thiện' : ($product->price_unit === 'sqm' ? 'Tính theo Mét Vuông (m²)' : 'Tính theo Bộ') }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Chất liệu:</td>
                            <td>{{ $product->material ?: 'Vải dệt cao cấp' }}</td>
                        </tr>
                        <tr>
                            <td>Xuất xứ:</td>
                            <td>{{ $product->origin ?: 'Chính hãng' }}</td>
                        </tr>
                        <tr>
                            <td>Độ cản sáng:</td>
                            <td>{{ $product->blackout_rate ? $product->blackout_rate . '% (Chống tia cực tím UV)' : 'Cản sáng tốt' }}</td>
                        </tr>
                        <tr>
                            <td>Giới hạn may:</td>
                            <td>Rộng {{ $product->min_width }} - {{ $product->max_width }}cm | Cao {{ $product->min_height }} - {{ $product->max_height }}cm</td>
                        </tr>
                    </table>

                    <div class="desc-title">Mô Tả Sản Phẩm:</div>
                    <div class="desc-text">{{ $product->description ?: 'Sản phẩm rèm cao cấp nhập khẩu chính hãng, độ bền sợi trên 10 năm.' }}</div>
                </div>
            </div>

            <!-- Right: Live Dimension Calculator & Customizer Form -->
            <div class="detail-info">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 8px;">
                    <h1 style="margin: 0;">{{ $product->name }}</h1>
                    <button type="button" 
                            class="btn-wishlist-toggle {{ $isWishlisted ? 'is-active' : '' }}" 
                            data-wishlist-id="{{ $product->id }}" 
                            title="{{ $isWishlisted ? 'Bỏ khỏi danh sách yêu thích' : 'Thêm vào danh sách yêu thích' }}">
                        <i class="{{ $isWishlisted ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                    </button>
                </div>

                <div class="product-rating-row" style="margin-bottom: 16px;">
                    <div class="stars-gold">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= round($product->average_rating) ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                        @endfor
                    </div>
                    <span class="rating-num" style="font-weight: 800; font-size: 15px;">{{ number_format($product->average_rating, 1) }} / 5.0</span>
                    <a href="#reviewsSection" class="rating-count" style="text-decoration: underline; color: var(--text-muted); cursor: pointer;">
                        ({{ $product->approved_reviews_count }} lượt đánh giá & bình chọn)
                    </a>
                </div>

                <div class="detail-price-banner">
                    <div>
                        <span class="price-main">{{ number_format($product->effective_price, 0, ',', '.') }} ₫</span>
                        <span class="price-sub">/ {{ $product->unit_label }}</span>
                    </div>
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <span style="text-decoration: line-through; color: var(--text-light); font-size: 16px;">
                            {{ number_format($product->price, 0, ',', '.') }} ₫
                        </span>
                    @endif
                </div>

                <form id="curtainCalcForm" action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" id="basePrice" value="{{ $product->effective_price }}">
                    <input type="hidden" id="priceUnit" value="{{ $product->price_unit }}">
                    <input type="hidden" id="minArea" value="{{ $product->min_area ?? 1.0 }}">

                    <div class="calc-card">
                        <div class="calc-header">
                            <h3><i class="fa-solid fa-ruler-combined"></i> Bộ Công Cụ Ước Tính Kích Thước Ô Cửa</h3>
                            <span style="font-size: 12px; color: var(--text-muted);">Đơn vị: centimet (cm)</span>
                        </div>

                        <!-- Room Label -->
                        <div class="modal-field" style="margin-bottom: 16px;">
                            <label><i class="fa-solid fa-tag" style="color: var(--accent-cta);"></i> Tên Vị Trí Ô Cửa (Để xưởng may và thợ dán nhãn)</label>
                            <input type="text" name="room_label" id="roomLabel" value="Cửa phòng khách chính" placeholder="Vd: Cửa chính phòng khách, Cửa sổ phòng ngủ Master...">
                        </div>

                        <!-- Width & Height Inputs -->
                        <div class="form-group-row">
                            <div class="form-field">
                                <label>Chiều Rộng Ô Cửa (W) *</label>
                                <div class="input-with-unit">
                                    <input type="number" name="width" id="inputWidth" value="200" 
                                           min="{{ $product->min_width }}" max="{{ $product->max_width }}" step="1" required>
                                    <span class="input-unit">cm</span>
                                </div>
                            </div>
                            <div class="form-field">
                                <label>Chiều Cao Ô Cửa (H) *</label>
                                <div class="input-with-unit">
                                    <input type="number" name="height" id="inputHeight" value="250" 
                                           min="{{ $product->min_height }}" max="{{ $product->max_height }}" step="1" required>
                                    <span class="input-unit">cm</span>
                                </div>
                            </div>
                        </div>

                        <!-- Mount Type Switcher -->
                        <div class="form-field" style="margin-bottom: 16px;">
                            <label>Kiểu Lắp Đặt</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <label class="option-pill">
                                    <span class="option-label-wrap">
                                        <input type="radio" name="mount_type" value="outside" checked>
                                        <span>Phủ Bì Tường (Phổ biến nhất)</span>
                                    </span>
                                </label>
                                <label class="option-pill">
                                    <span class="option-label-wrap">
                                        <input type="radio" name="mount_type" value="inside">
                                        <span>Lọt Lòng Khung Cửa</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Curtain Options (Header, Track, Motor, Sheer) -->
                        @if(isset($optionGroups) && $optionGroups->count() > 0)
                            @foreach($optionGroups as $group)
                                @if($group->applies_to === 'all' || ($group->applies_to === 'fabric' && str_contains($product->category->slug ?? '', 'vai')))
                                    <div class="option-section">
                                        <div class="option-section-title">
                                            <span>{{ $group->name }}</span>
                                            @if($group->is_required)
                                                <span style="color: var(--accent-cta); font-size: 11px;">(Bắt buộc)</span>
                                            @endif
                                        </div>

                                        <div class="option-pill-group">
                                            @foreach($group->values as $val)
                                                <label class="option-pill">
                                                    <span class="option-label-wrap">
                                                        <input type="radio" name="options[{{ $group->code }}]" value="{{ $val->id }}"
                                                               data-extra="{{ $val->extra_price }}"
                                                               data-impact="{{ $val->price_impact_type }}"
                                                               {{ $val->is_default ? 'checked' : '' }}>
                                                        <span>{{ $val->name }}</span>
                                                    </span>
                                                    <span class="option-extra-price">{{ $val->formatted_extra_price }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        <!-- Live Quote Box -->
                        <div class="calc-live-quote">
                            <div class="calc-live-row">
                                <span>Kích thước quy đổi tính tiền:</span>
                                <strong id="lblCalculatedUnits" style="color: var(--text-main);">2.0 mét ngang</strong>
                            </div>
                            <div class="calc-live-row">
                                <span>Tiền vải chính:</span>
                                <span id="lblBaseCost">1.980.000 ₫</span>
                            </div>
                            <div class="calc-live-row">
                                <span>Phụ phí tùy chọn & Động cơ:</span>
                                <span id="lblOptionsCost">0 ₫</span>
                            </div>
                            <div class="calc-live-row total-row">
                                <span>TỔNG CHI PHÍ DỰ KIẾN:</span>
                                <span class="total-value" id="lblGrandTotal">1.980.000 ₫</span>
                            </div>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px; font-weight: normal; line-height: 1.4;">
                                <i class="fa-solid fa-circle-info" style="color: #2563eb;"></i> <em>Giá trên là <strong>Giá Dự Kiến</strong>. Chuyên viên sẽ mang catalogue mẫu vải tận nơi, đo đạc chính xác từng ô cửa và lập bảng báo giá chính thức để bạn duyệt trước khi may.</em>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="detail-actions">
                            <button type="submit" class="btn-add-cart">
                                <i class="fa-solid fa-cart-plus"></i> Thêm Vào Giỏ Hàng
                            </button>
                            <button type="button" onclick="openSurveyWithProduct('{{ addslashes($product->name) }}')" class="btn-survey-now">
                                <i class="fa-solid fa-house-chimney-user"></i> Đặt Lịch Đo Tại Nhà (Miễn Phí)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openSurveyWithProduct(productName) {
            const w = document.getElementById('inputWidth')?.value || 200;
            const h = document.getElementById('inputHeight')?.value || 250;
            const price = document.getElementById('lblGrandTotal')?.innerText || '';
            const notesField = document.getElementById('surveyNotesField');
            if (notesField) {
                notesField.value = `Khách hàng quan tâm mẫu "${productName}" (Kích thước dự kiến: ${w}cm x ${h}cm, dự toán: ${price}). Yêu cầu mang mẫu thực tế tận nhà.`;
            }
            openSurveyModal();
        }
    </script>

    <!-- Customer Reviews & Ratings Section -->
    <section id="reviewsSection" class="shop-container" style="margin-top: 50px; margin-bottom: 60px;">
        <div class="reviews-wrapper-card">
            <div class="reviews-header-title">
                <h2><i class="fa-solid fa-star" style="color: #f59e0b;"></i> Đánh Giá & Nhận Xét Từ Khách Hàng Thực Tế</h2>
                <p>Khách hàng đã đặt may và hoàn thiện lắp đặt mẫu "{{ $product->name }}" chia sẻ trải nghiệm.</p>
            </div>

            <div class="reviews-summary-grid">
                <!-- Score Breakdown -->
                <div class="rating-score-box">
                    <div class="big-score">{{ number_format($product->average_rating, 1) }}</div>
                    <div class="stars-gold large">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= round($product->average_rating) ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                        @endfor
                    </div>
                    <div class="score-sub">Dựa trên {{ $product->approved_reviews_count }} đánh giá thực tế</div>
                </div>

                <!-- Star percentage bars -->
                @php
                    $allApproved = $product->approvedReviews;
                    $totalRev = $allApproved->count();
                @endphp
                <div class="rating-bars-box">
                    @for($s = 5; $s >= 1; $s--)
                        @php
                            $cnt = $allApproved->where('rating', $s)->count();
                            $pct = $totalRev > 0 ? round(($cnt / $totalRev) * 100) : ($s == 5 ? 85 : ($s == 4 ? 15 : 0));
                        @endphp
                        <div class="rating-bar-row">
                            <span class="bar-label">{{ $s }} sao</span>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: {{ $pct }}%;"></div>
                            </div>
                            <span class="bar-count">{{ $cnt }} ({{ $pct }}%)</span>
                        </div>
                    @endfor
                </div>

                <!-- Write Review Callout -->
                <div class="write-review-prompt">
                    <h4>Bạn đã trải nghiệm mẫu rèm này?</h4>
                    <p>Chia sẻ cảm nhận về chất vải, độ cản sáng và dịch vụ lắp đặt để giúp cộng đồng có thêm góc nhìn khách quan.</p>
                    <a href="#reviewFormBox" class="btn-write-review">
                        <i class="fa-solid fa-pen-nib"></i> Viết Nhận Xét Ngay
                    </a>
                </div>
            </div>

            <!-- Review Form & List Grid -->
            <div class="reviews-content-grid">
                <!-- Left: List of Reviews -->
                <div class="reviews-list-col">
                    <h3 class="col-title">Nhận Xét Của Khách Hàng ({{ $totalRev }})</h3>
                    @if($totalRev > 0)
                        <div class="review-items-list">
                            @foreach($allApproved as $rev)
                                <div class="review-item-card">
                                    <div class="review-author-bar">
                                        <div class="author-avatar">{{ mb_substr($rev->customer_name, 0, 1) }}</div>
                                        <div class="author-meta">
                                            <div class="name-row">
                                                <strong>{{ $rev->customer_name }}</strong>
                                                <span class="verified-badge"><i class="fa-solid fa-circle-check"></i> Đã mua tại CurtainLux</span>
                                            </div>
                                            <div class="review-date-stars">
                                                <div class="stars-gold small">
                                                    @for($k = 1; $k <= 5; $k++)
                                                        <i class="{{ $k <= $rev->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                                    @endfor
                                                </div>
                                                <span class="date-text">{{ $rev->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="review-comment-text">
                                        {{ $rev->comment }}
                                    </div>

                                    @if($rev->hasReply())
                                        <div class="review-official-reply" style="margin-top: 14px; margin-left: 16px; padding: 14px 16px; background: #faf7f2; border-left: 3px solid #b8935c; border-radius: 8px; position: relative;">
                                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; gap: 8px; flex-wrap: wrap;">
                                                <div style="display: flex; align-items: center; gap: 6px;">
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; background: #b8935c; color: #ffffff; border-radius: 50%; font-size: 10px;">
                                                        <i class="fa-solid fa-check"></i>
                                                    </span>
                                                    <strong style="font-size: 13px; color: #2d2621; font-weight: 700;">Phản hồi từ Xưởng May CurtainLux</strong>
                                                    <span style="font-size: 10px; background: #e8ded2; color: #6b5847; padding: 1px 7px; border-radius: 12px; font-weight: 600;">Chính thức</span>
                                                </div>
                                                @if($rev->replied_at)
                                                    <span style="font-size: 11px; color: #8c7e73;">
                                                        <i class="fa-regular fa-clock" style="font-size: 10px;"></i> {{ $rev->replied_at->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div style="font-size: 13px; color: #4a3e35; line-height: 1.6; white-space: pre-line;">
                                                {{ $rev->reply_content }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-reviews-notice">
                            <i class="fa-regular fa-comment-dots"></i>
                            <p>Chưa có nhận xét nào cho mẫu rèm này. Hãy là người đầu tiên để lại đánh giá!</p>
                        </div>
                    @endif
                </div>

                <!-- Right: Submit Review Form (Verified Purchase Policy) -->
                <div class="review-form-col" id="reviewFormBox">
                    <div class="review-form-card">
                        <h3><i class="fa-solid fa-pen-to-square"></i> Gửi Đánh Giá Của Bạn</h3>
                        
                        @if(session('success'))
                            <div class="review-alert-success">
                                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div style="background: #fef2f2; border: 1px solid #fecdd3; color: #b91c1c; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                            </div>
                        @endif

                        @php
                            $canReview = false;
                            if (auth()->check()) {
                                $canReview = auth()->user()->isAdmin() || \App\Models\Order::where('user_id', auth()->id())
                                    ->whereIn('order_status', ['confirmed', 'manufacturing', 'shipping', 'installed', 'completed'])
                                    ->whereHas('items', function ($q) use ($product) {
                                        $q->where('product_id', $product->id);
                                    })
                                    ->exists();
                            }
                        @endphp

                        @if($canReview)
                            <div style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; padding: 10px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-circle-check" style="font-size: 16px;"></i>
                                <span>Bạn đã đặt may mẫu rèm này. Nhận xét của bạn sẽ được gắn huy hiệu <strong>Người Mua Xác Thực</strong>.</span>
                            </div>

                            <form action="{{ route('reviews.store') }}" method="POST" id="customerReviewForm">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="rating" id="reviewRatingInput" value="5">

                                <!-- Interactive Stars -->
                                <div class="form-group-star">
                                    <label>Đánh giá số sao:</label>
                                    <div class="interactive-stars" id="starRatingGroup">
                                        <i class="fa-solid fa-star star-btn active" data-rating="1"></i>
                                        <i class="fa-solid fa-star star-btn active" data-rating="2"></i>
                                        <i class="fa-solid fa-star star-btn active" data-rating="3"></i>
                                        <i class="fa-solid fa-star star-btn active" data-rating="4"></i>
                                        <i class="fa-solid fa-star star-btn active" data-rating="5"></i>
                                    </div>
                                    <span class="rating-status-text" id="ratingDesc">Tuyệt vời - Rất ưng ý (5 sao)</span>
                                </div>

                                <div class="form-field-review">
                                    <label>Họ và Tên *</label>
                                    <input type="text" name="customer_name" required value="{{ auth()->user()->name }}" readonly style="background: #f8fafc;">
                                </div>

                                <div class="form-field-review">
                                    <label>Số Điện Thoại (Bảo mật)</label>
                                    <input type="tel" name="customer_phone" value="{{ auth()->user()->phone }}" placeholder="09xx xxx xxx">
                                </div>

                                <div class="form-field-review">
                                    <label>Nhận Xét Chi Tiết *</label>
                                    <textarea name="comment" rows="4" required placeholder="Chia sẻ cảm nhận về độ rủ, cản nắng, độ dày dặn và độ hoàn thiện đường may..."></textarea>
                                </div>

                                <button type="submit" class="btn-submit-review">
                                    <i class="fa-solid fa-paper-plane"></i> Gửi Đánh Giá Xác Thực
                                </button>
                            </form>
                        @elseif(!auth()->check())
                            <div style="background: #f8fafc; border: 1px dashed var(--border-line); border-radius: 8px; padding: 24px; text-align: center;">
                                <i class="fa-solid fa-shield-halved" style="font-size: 32px; color: var(--primary, #b8935c); margin-bottom: 10px;"></i>
                                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Chính Sách Đánh Giá Xác Thực</h4>
                                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px; line-height: 1.5;">Để đảm bảo tính minh bạch, chỉ khách hàng đã đăng nhập và đặt may mẫu rèm này tại CurtainLux mới có thể viết nhận xét.</p>
                                <a href="{{ route('login') }}" class="btn-book-survey" style="display: inline-flex; text-decoration: none; font-size: 13px;">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Đăng Nhập Để Đánh Giá
                                </a>
                            </div>
                        @else
                            <div style="background: #f8fafc; border: 1px dashed var(--border-line); border-radius: 8px; padding: 24px; text-align: center;">
                                <i class="fa-solid fa-bag-shopping" style="font-size: 32px; color: #94a3b8; margin-bottom: 10px;"></i>
                                <h4 style="font-size: 15px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Bạn Chưa Mua Sản Phẩm Này</h4>
                                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px; line-height: 1.5;">Tài khoản của bạn chưa có đơn đặt may mẫu rèm này. Hãy đặt may để cảm nhận chất lượng đường may và độ cản nắng thực tế.</p>
                                <a href="#customCalcForm" class="btn-wishlist" style="display: inline-flex; text-decoration: none; font-size: 13px; color: var(--primary, #b8935c); border-color: var(--primary, #b8935c);">
                                    <i class="fa-solid fa-ruler-combined"></i> Đặt May Mẫu Này Ngay
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Home Survey Booking Modal -->
    <div id="surveyModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="fa-solid fa-calendar-check"></i> Đặt Lịch Khảo Sát & Mang Mẫu Tận Nhà</h3>
                <button type="button" class="btn-close-modal" onclick="closeSurveyModal()">&times;</button>
            </div>
            <form action="{{ route('consultation.store') }}" method="POST" class="modal-body">
                @csrf
                <p class="modal-desc">Kỹ thuật viên sẽ mang theo cây mẫu thực tế của mẫu <strong>"{{ $product->name }}"</strong> đến tận nhà bạn để so màu và đo đạc miễn phí.</p>
                
                <div class="modal-field">
                    <label>Họ và Tên Của Bạn *</label>
                    <input type="text" name="customer_name" required placeholder="Ví dụ: Anh Dũng / Chị Hương">
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
                    <input type="text" name="address" required placeholder="Số nhà, tên đường, tòa chung cư...">
                </div>

                <div class="form-group-row">
                    <div class="modal-field">
                        <label>Ngày Hẹn Mong Muốn *</label>
                        <input type="date" name="preferred_date" required value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>
                    <div class="modal-field">
                        <label>Khung Giờ</label>
                        <select name="preferred_time">
                            <option value="Sáng (08:30 - 11:30)">Buổi Sáng (08:30 - 11:30)</option>
                            <option value="Chiều (14:00 - 17:30)">Buổi Chiều (14:00 - 17:30)</option>
                            <option value="Tối (18:00 - 20:30)">Buổi Tối (18:00 - 20:30)</option>
                        </select>
                    </div>
                </div>

                <div class="modal-field">
                    <label>Ghi Chú</label>
                    <textarea name="notes" rows="2" placeholder="Ghi chú thêm về giờ giấc hoặc vị trí căn hộ..."></textarea>
                </div>

                <button type="submit" class="btn-submit-survey">
                    <i class="fa-solid fa-paper-plane"></i> Xác Nhận Đặt Lịch Hẹn
                </button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} CurtainLux - Thương Hiệu Rèm Cửa & Nội Thất Vải Tinh Tế. Hotline: 0912.345.678</p>
    </footer>

    <!-- Floating Live Chatbot Widget -->
    @include('shop.partials.chat-widget')

    <script src="{{ asset('js/shop.js') }}"></script>
    <script src="{{ asset('js/chat-widget.js') }}"></script>
</body>
</html>
