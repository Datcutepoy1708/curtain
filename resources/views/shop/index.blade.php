<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CurtainLux - Thế Giới Rèm Cửa Cao Cấp & Tinh Tế</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- External Shop CSS (Japandi Warm Neutral Palette) -->
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat-widget.css') }}">
</head>
<body>

    @include('shop.partials.header')

    @if(session('success'))
    <div class="container" style="margin-top: 20px; margin-bottom: 0;">
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <section class="hero">
        <div class="hero-pill">
            <i class="fa-solid fa-gem"></i> Thiết Kế & Thi Công Rèm Cửa Cao Cấp
        </div>
        <h1>Không Gian Sống Ấm Cúng & Sang Trọng</h1>
        <p>Kiến tạo vẻ đẹp tinh tế phong cách Japandi & Hiện Đại với các dòng rèm vải 2 lớp Bỉ/Nhật, rèm cầu vồng Hàn Quốc, rèm sáo gỗ tự nhiên và động cơ rèm thông minh.</p>

        <form action="{{ route('shop.index') }}" method="GET" class="search-box">
            <input type="text" name="search" placeholder="Tìm kiếm rèm vải 2 lớp, rèm cầu vồng, rèm gỗ..." value="{{ request('search') }}">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Tìm Mẫu Rèm</button>
        </form>
    </section>

    @if(isset($bestSellers) && $bestSellers->count() > 0 && !request('search') && !request('category'))
        <div class="container" style="margin-top: 30px; margin-bottom: 10px;">
            <div style="background: #fff; border: 1px solid var(--border-line); border-radius: 16px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <div style="font-size: 12px; font-weight: 800; color: #dc2626; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-fire"></i> Xu Hướng Thị Trường & Doanh Số Thực Tế
                        </div>
                        <h2 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-top: 2px;">
                            Top Mẫu Rèm Bán Chạy Nhất
                        </h2>
                    </div>
                    <a href="{{ route('shop.index', ['sort' => 'best_selling']) }}" style="font-size: 13px; font-weight: 700; color: var(--primary, #b8935c); text-decoration: none; display: flex; align-items: center; gap: 6px;">
                        Xem tất cả theo doanh số &rarr;
                    </a>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                    @foreach($bestSellers as $idx => $bs)
                        <div style="border: 1px solid var(--border-line); border-radius: 12px; overflow: hidden; background: #fff; transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column;">
                            <div style="position: relative; height: 160px; overflow: hidden;">
                                <img src="{{ $bs->image ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80' }}" 
                                     alt="{{ $bs->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                <span style="position: absolute; top: 10px; left: 10px; background: #dc2626; color: #fff; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                    <i class="fa-solid fa-fire"></i> Top {{ $idx + 1 }} Bán Chạy
                                </span>
                                <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.75); color: #fff; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">
                                    Đã bán: {{ $bs->total_sold }} {{ $bs->stock_unit_label }}
                                </span>
                            </div>
                            <div style="padding: 14px; display: flex; flex-direction: column; flex: 1; justify-content: space-between;">
                                <div>
                                    <div style="font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">{{ $bs->category->name ?? 'Rèm Cửa' }}</div>
                                    <a href="{{ route('shop.show', $bs->slug) }}" style="font-size: 14px; font-weight: 700; color: var(--text-main); text-decoration: none; margin: 4px 0; display: block; line-height: 1.4;">
                                        {{ $bs->name }}
                                    </a>
                                    <div style="display: flex; align-items: center; gap: 4px; font-size: 12px; color: #f59e0b; margin-bottom: 8px;">
                                        <i class="fa-solid fa-star"></i>
                                        <span style="font-weight: 700; color: var(--text-main);">{{ number_format($bs->average_rating, 1) }}</span>
                                        <span style="color: var(--text-muted);">({{ $bs->approved_reviews_count }} đánh giá)</span>
                                    </div>
                                </div>
                                <div style="border-top: 1px dashed var(--border-line); padding-top: 10px; margin-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <div style="font-size: 11px; color: var(--text-muted);">Đơn giá / {{ $bs->unit_label }}</div>
                                        <div style="font-size: 15px; font-weight: 800; color: var(--accent-cta);">{{ number_format($bs->effective_price, 0, ',', '.') }} ₫</div>
                                    </div>
                                    <a href="{{ route('shop.show', $bs->slug) }}" class="btn-wishlist" style="font-size: 11px; padding: 6px 10px; text-decoration: none;">
                                        Xem mẫu &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="container">
        <div class="shop-layout">
            <!-- Sidebar Filters -->
            <aside class="filter-card">
                <form method="GET" action="{{ route('shop.index') }}">
                    <div class="filter-title">
                        <i class="fa-solid fa-bars-staggered" style="color: var(--accent-cta);"></i>
                        <span>Danh Mục Rèm Cửa</span>
                    </div>

                    <ul class="cat-list">
                        <li>
                            <a href="{{ route('shop.index') }}" class="{{ !request('category') ? 'active' : '' }}">
                                <span>Tất Cả Danh Mục</span>
                                <span>({{ \App\Models\Product::where('is_active', true)->count() }})</span>
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('shop.index', array_merge(request()->query(), ['category' => $category->slug])) }}" 
                                   class="{{ request('category') == $category->slug ? 'active' : '' }}">
                                    <span>{{ $category->name }}</span>
                                    <span>({{ $category->products_count }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    @if(!empty($materials) && count($materials) > 0)
                    <div class="filter-group">
                        <div class="filter-group-title">Chất Liệu Vải / Gỗ</div>
                        <select name="material" class="filter-select" onchange="this.form.submit()">
                            <option value="">-- Tất cả chất liệu --</option>
                            @foreach($materials as $mat)
                                <option value="{{ $mat }}" {{ request('material') == $mat ? 'selected' : '' }}>{{ $mat }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(!empty($origins) && count($origins) > 0)
                    <div class="filter-group">
                        <div class="filter-group-title">Xuất Xứ Vải</div>
                        <select name="origin" class="filter-select" onchange="this.form.submit()">
                            <option value="">-- Tất cả xuất xứ --</option>
                            @foreach($origins as $org)
                                <option value="{{ $org }}" {{ request('origin') == $org ? 'selected' : '' }}>{{ $org }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="filter-group">
                        <div class="filter-group-title">Mức Giá / Đơn Vị</div>
                        <select name="price_range" class="filter-select" onchange="this.form.submit()">
                            <option value="">-- Mọi mức giá --</option>
                            <option value="under_500" {{ request('price_range') == 'under_500' ? 'selected' : '' }}>Dưới 500.000 ₫</option>
                            <option value="500_1000" {{ request('price_range') == '500_1000' ? 'selected' : '' }}>Từ 500.000 ₫ - 1.000.000 ₫</option>
                            <option value="over_1000" {{ request('price_range') == 'over_1000' ? 'selected' : '' }}>Trên 1.000.000 ₫</option>
                        </select>
                    </div>
                </form>

                <!-- Box Hỗ Trợ Tư Vấn Tận Nhà -->
                <div style="background: var(--bg-subtle); border-radius: var(--radius-sm); padding: 18px; margin-top: 24px; text-align: center;">
                    <i class="fa-solid fa-ruler-combined" style="font-size: 26px; color: var(--accent-cta); margin-bottom: 8px;"></i>
                    <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 6px;">Bạn Chưa Rõ Số Đo?</h4>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">Thợ kỹ thuật của CurtainLux sẽ mang mẫu vải thực tế đến tận nhà đo đạc miễn phí.</p>
                    <button type="button" onclick="openSurveyModal()" class="btn-book-survey" style="width: 100%; justify-content: center; font-size: 13px; padding: 8px;">
                        Đặt Hẹn Ngay
                    </button>
                </div>
            </aside>

            <!-- Product Grid -->
            <main>
                <div class="shop-header-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div class="shop-count">
                        Hiển thị <strong>{{ $products->total() }}</strong> mẫu rèm phù hợp
                    </div>

                    <!-- Sort Dropdown -->
                    <form method="GET" action="{{ route('shop.index') }}" style="display: flex; align-items: center; gap: 8px;">
                        @foreach(request()->except('sort', 'page') as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <label style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Sắp xếp theo:</label>
                        <select name="sort" onchange="this.form.submit()" 
                                style="padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border-line); background: #fff; font-size: 13px; font-weight: 600; color: var(--text-main); outline: none;">
                            <option value="" {{ !request('sort') ? 'selected' : '' }}>Mới nhất</option>
                            <option value="best_selling" {{ request('sort') === 'best_selling' ? 'selected' : '' }}>🔥 Bán chạy nhất</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                        </select>
                    </form>
                </div>

                @if($products->count() > 0)
                    <div class="products-grid">
                        @foreach($products as $product)
                            <div class="product-card">
                                <div class="product-img-wrap">
                                    <img src="{{ $product->image ?: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $product->name }}">
                                    @php
                                        $isWishlisted = in_array($product->id, $wishlistedProductIds ?? []);
                                    @endphp
                                    <button type="button" 
                                            class="btn-wishlist-toggle {{ $isWishlisted ? 'is-active' : '' }}" 
                                            data-wishlist-id="{{ $product->id }}" 
                                            title="{{ $isWishlisted ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích' }}">
                                        <i class="{{ $isWishlisted ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                    </button>
                                    <span class="badge-cat">{{ $product->category->name ?? 'Rèm Cửa' }}</span>
                                    @if($product->blackout_rate)
                                        <span class="badge-blackout">Cản sáng {{ $product->blackout_rate }}%</span>
                                    @endif
                                    @if($product->images && $product->images->count() > 0)
                                        <span class="badge-gallery" title="{{ $product->images->count() + 1 }} góc chụp"><i class="fa-solid fa-images"></i> {{ $product->images->count() + 1 }}</span>
                                    @endif
                                </div>
                                <div class="product-body">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="product-title">
                                        {{ $product->name }}
                                    </a>

                                    <div class="product-rating-row">
                                        <div class="stars-gold">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= round($product->average_rating) ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                            @endfor
                                        </div>
                                        <span class="rating-num">{{ number_format($product->average_rating, 1) }}</span>
                                        <span class="rating-count">({{ $product->approved_reviews_count }})</span>
                                    </div>

                                    <div class="product-specs-chip">
                                        @if($product->material)
                                            <span class="chip"><i class="fa-solid fa-scroll"></i> {{ $product->material }}</span>
                                        @endif
                                        @if($product->origin)
                                            <span class="chip"><i class="fa-solid fa-earth-americas"></i> {{ $product->origin }}</span>
                                        @endif
                                    </div>

                                    <div class="product-footer">
                                        <div class="price-wrap">
                                            <span class="price-unit-note">Đơn giá tham khảo / {{ $product->unit_label }}</span>
                                            <span class="price">
                                                {{ number_format($product->effective_price, 0, ',', '.') }} ₫
                                            </span>
                                        </div>
                                        <a href="{{ route('shop.show', $product->slug) }}" class="btn-view">
                                            <i class="fa-solid fa-calculator"></i> Tính Giá
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top: 36px;">
                        {{ $products->links('vendor.pagination.custom') }}
                    </div>
                @else
                    <div style="background: var(--bg-card); border-radius: var(--radius-md); padding: 60px 20px; text-align: center; border: 1px solid var(--border-line);">
                        <i class="fa-regular fa-folder-open" style="font-size: 40px; color: var(--text-light); margin-bottom: 12px;"></i>
                        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px;">Không tìm thấy mẫu rèm phù hợp</h3>
                        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">Vui lòng thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc.</p>
                        <a href="{{ route('shop.index') }}" class="btn-book-survey">Xem Tất Cả Mẫu Rèm</a>
                    </div>
                @endif
            </main>
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
                <p class="modal-desc">Chuyên viên CurtainLux sẽ mang đầy đủ cây mẫu vải thực tế đến tận nơi, tư vấn phối màu hợp phong thủy và đo đạc kích thước chuẩn xác <strong>hoàn toàn miễn phí</strong>.</p>
                
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
                        <label>Số Lượng Ô Cửa Dự Kiến</label>
                        <input type="number" name="estimated_windows" value="2" min="1" max="50">
                    </div>
                </div>

                <div class="modal-field">
                    <label>Địa Chỉ Khảo Sát *</label>
                    <input type="text" name="address" required placeholder="Số nhà, tên đường, tòa nhà chung cư...">
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

                <div class="modal-field">
                    <label>Ghi Chú Yêu Cầu Riêng (Nếu Có)</label>
                    <textarea name="notes" rows="2" placeholder="Ví dụ: Mang thêm mẫu rèm vải tone be hoặc rèm cầu vồng cản sáng 100%..."></textarea>
                </div>

                <button type="submit" class="btn-submit-survey">
                    <i class="fa-solid fa-paper-plane"></i> Gửi Yêu Cầu Khảo Sát Miễn Phí
                </button>
            </form>
        </div>
    </div>

    <footer>
        <div class="footer-features">
            <div class="feature-box">
                <i class="fa-solid fa-ruler-combined"></i>
                <div>
                    <h4>Khảo Sát Tận Nhà Miễn Phí</h4>
                    <p>Mang catalogue cây mẫu vải tận nơi, tư vấn đo đạc chính xác 100%.</p>
                </div>
            </div>
            <div class="feature-box">
                <i class="fa-solid fa-shield-halved"></i>
                <div>
                    <h4>Bảo Hành Chính Hãng 3-5 Năm</h4>
                    <p>Bảo hành phụ kiện ray treo và động cơ điện thông minh dài hạn.</p>
                </div>
            </div>
            <div class="feature-box">
                <i class="fa-solid fa-truck-fast"></i>
                <div>
                    <h4>Gia Công & Thi Công Nhanh</h4>
                    <p>Lắp đặt hoàn thiện chỉ từ 48h - 72h sau khi khách chốt mẫu.</p>
                </div>
            </div>
        </div>
        <p>&copy; {{ date('Y') }} CurtainLux - Thương Hiệu Rèm Cửa & Nội Thất Vải Tinh Tế. Hotline: 0912.345.678</p>
    </footer>

    <!-- Floating Live Chatbot Widget -->
    @include('shop.partials.chat-widget')

    <script src="{{ asset('js/shop.js') }}"></script>
    <script src="{{ asset('js/chat-widget.js') }}"></script>
</body>
</html>
