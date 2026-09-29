<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CurtainOptionController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ConsultationController as AdminConsultationController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReviewController as CustomerReviewController;
use App\Http\Controllers\NewsController as CustomerNewsController;
use App\Http\Controllers\CustomerFaqController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\BotRuleController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\AuditLogController;

/*
|--------------------------------------------------------------------------
| Web Routes - Hệ Thống Bán & Quản Lý Rèm Cửa Cao Cấp (CurtainLux)
|--------------------------------------------------------------------------
*/

// Storefront Routes
Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/san-pham/{slug}', [ShopController::class, 'show'])->name('shop.show');

// News & Curtain Consultation Guides (Tin tức & Cẩm nang rèm)
Route::get('/tin-tuc', [CustomerNewsController::class, 'index'])->name('news.index');
Route::get('/tin-tuc/{slug}', [CustomerNewsController::class, 'show'])->name('news.show');

// FAQs (Hỏi đáp thường gặp)
Route::get('/faq', [CustomerFaqController::class, 'index'])->name('faq.index');
Route::get('/cau-hoi-thuong-gap', [CustomerFaqController::class, 'index']);

// Customer Product Reviews & Ratings (Đánh giá sao & nhận xét)
Route::post('/danh-gia', [CustomerReviewController::class, 'store'])->name('reviews.store');

// Cart & Dimension Orders
Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
Route::post('/gio-hang/cap-nhat/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/gio-hang/xoa/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/gio-hang/dat-hang', [CartController::class, 'checkout'])->name('cart.checkout');

