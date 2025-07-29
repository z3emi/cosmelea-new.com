<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth; // موجود فقط بالكود الثاني
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

// Frontend Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\Auth\WhatsAppVerificationController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;  // <== هنا

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('orders/trash', [OrderController::class, 'trash'])->name('orders.trash');  // استخدم OrderController
    Route::resource('orders', OrderController::class);                                    // استخدم OrderController
});

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DiscountCodeController;
use App\Http\Controllers\Admin\PurchaseInvoiceController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ActivityLogController;

// A) Authentication Routes
Auth::routes(['verify' => true]);

// B) Maintenance & Tracking Routes
Route::get('/maintenance', function () {
    return view('frontend.maintenance');
})->name('maintenance.page');

Route::get('lang/{locale}', function (Request $request, $locale) {
    if (in_array($locale, ['ar', 'en', 'ku'])) {
        Session::put('locale', $locale);
    }

    $redirectUrl = $request->header('referer');
    if (!$redirectUrl || Str::contains($redirectUrl, ['/wishlist/count', '/cart/count'])) {
        $redirectUrl = route('homepage');
    }

    return redirect($redirectUrl);
})->name('lang.switch');

Route::get('/track-order', [OrderTrackingController::class, 'showTrackingForm'])->name('tracking.form');
Route::post('/track-order', [OrderTrackingController::class, 'trackOrder'])->name('tracking.track');

