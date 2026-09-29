<!-- CurtainLux Custom Notification & Confirm Dialog (Replaces browser "localhost says") -->
<link rel="stylesheet" href="{{ asset('css/curtain-notify.css') }}">
<script src="{{ asset('js/curtain-notify.js') }}"></script>

<header>
    <div class="header-inner">
        <a href="{{ route('shop.index') }}" class="logo">
            <i class="fa-solid fa-person-shelter"></i>
            <span>CURTAIN LUX</span>
        </a>

        <ul class="nav-links">
            <li><a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.index') ? 'active' : '' }}">Bộ Sưu Tập Rèm</a></li>
            <li><a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.*') ? 'active' : '' }}">Cẩm Nang Chọn Rèm</a></li>
            <li><a href="{{ route('faq.index') }}" class="{{ request()->routeIs('faq.*') ? 'active' : '' }}">Hỏi Đáp FAQ</a></li>
            <li><a href="{{ route('order.tracking') }}" class="{{ request()->routeIs('order.tracking') ? 'active' : '' }}">Tra Cứu Đơn</a></li>
            <li><a href="javascript:void(0)" onclick="openSurveyModal()">Khảo Sát Tận Nhà</a></li>
            @auth
                @if(auth()->user()->isStaff())
                    <li><a href="{{ route('admin.dashboard') }}" style="color: #b8935c; font-weight: 700;"><i class="fa-solid fa-gear"></i> Trang Admin</a></li>
                @endif
            @endauth
        </ul>

        <div class="header-actions">
            @php
                $cartSessionId = session()->get('cart_session_id');
                $cartCount = $cartSessionId ? \App\Models\CartItem::where('session_id', $cartSessionId)->count() : 0;

                $wUserId = auth()->id();
                $wSessionId = session()->get('wishlist_session_id') ?: session()->getId();
                $wishlistCount = $wUserId 
                    ? \App\Models\Wishlist::where('user_id', $wUserId)->count() 
                    : \App\Models\Wishlist::where('session_id', $wSessionId)->count();
            @endphp

            <!-- 1. Wishlist Button -->
            <a href="{{ route('wishlist.index') }}" class="btn-wishlist" title="Danh sách rèm yêu thích">
                <i class="fa-solid fa-heart" style="color: #ef4444;"></i>
                <span>Yêu Thích</span>
                <span class="wishlist-badge" id="wishlistHeaderBadge" style="{{ $wishlistCount > 0 ? '' : 'display:none;' }}">
                    {{ $wishlistCount }}
                </span>
            </a>

            <!-- 2. Cart Button -->
            <a href="{{ route('cart.index') }}" class="btn-cart" title="Giỏ hàng may đo của bạn">
                <i class="fa-solid fa-bag-shopping" style="color: var(--primary, #b8935c);"></i>
                <span>Giỏ Hàng</span>
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount }}</span>
                @endif
            </a>

            <!-- 3. Free Survey Booking Button -->
            <button type="button" onclick="openSurveyModal()" class="btn-book-survey">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Đặt Lịch Đo Miễn Phí</span>
            </button>

            <!-- 4. Account / Login Button -->
            @auth
                <div class="user-profile-pill" style="position: relative;">
                    <a href="{{ route('customer.profile') }}" class="user-name-text" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-circle-user" style="color: var(--primary, #b8935c); font-size: 16px;"></i> 
                        <span>{{ auth()->user()->name }}</span>
                        <i class="fa-solid fa-chevron-down" style="font-size: 10px; color: var(--text-muted); margin-left: 2px;"></i>
                    </a>
                    
                    <div class="user-dropdown-menu" style="display: none; position: absolute; top: calc(100% + 8px); right: 0; background: #fff; border: 1px solid var(--border-line); border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); width: 200px; padding: 6px 0; z-index: 1000;">
                        <a href="{{ route('customer.profile') }}" style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 13px; font-weight: 600; color: var(--text-main); text-decoration: none;">
                            <i class="fa-solid fa-id-card" style="color: var(--primary, #b8935c); width: 16px;"></i> Hồ Sơ Của Tôi
                        </a>
                        <a href="{{ route('customer.orders') }}" style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 13px; font-weight: 600; color: var(--text-main); text-decoration: none;">
                            <i class="fa-solid fa-cart-flatbed" style="color: #2563eb; width: 16px;"></i> Đơn Hàng May Rèm
                        </a>
                        <a href="{{ route('customer.consultations') }}" style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 13px; font-weight: 600; color: var(--text-main); text-decoration: none;">
                            <i class="fa-solid fa-calendar-check" style="color: #16a34a; width: 16px;"></i> Lịch Khảo Sát
                        </a>
                        @if(auth()->user()->isStaff())
                            <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 13px; font-weight: 700; color: #b8935c; text-decoration: none; border-top: 1px dashed var(--border-line);">
                                <i class="fa-solid fa-gear" style="width: 16px;"></i> Quản Trị Showroom
                            </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0; border-top: 1px solid var(--border-line);">
                            @csrf
                            <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 8px 16px; font-size: 13px; font-weight: 600; color: #dc2626; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-arrow-right-from-bracket" style="width: 16px;"></i> Đăng Xuất
                            </button>
                        </form>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 4px;">
                        @csrf
                        <button type="submit" title="Đăng xuất" class="btn-logout-icon">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const pill = document.querySelector('.user-profile-pill');
                    const dropdown = document.querySelector('.user-dropdown-menu');
                    if (pill && dropdown) {
                        pill.addEventListener('mouseenter', () => dropdown.style.display = 'block');
                        pill.addEventListener('mouseleave', () => dropdown.style.display = 'none');
                    }
                });
                </script>
            @else
                <a href="{{ route('login') }}" class="btn-auth-login">
                    <i class="fa-regular fa-user" style="color: var(--primary, #b8935c);"></i>
                    <span>Đăng Nhập</span>
                </a>
            @endauth
        </div>
    </div>
</header>
