<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Rèm Yêu Thích — CurtainLux</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- External Shop CSS, Wishlist CSS & Chat Widget CSS -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wishlist.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat-widget.css') }}">
</head>
<body>

    @include('shop.partials.header')

    <div class="wishlist-page-wrapper">
        <div class="wishlist-container">

            <!-- Breadcrumbs -->
            <nav class="wishlist-breadcrumbs" aria-label="Breadcrumb">
                <a href="{{ route('shop.index') }}"><i class="fa-solid fa-house" style="font-size: 11px;"></i> Trang chủ</a>
                <span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
                <span class="current">Danh Sách Rèm Yêu Thích</span>
            </nav>

            <!-- Page Header Card -->
            <div class="wishlist-header-card">
                <div class="wishlist-header-title-group">
                    <h1>
                        <span class="heart-icon"><i class="fa-solid fa-heart"></i></span>
                        Bộ Sưu Tập Rèm Bạn Yêu Thích
                    </h1>
                    <p>
                        Lưu giữ những mẫu rèm vải, rèm cuốn và rèm cầu vồng bạn ưng ý nhất để dễ dàng tham khảo dự toán và yêu cầu khảo sát tận nhà.
                    </p>
                </div>
                <div class="wishlist-header-actions">
                    @if($wishlists->total() > 0)
                        <div class="wishlist-count-badge">
                            <i class="fa-solid fa-bookmark"></i> Đang lưu {{ $wishlists->total() }} mẫu rèm
                        </div>
                    @endif
                    <a href="{{ route('shop.index') }}" class="wishlist-back-btn">
                        <i class="fa-solid fa-compass"></i> Xem Thêm Mẫu Rèm Khác
                    </a>
                </div>
            </div>

            <!-- Wishlist Grid or Empty State -->
            @if($wishlists->count() > 0)
                <div class="wishlist-grid">
                    @foreach($wishlists as $item)
                        @php $p = $item->product; @endphp
                        @if($p)
                            <article class="wishlist-card" id="wishlist-card-{{ $item->id }}">
                                <div class="wishlist-card-media">
                                    <a href="{{ route('shop.show', $p->slug) }}">
                                        <img src="{{ $p->image ?: ($p->images->first()->image_url ?? 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80') }}" 
                                             alt="{{ $p->name }}" 
                                             loading="lazy">
                                    </a>

                                    <span class="wishlist-media-badge-cat">{{ $p->category->name ?? 'Rèm Cửa' }}</span>

                                    @if($p->blackout_rate)
                                        <span class="wishlist-media-badge-blackout">Cản sáng {{ $p->blackout_rate }}%</span>
                                    @endif
                                </div>

                                <div class="wishlist-card-body">
                                    <a href="{{ route('shop.show', $p->slug) }}" class="wishlist-item-title" title="{{ $p->name }}">
                                        {{ $p->name }}
                                    </a>

                                    <div class="wishlist-specs">
                                        @if($p->material)
                                            <span class="wishlist-chip" title="Chất liệu"><i class="fa-solid fa-scroll"></i> {{ $p->material }}</span>
                                        @endif
                                        @if($p->origin)
                                            <span class="wishlist-chip" title="Xuất xứ"><i class="fa-solid fa-globe"></i> {{ $p->origin }}</span>
                                        @endif
                                    </div>

                                    <div class="wishlist-price-container">
                                        <div class="wishlist-price-unit">Đơn giá tham khảo / {{ $p->unit_label }}</div>
                                        <div class="wishlist-price-values">
                                            <span class="wishlist-price-main">{{ number_format($p->effective_price, 0, ',', '.') }} ₫</span>
                                            @if($p->sale_price)
                                                <span class="wishlist-price-old">{{ number_format($p->price, 0, ',', '.') }} ₫</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="wishlist-actions-row">
                                        <a href="{{ route('shop.show', $p->slug) }}" class="btn-wishlist-calc">
                                            <i class="fa-solid fa-sliders"></i> May Đo & Báo Giá
                                        </a>
                                        <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bỏ mẫu rèm này khỏi danh sách yêu thích?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-wishlist-trash" title="Bỏ khỏi yêu thích">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>

                @if($wishlists->hasPages())
                    <div style="margin-top: 36px; display: flex; justify-content: center;">
                        {{ $wishlists->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="wishlist-empty-box">
                    <div class="wishlist-empty-icon">
                        <i class="fa-regular fa-heart"></i>
                    </div>
                    <h3>Danh Sách Rèm Yêu Thích Đang Trống</h3>
                    <p>
                        Hãy dạo quanh bộ sưu tập rèm cửa cao cấp của CurtainLux và nhấn vào biểu tượng trái tim để lưu lại những mẫu bạn yêu thích nhất nhé!
                    </p>
                    <a href="{{ route('shop.index') }}" class="wishlist-empty-cta">
                        <i class="fa-solid fa-compass"></i> Khám Phá Bộ Sưu Tập Rèm Ngay
                    </a>
                </div>
            @endif

        </div>
    </div>

    <!-- Floating Live Chatbot Widget -->
    @include('shop.partials.chat-widget')

    <!-- Flash Notifications -->
    @if(session('success'))
        <div data-toast-flash="success" data-toast-message="{{ session('success') }}" style="display:none;"></div>
    @endif
    @if(session('info'))
        <div data-toast-flash="info" data-toast-message="{{ session('info') }}" style="display:none;"></div>
    @endif

    <!-- Scripts -->
    <script src="{{ asset('js/shop.js') }}"></script>
    <script src="{{ asset('js/chat-widget.js') }}"></script>
</body>
</html>