// C) Main Frontend Routes
    Route::get('/', [PageController::class, 'homepage'])->name('homepage');
    Route::get('/home', [PageController::class, 'homepage'])->name('homepage');
    Route::get('/shop', [PageController::class, 'shop'])->name('shop');
    Route::get('/product/{product}', [PageController::class, 'productDetail'])->name('product.detail');
    Route::get('/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy.policy');
    Route::get('/password/reset-phone', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetPhoneForm'])->name('password.reset.custom');
    Route::get('/about-us', fn() => view('frontend.pages.about-us'))->name('about.us');
    Route::get('/order-method', fn() => view('frontend.pages.order-method'))->name('order.method');
    Route::get('/faq', fn() => view('frontend.pages.faq'))->name('faq');    
    Route::get('/categories', [PageController::class, 'categories'])->name('categories.index');
    Route::get('/track-order', [OrderTrackingController::class, 'showTrackingForm'])->name('tracking.form');
    Route::post('/track-order', [OrderTrackingController::class, 'trackOrder'])->name('tracking.track');


    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/apply-discount', [CartController::class, 'applyDiscount'])->name('cart.applyDiscount');
    Route::post('/cart/add-async', [CartController::class, 'store'])->name('cart.store.async');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::get('/cart/content', [CartController::class, 'content'])->name('cart.content');
    Route::post('/cart/remove-discount', [CartController::class, 'removeDiscount'])->name('cart.removeDiscount');
    
    // OTP
    Route::post('/password/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('password.send.otp');
    Route::get('/password/reset-with-otp', [ForgotPasswordController::class, 'showResetFormWithOtp'])->name('password.reset.otp.form');
    Route::post('/password/update-with-otp', [ForgotPasswordController::class, 'resetPasswordWithOtp'])->name('password.update.with.otp');
    Route::get('/password/reset-phone', [ForgotPasswordController::class, 'showResetPhoneForm'])->name('password.reset.phone.form');
    Route::get('/verify-otp', [OtpVerificationController::class, 'show'])->name('otp.verification.show');
    Route::post('/verify-otp', [OtpVerificationController::class, 'verify'])->name('otp.verification.verify');
    Route::post('/resend-otp', [OtpVerificationController::class, 'resend'])->name('otp.verification.resend');
    Route::post('/receive-whatsapp', [WhatsAppWebhookController::class, 'handleIncomingMessage']);




    Route::middleware(['auth'])->group(function () {
        // Checkout
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
        Route::post('/checkout/address/store', [ProfileController::class, 'storeAddressAjax'])->name('checkout.address.store.ajax');

        // Wishlist
        Route::get('/wishlist', [FavoriteController::class, 'index'])->name('wishlist');
        Route::post('/wishlist/toggle/{product}', [FavoriteController::class, 'toggle'])->name('wishlist.toggle');
        Route::post('/wishlist/toggle-async/{product}', [FavoriteController::class, 'toggle'])->name('wishlist.toggle.async');
        Route::get('/wishlist/count', [FavoriteController::class, 'count'])->name('wishlist.count');

        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
        Route::get('/profile/orders/{order}', [ProfileController::class, 'showOrderDetails'])->name('profile.orders.show');

        Route::prefix('profile/addresses')->name('profile.addresses.')->group(function () {
            Route::get('/', [ProfileController::class, 'addresses'])->name('index');
            Route::get('/create', [ProfileController::class, 'createAddress'])->name('create');
            Route::post('/', [ProfileController::class, 'storeAddress'])->name('store');
            Route::delete('/{address}', [ProfileController::class, 'destroyAddress'])->name('destroy');
        });
    });

// E) Admin Panel Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-admin-panel');

    // Resources
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('orders', OrderController::class);  // استخدم OrderController بدل AdminOrderController
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('roles', RoleController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseInvoiceController::class);
    Route::resource('discount-codes', DiscountCodeController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('discount_codes', DiscountCodeController::class);
    
    // Extras
    Route::post('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggleStatus');
    Route::get('orders/trash', [OrderController::class, 'trash'])->name('orders.trash');
    Route::post('orders/trash/{id}/restore', [OrderController::class, 'restore'])->name('orders.restore');
    Route::delete('orders/trash/{id}/force-delete', [OrderController::class, 'forceDelete'])->name('orders.forceDelete');
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('orders/apply-discount', [OrderController::class, 'applyDiscount'])->name('orders.applyDiscount');

    Route::post('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
    Route::post('users/{user}/unban', [UserController::class, 'unban'])->name('users.unban');
    Route::post('users/{user}/force-logout', [UserController::class, 'forceLogout'])->name('users.forceLogout');
    Route::post('users/force-logout-all', [UserController::class, 'forceLogoutAll'])->name('users.forceLogoutAll');
    Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
    Route::post('users/stop-impersonate', [UserController::class, 'stopImpersonate'])->name('users.stopImpersonate');
    Route::get('users/{user}/orders', [UserController::class, 'showUserOrders'])->name('users.orders');
    Route::get('/users/inactive', [UserController::class, 'inactive'])->name('users.inactive');

    Route::post('customers/{customer}/ban', [CustomerController::class, 'ban'])->name('customers.ban');
    Route::post('customers/{customer}/unban', [CustomerController::class, 'unban'])->name('customers.unban');
    Route::get('customers/address/{id}', [CustomerController::class, 'getAddress'])->name('customers.getAddress');

    Route::post('discount-codes/{discount_code}/toggle-status', [DiscountCodeController::class, 'toggleStatus'])->name('discount-codes.toggleStatus');
    Route::get('/discount_codes/create', [\App\Http\Controllers\Admin\DiscountCodeController::class, 'create'])->name('discount_codes.create');
    Route::get('/discount_codes/{discount_code}/edit', [\App\Http\Controllers\Admin\DiscountCodeController::class, 'edit'])->name('discount_codes.edit');

    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/logout-all', [SettingsController::class, 'logoutAllUsers'])->name('settings.logoutAll');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::delete('/products/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
    
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
Route::get('reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
Route::get('reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
Route::delete('/products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('admin.products.images.destroy');
});

// F) Utility Routes
    Route::get('/whatsapp/verify', [WhatsAppVerificationController::class, 'show'])->name('whatsapp.verification.notice');
    Route::post('/whatsapp/verify', [WhatsAppVerificationController::class, 'verify'])->name('whatsapp.verification.verify');
    Route::get('/whatsapp-webhook', [WhatsAppWebhookController::class, 'verify']);
    Route::post('/whatsapp-webhook', [WhatsAppWebhookController::class, 'handle']);