// Order Confirmation & VietQR Payment (Complexus ecosystem)
Route::get('/don-hang/xac-nhan/{orderCode}', [CartController::class, 'orderSuccess'])->name('order.success');
Route::get('/tra-cuu-don-hang', [\App\Http\Controllers\PaymentController::class, 'tracking'])->name('order.tracking');
Route::get('/api/payment/status/{orderCode}', [\App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('payment.status');
Route::post('/api/payment/sandbox-simulate/{orderCode}', [\App\Http\Controllers\PaymentController::class, 'sandboxSimulate'])->name('payment.sandbox-simulate');
Route::post('/api/sepay/webhook', [\App\Http\Controllers\PaymentController::class, 'sepayWebhook'])->name('payment.sepay.webhook');
Route::post('/sepay/webhook', [\App\Http\Controllers\PaymentController::class, 'sepayWebhook']);

// Online Payment Gateway (VNPAY Sandbox)
Route::get('/thanh-toan/vnpay/{orderCode}', [\App\Http\Controllers\VnpayPaymentController::class, 'showGateway'])->name('payment.vnpay');
Route::get('/thanh-toan/vnpay-callback', [\App\Http\Controllers\VnpayPaymentController::class, 'callback'])->name('payment.vnpay.callback');
Route::get('/thanh-toan/thanh-cong/{orderCode}', [\App\Http\Controllers\VnpayPaymentController::class, 'success'])->name('payment.vnpay.success');
Route::get('/thanh-toan/that-bai/{orderCode}', [\App\Http\Controllers\VnpayPaymentController::class, 'failed'])->name('payment.vnpay.failed');
Route::post('/thanh-toan/doi-phuong-thuc/{orderCode}', [\App\Http\Controllers\VnpayPaymentController::class, 'changeMethod'])->name('payment.change-method');

// Home Survey & Consultation Booking
Route::post('/dat-lich-khao-sat', [ConsultationController::class, 'store'])->name('consultation.store');

// Wishlist (Danh sách rèm yêu thích)
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

// Live Chat & CurtainBot API
Route::post('/api/chat/init', [ChatController::class, 'init'])->name('chat.init');
Route::post('/api/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::get('/api/chat/poll/{id}', [ChatController::class, 'poll'])->name('chat.poll');

// =========================================================================
// Customer Authentication & Password Reset Routes
// =========================================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Customer Password Recovery
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Social Authentication (Google, Facebook, Zalo)
Route::get('/auth/social/{provider}', [\App\Http\Controllers\SocialAuthController::class, 'redirect'])->name('auth.social.redirect');
Route::get('/auth/social/{provider}/callback', [\App\Http\Controllers\SocialAuthController::class, 'callback'])->name('auth.social.callback');
Route::get('/auth/social/{provider}/sandbox', [\App\Http\Controllers\SocialAuthController::class, 'sandboxLogin'])->name('auth.social.sandbox');

// Backward compatibility aliases
Route::get('/auth/login', fn() => redirect()->route('login'))->name('auth.login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login.submit');
Route::get('/auth/register', fn() => redirect()->route('register'))->name('auth.register');
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register.submit');
Route::get('/auth/admin-login', fn() => redirect()->route('admin.login'))->name('auth.admin-login');

// =========================================================================
// Customer Account Portal (Trang quản lý tài khoản khách hàng)
// =========================================================================
Route::middleware(['signed'])->group(function () {
    Route::get('/bao-gia/{code}', [\App\Http\Controllers\CustomerAccountController::class, 'quotationDetail'])->name('customer.quotation.guest.detail');
    Route::post('/bao-gia/{code}/duyet', [\App\Http\Controllers\CustomerAccountController::class, 'acceptQuotation'])->name('customer.quotation.guest.accept');
    Route::post('/bao-gia/{code}/yeu-cau-sua', [\App\Http\Controllers\CustomerAccountController::class, 'requestQuotationRevision'])->name('customer.quotation.guest.request-revision');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/tai-khoan', [\App\Http\Controllers\CustomerAccountController::class, 'profile'])->name('customer.profile');
    Route::put('/tai-khoan/ho-so', [\App\Http\Controllers\CustomerAccountController::class, 'updateProfile'])->name('customer.profile.update');
    Route::put('/tai-khoan/doi-mat-khau', [\App\Http\Controllers\CustomerAccountController::class, 'updatePassword'])->name('customer.password.update');
    Route::get('/tai-khoan/don-hang', [\App\Http\Controllers\CustomerAccountController::class, 'orders'])->name('customer.orders');
    Route::get('/tai-khoan/don-hang/{orderCode}', [\App\Http\Controllers\CustomerAccountController::class, 'orderDetail'])->name('customer.order.detail');
    Route::post('/tai-khoan/don-hang/{orderCode}/huy', [\App\Http\Controllers\CustomerAccountController::class, 'cancelOrder'])->name('customer.order.cancel');
    Route::get('/tai-khoan/lich-khao-sat', [\App\Http\Controllers\CustomerAccountController::class, 'consultations'])->name('customer.consultations');
    Route::get('/tai-khoan/bao-gia/{code}', [\App\Http\Controllers\CustomerAccountController::class, 'quotationDetail'])->name('customer.quotation.detail');
    Route::post('/tai-khoan/bao-gia/{code}/duyet', [\App\Http\Controllers\CustomerAccountController::class, 'acceptQuotation'])->name('customer.quotation.accept');
    Route::post('/tai-khoan/bao-gia/{code}/yeu-cau-sua', [\App\Http\Controllers\CustomerAccountController::class, 'requestQuotationRevision'])->name('customer.quotation.request-revision');
});

// =========================================================================
// Admin Authentication (Phân tách hoàn toàn URL Quản trị)
// =========================================================================
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');
Route::get('/admin/forgot-password', [AuthController::class, 'showAdminForgotPassword'])->name('admin.password.request');

// =========================================================================
// Admin Panel Routes (Prefix: /admin) - Protected with 'admin' Middleware
// =========================================================================
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    // 1. Dashboard (Mọi nhân viên có thể xem tổng quan)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Statistics & Reports
    Route::middleware('permission:statistics.view')->group(function () {
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
        Route::get('/statistics/export-excel', [StatisticsController::class, 'exportExcel'])->name('statistics.export-excel');
    });

    // 2. Products & Categories (View, Create, Edit, Delete)
    Route::middleware('permission:products.create')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    });

    Route::middleware('permission:products.view')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    });

    Route::middleware('permission:products.edit')->group(function () {
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::match(['put', 'patch'], '/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/{id}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle');

        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::match(['put', 'patch'], '/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::post('/categories/{id}/toggle', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');
    });

    Route::middleware('permission:products.delete')->group(function () {
        Route::delete('/products/images/{id}', [ProductController::class, 'deleteImage'])->name('products.images.destroy');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');

        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/bulk-action', [CategoryController::class, 'bulkAction'])->name('categories.bulk-action');
    });

    // Options & Accessories
    Route::middleware('permission:options.manage')->group(function () {
        Route::get('/options', [CurtainOptionController::class, 'index'])->name('options.index');
        Route::post('/options/group', [CurtainOptionController::class, 'storeGroup'])->name('options.store-group');
        Route::put('/options/group/{id}', [CurtainOptionController::class, 'updateGroup'])->name('options.update-group');
        Route::delete('/options/group/{id}', [CurtainOptionController::class, 'destroyGroup'])->name('options.destroy-group');
        Route::post('/options/group/{id}/toggle', [CurtainOptionController::class, 'toggleGroupStatus'])->name('options.toggle-group');

        Route::post('/options/value', [CurtainOptionController::class, 'storeValue'])->name('options.store-value');
        Route::put('/options/value/{id}', [CurtainOptionController::class, 'updateValue'])->name('options.update-value');
        Route::post('/options/value/{id}/toggle', [CurtainOptionController::class, 'toggleValueStatus'])->name('options.toggle-value');
        Route::delete('/options/value/{id}', [CurtainOptionController::class, 'destroyValue'])->name('options.destroy-value');
        Route::post('/options/bulk-action', [CurtainOptionController::class, 'bulkAction'])->name('options.bulk-action');
    });

    // 3. Orders & Consultations
    Route::middleware('permission:orders.view')->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    });

    Route::middleware('permission:orders.status')->group(function () {
        Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{id}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
        Route::post('/orders/bulk-action', [OrderController::class, 'bulkAction'])->name('orders.bulk-action');
    });

    Route::middleware('permission:consultations.view')->group(function () {
        Route::get('/consultations', [AdminConsultationController::class, 'index'])->name('consultations.index');
        Route::get('/consultations/{id}', [AdminConsultationController::class, 'show'])->name('consultations.show');
    });

    Route::middleware('permission:consultations.assign')->group(function () {
        Route::post('/consultations/{id}/status', [AdminConsultationController::class, 'updateStatus'])->name('consultations.update-status');

        // Window measurements
        Route::post('/consultations/{id}/windows', [\App\Http\Controllers\Admin\ConsultationWindowController::class, 'store'])->name('consultation-windows.store');
        Route::put('/consultation-windows/{id}', [\App\Http\Controllers\Admin\ConsultationWindowController::class, 'update'])->name('consultation-windows.update');
        Route::delete('/consultation-windows/{id}', [\App\Http\Controllers\Admin\ConsultationWindowController::class, 'destroy'])->name('consultation-windows.destroy');

        // Quotations
        Route::post('/consultations/{id}/quotations/generate', [\App\Http\Controllers\Admin\QuotationController::class, 'generate'])->name('quotations.generate');
        Route::put('/quotations/{id}', [\App\Http\Controllers\Admin\QuotationController::class, 'update'])->name('quotations.update');
        Route::post('/quotations/{id}/send', [\App\Http\Controllers\Admin\QuotationController::class, 'sendToCustomer'])->name('quotations.send');
        Route::post('/quotations/{id}/convert-order', [\App\Http\Controllers\Admin\QuotationController::class, 'convertToOrder'])->name('quotations.convert-order');
    });

    // Discounts / Vouchers
    Route::middleware('permission:discounts.manage')->group(function () {
        Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
        Route::get('/discounts/create', [DiscountController::class, 'create'])->name('discounts.create');
        Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');
        Route::get('/discounts/{id}/edit', [DiscountController::class, 'edit'])->name('discounts.edit');
        Route::put('/discounts/{id}', [DiscountController::class, 'update'])->name('discounts.update');
        Route::post('/discounts/{id}/toggle', [DiscountController::class, 'toggle'])->name('discounts.toggle');
        Route::delete('/discounts/{id}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
    });

    // 4. Customers & Reviews
    Route::middleware('permission:customers.view')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
        Route::post('/customers/{id}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle');
    });

    Route::middleware('permission:reviews.manage')->group(function () {
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{id}/toggle', [ReviewController::class, 'toggleStatus'])->name('reviews.toggle');
        Route::post('/reviews/{id}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
        Route::delete('/reviews/{id}/reply', [ReviewController::class, 'deleteReply'])->name('reviews.delete-reply');
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // 5. Banners & News
    Route::middleware('permission:banners.manage')->group(function () {
        Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
        Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
        Route::get('/banners/{id}/edit', [BannerController::class, 'edit'])->name('banners.edit');
        Route::put('/banners/{id}', [BannerController::class, 'update'])->name('banners.update');
        Route::post('/banners/{id}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');
        Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');
    });

    Route::middleware('permission:news.manage')->group(function () {
        Route::get('/news', [NewsController::class, 'index'])->name('news.index');
        Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
        Route::post('/news', [NewsController::class, 'store'])->name('news.store');
        Route::get('/news/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
        Route::put('/news/{id}', [NewsController::class, 'update'])->name('news.update');
        Route::post('/news/{id}/toggle', [NewsController::class, 'toggle'])->name('news.toggle');
        Route::delete('/news/{id}', [NewsController::class, 'destroy'])->name('news.destroy');

        Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index');
        Route::post('/faqs', [FaqController::class, 'store'])->name('faqs.store');
        Route::put('/faqs/{id}', [FaqController::class, 'update'])->name('faqs.update');
        Route::post('/faqs/{id}/toggle', [FaqController::class, 'toggle'])->name('faqs.toggle');
        Route::delete('/faqs/{id}', [FaqController::class, 'destroy'])->name('faqs.destroy');
    });

    // 6. Staff & Settings (Chỉ Super Admin và người được cấp quyền staff.manage)
    Route::middleware('permission:staff.manage')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::post('/staff/{id}/toggle', [StaffController::class, 'toggleStatus'])->name('staff.toggle');

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::post('/roles/{id}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    });

    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // 7. Live Chat
    Route::middleware('permission:customers.view,staff.manage')->group(function () {
        Route::get('/chats', [AdminChatController::class, 'index'])->name('chats.index');
        Route::get('/chats/{id}/messages', [AdminChatController::class, 'getMessages'])->name('chats.messages');
        Route::post('/chats/{id}/reply', [AdminChatController::class, 'reply'])->name('chats.reply');
        Route::post('/chats/{id}/takeover', [AdminChatController::class, 'takeover'])->name('chats.takeover');
        Route::post('/chats/{id}/close', [AdminChatController::class, 'close'])->name('chats.close');
        Route::get('/chats/{id}/poll', [AdminChatController::class, 'poll'])->name('chats.poll');
    });

    // 8. Bot Rules (Strictly for staff.manage)
    Route::middleware('permission:staff.manage')->group(function () {
        Route::get('/bot-rules', [BotRuleController::class, 'index'])->name('bot-rules.index');
        Route::post('/bot-rules', [BotRuleController::class, 'store'])->name('bot-rules.store');
        Route::put('/bot-rules/{id}', [BotRuleController::class, 'update'])->name('bot-rules.update');
        Route::post('/bot-rules/{id}/toggle', [BotRuleController::class, 'toggle'])->name('bot-rules.toggle');
        Route::delete('/bot-rules/{id}', [BotRuleController::class, 'destroy'])->name('bot-rules.destroy');
        Route::post('/bot-rules/bulk-action', [BotRuleController::class, 'bulkAction'])->name('bot-rules.bulk-action');
    });
});

// =========================================================================
// Fallback Route: Tự động điều hướng về trang chủ khi URL không tồn tại
// =========================================================================
Route::fallback(function () {
    return redirect()->route('shop.index')->with('info', 'Địa chỉ đường dẫn không tồn tại hoặc đã thay đổi. Hệ thống đã đưa bạn về trang chủ CurtainLux.');
});
