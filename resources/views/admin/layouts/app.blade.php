@php
    $currentUser = auth()->user();
    $initials = $currentUser ? $currentUser->initials : 'AD';
    $pendingConsultationsCount = \App\Models\Consultation::where('status', 'pending')->count();
    $pendingOrdersCount = \App\Models\Order::where('order_status', 'pending')->count();
    $waitingChatsCount = \App\Models\ChatConversation::whereIn('status', ['bot', 'waiting_staff'])->count();
@endphp
<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản Trị Rèm Cửa') — CurtainLux Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- External CSS (Strictly decoupled, matching Complexus architecture) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ file_exists(public_path('css/admin.css')) ? filemtime(public_path('css/admin.css')) : time() }}">
    <link rel="stylesheet" href="{{ asset('css/notification.css') }}">
    <link rel="stylesheet" href="{{ asset('css/curtain-notify.css') }}">
    
    @stack('styles')
</head>
<body>
<script>
    if (localStorage.getItem('admin_sidebar_collapsed') === 'true' && window.innerWidth > 1024) {
        document.documentElement.classList.add('admin-collapsed-preload');
    }
</script>
<div class="admin-layout" id="adminLayout">
    <!-- ═════════════════════════════════════════════════════════════════
         SIDEBAR NAVIGATION
    ═════════════════════════════════════════════════════════════════ -->
    <div class="admin-sidebar-backdrop"></div>
    <aside class="admin-sidebar" id="adminSidebar">
        <!-- Logo -->
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
            <i class="fa-solid fa-person-shelter" style="font-size: 22px; color: var(--brand); margin-right: 8px;"></i>
            <span class="sidebar-logo-text" style="font-size: 15px; font-weight: 800; letter-spacing: 0.05em; color: var(--on-surface);">CURTAIN LUX</span>
        </a>

        <!-- Navigation Groups -->
        <nav class="sidebar-nav" aria-label="Admin navigation">
            <ul class="nav-groups-list">
                <!-- Group 1: Tổng Quan & Báo Cáo -->
                <li class="nav-group" data-title="Tổng Quan & Báo Cáo">
                    <button type="button" class="group-header-btn expanded" aria-expanded="true">
                        <div class="group-header-left">
                            <i class="fa-solid fa-chart-pie sidebar-group-icon"></i>
                            <span class="group-title">Tổng Quan & Báo Cáo</span>
                        </div>
                        <i class="fa-solid fa-chevron-down chevron-icon"></i>
                    </button>
                    <ul class="sub-menu-list expanded">
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.dashboard') }}" class="sub-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Bảng điều khiển</span>
                            </a>
                        </li>
                        @if($currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('statistics.view')))
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.statistics') }}" class="sub-nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Thống kê doanh thu</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                <!-- Group 2: Quản Lý Sản Phẩm Rèm -->
                @if($currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('products.view') || $currentUser->hasPermission('options.manage')))
                <li class="nav-group" data-title="Sản Phẩm & Phụ Kiện">
                    <button type="button" class="group-header-btn expanded" aria-expanded="true">
                        <div class="group-header-left">
                            <i class="fa-solid fa-boxes-stacked sidebar-group-icon"></i>
                            <span class="group-title">Sản Phẩm & Phụ Kiện</span>
                        </div>
                        <i class="fa-solid fa-chevron-down chevron-icon"></i>
                    </button>
                    <ul class="sub-menu-list expanded">
                        @if($currentUser->isAdmin() || $currentUser->hasPermission('products.view'))
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.products.index') }}" class="sub-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Danh sách mẫu rèm</span>
                            </a>
                        </li>
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.categories.index') }}" class="sub-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Danh mục loại rèm</span>
                            </a>
                        </li>
                        @endif
                        @if($currentUser->isAdmin() || $currentUser->hasPermission('options.manage'))
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.options.index') }}" class="sub-nav-link {{ request()->routeIs('admin.options.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Tùy chọn ray & phụ kiện</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @php
                    $canSeeOrders = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('orders.view'));
                    $canSeeConsultations = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('consultations.view'));
                    $canSeeDiscounts = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('discounts.manage'));
                    $canSeeChat = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('customers.view') || $currentUser->hasPermission('staff.manage'));
                    $canSeeBotRules = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('staff.manage'));
                @endphp
                @if($canSeeOrders || $canSeeConsultations || $canSeeDiscounts || $canSeeChat || $canSeeBotRules)
                <!-- Group 3: Bán Hàng & Đo Đạc -->
                <li class="nav-group {{ ($pendingOrdersCount > 0 || $pendingConsultationsCount > 0 || $waitingChatsCount > 0) ? 'has-badge' : '' }}" data-title="Bán Hàng & Đo Đạc">
                    <button type="button" class="group-header-btn expanded" aria-expanded="true">
                        <div class="group-header-left">
                            <i class="fa-solid fa-cart-shopping sidebar-group-icon"></i>
                            <span class="group-title">Bán Hàng & Đo Đạc</span>
                        </div>
                        <i class="fa-solid fa-chevron-down chevron-icon"></i>
                    </button>
                    <ul class="sub-menu-list expanded">
                        @if($canSeeOrders)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.orders.index') }}" class="sub-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Đơn đặt rèm</span>
                                @if($pendingOrdersCount > 0)
                                    <span class="badge badge-warning" style="margin-left: auto; font-size: 10px;">{{ $pendingOrdersCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endif
                        @if($canSeeConsultations)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.consultations.index') }}" class="sub-nav-link {{ request()->routeIs('admin.consultations.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Lịch hẹn đo đạc</span>
                                @if($pendingConsultationsCount > 0)
                                    <span class="badge badge-warning" style="margin-left: auto; font-size: 10px;">{{ $pendingConsultationsCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endif
                        @if($canSeeDiscounts)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.discounts.index') }}" class="sub-nav-link {{ request()->routeIs('admin.discounts.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Mã giảm giá (Coupon)</span>
                            </a>
                        </li>
                        @endif
                        @if($canSeeChat)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.chats.index') }}" class="sub-nav-link {{ request()->routeIs('admin.chats.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Bàn trực Live Chat</span>
                                @if($waitingChatsCount > 0)
                                    <span class="badge badge-warning" style="margin-left: auto; font-size: 10px;">{{ $waitingChatsCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endif
                        @if($canSeeBotRules)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.bot-rules.index') }}" class="sub-nav-link {{ request()->routeIs('admin.bot-rules.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Kịch bản Chatbot</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @php
                    $canSeeCustomers = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('customers.view'));
                    $canSeeReviews = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('reviews.manage'));
                @endphp
                @if($canSeeCustomers || $canSeeReviews)
                <!-- Group 4: Khách Hàng & Đánh Giá -->
                <li class="nav-group" data-title="Khách Hàng & Đánh Giá">
                    <button type="button" class="group-header-btn expanded" aria-expanded="true">
                        <div class="group-header-left">
                            <i class="fa-solid fa-users sidebar-group-icon"></i>
                            <span class="group-title">Khách Hàng & Đánh Giá</span>
                        </div>
                        <i class="fa-solid fa-chevron-down chevron-icon"></i>
                    </button>
                    <ul class="sub-menu-list expanded">
                        @if($canSeeCustomers)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.customers.index') }}" class="sub-nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Danh sách khách hàng</span>
                            </a>
                        </li>
                        @endif
                        @if($canSeeReviews)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.reviews.index') }}" class="sub-nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Đánh giá sản phẩm</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @php
                    $canSeeBanners = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('banners.manage'));
                    $canSeeNews = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('news.manage'));
                @endphp
                @if($canSeeBanners || $canSeeNews)
                <!-- Group 5: Nội Dung & Marketing -->
                <li class="nav-group" data-title="Nội Dung & Marketing">
                    <button type="button" class="group-header-btn expanded" aria-expanded="true">
                        <div class="group-header-left">
                            <i class="fa-solid fa-bullhorn sidebar-group-icon"></i>
                            <span class="group-title">Nội Dung & Marketing</span>
                        </div>
                        <i class="fa-solid fa-chevron-down chevron-icon"></i>
                    </button>
                    <ul class="sub-menu-list expanded">
                        @if($canSeeBanners)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.banners.index') }}" class="sub-nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Banner & Quảng cáo</span>
                            </a>
                        </li>
                        @endif
                        @if($canSeeNews)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.news.index') }}" class="sub-nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Cẩm nang chọn rèm</span>
                            </a>
                        </li>
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.faqs.index') }}" class="sub-nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Câu hỏi thường gặp (FAQ)</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Group 6: Nhân Sự & Cài Đặt Hệ Thống -->
                @php
                    $canManageStaff = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('staff.manage'));
                    $canManageSettings = $currentUser && ($currentUser->isAdmin() || $currentUser->hasPermission('settings.manage'));
                @endphp
                @if($canManageStaff || $canManageSettings)
                <li class="nav-group" data-title="Hệ Thống & Quản Trị">
                    <button type="button" class="group-header-btn expanded" aria-expanded="true">
                        <div class="group-header-left">
                            <i class="fa-solid fa-shield-halved sidebar-group-icon"></i>
                            <span class="group-title">Hệ Thống & Quản Trị</span>
                        </div>
                        <i class="fa-solid fa-chevron-down chevron-icon"></i>
                    </button>
                    <ul class="sub-menu-list expanded">
                        @if($canManageStaff)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.staff.index') }}" class="sub-nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Quản lý nhân sự</span>
                            </a>
                        </li>
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.roles.index') }}" class="sub-nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Vai trò & Phân quyền</span>
                            </a>
                        </li>
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.audit-logs.index') }}" class="sub-nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Nhật ký hoạt động</span>
                            </a>
                        </li>
                        @endif
                        @if($canManageSettings)
                        <li class="sub-menu-item">
                            <a href="{{ route('admin.settings.index') }}" class="sub-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <span class="sub-nav-dot"></span>
                                <span class="sub-nav-label">Cài đặt hệ thống</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
            </ul>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer" style="display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ route('shop.index') }}" target="_blank" class="btn-preview-store" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; font-size: 13px; font-weight: 600; background: var(--surface-alt); border: 1px solid var(--border); border-radius: var(--radius-md); color: var(--on-surface);">
                <i class="fa-solid fa-store"></i>
                <span class="footer-link-text">Xem Cửa Hàng &rarr;</span>
            </a>

            <button type="button" class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Thu gọn menu" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 8px 10px; font-size: 12px; font-weight: 600; background: transparent; border: 1px dashed var(--border); border-radius: var(--radius-md); color: var(--on-surface-variant); cursor: pointer; transition: all 0.2s ease;">
                <i class="fa-solid fa-angles-left collapse-icon" style="font-size: 13px; transition: transform 0.2s ease;"></i>
                <span class="collapse-text">Thu gọn sidebar</span>
            </button>
        </div>
    </aside>

    <!-- ═════════════════════════════════════════════════════════════════
         MAIN CONTENT WRAPPER & TOPBAR
    ═════════════════════════════════════════════════════════════════ -->
    <div class="admin-content">
        <header class="admin-topbar">
            <div class="topbar-left">
                <button type="button" class="admin-menu-toggle" data-admin-sidebar-toggle aria-controls="adminSidebar" title="Thu gọn / Mở rộng thanh điều hướng">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Bảng Điều Khiển')</h1>
            </div>

            <div class="topbar-right">
                <!-- Storefront link -->
                <a href="{{ route('shop.index') }}" target="_blank" class="btn btn-secondary btn-sm" title="Mở trang chủ khách hàng">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Storefront
                </a>

                <!-- Theme toggle button -->
                <button class="admin-theme-toggle" id="adminThemeToggle" type="button" title="Chuyển chế độ Sáng / Tối" style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:var(--surface-alt);border:1px solid var(--border);border-radius:8px;color:var(--on-surface);">
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>

                <!-- User Profile & Avatar -->
                <div class="user-profile-menu" style="display: flex; align-items: center; gap: 10px;">
                    <div class="user-avatar-circle" style="width: 36px; height: 36px; border-radius: 50%; background: var(--brand); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">
                        {{ $initials }}
                    </div>
                    <div class="user-info-wrap" style="line-height: 1.2;">
                        <span class="user-display-name" style="font-weight: 700; font-size: 13px; color: var(--on-surface); display: block;">
                            {{ $currentUser->name ?? 'Quản Trị Viên' }}
                        </span>
                        <span class="badge" style="background: #16a34a; color: #ffffff; font-size: 10px; padding: 1px 6px; border-radius: 4px; font-weight: 700;">
                            {{ $currentUser && $currentUser->isAdmin() ? 'Super Admin' : 'Kỹ Thuật Viên' }}
                        </span>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0 0 0 6px;">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm" title="Đăng xuất khỏi trang quản trị" style="padding: 6px 10px; color: #dc2626;">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="admin-main">
            @if(session('success'))
                <div class="alert alert-success" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; margin-bottom: 20px; background: #d1fae5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 8px;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; margin-bottom: 20px; background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 8px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger" style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; margin-bottom: 20px; background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 8px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- Decoupled JavaScript Files -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/curtain-notify.js') }}"></script>
<script src="{{ asset('js/notification.js') }}"></script>
<script src="{{ asset('js/admin.js') }}?v={{ file_exists(public_path('js/admin.js')) ? filemtime(public_path('js/admin.js')) : time() }}"></script>
<script src="{{ asset('js/admin-charts.js') }}"></script>
@stack('scripts')
</body>
</html>
