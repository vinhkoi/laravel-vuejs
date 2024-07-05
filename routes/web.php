<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductSaleController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\ChirpController;
use App\Http\Controllers\DialogflowController;
use App\Http\Controllers\OrderSummaryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\DetailController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\PcBuilderController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\ProductListController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\WishlistController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//user rotues

Route::get('/', [UserController::class, 'index'])->name('home');
Route::get('/about', [UserController::class, 'about'])->name('about');
Route::get('/contact', [UserController::class, 'contact'])->name('contact');
Route::get('/builder', [UserController::class, 'builder'])->name('builder');
Route::post('/dialogflow-webhook', [DialogflowController::class, 'handleWebhook']);
Route::get('/order-summary/{orderId}', [OrderSummaryController::class, 'show'])->name('order.summary');


//user route
Route::prefix('user')->controller(DashboardController::class)->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'welcome'])->name('user.dashboard');
    Route::get('/order', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('user.order');
    Route::get('/address', [DashboardController::class, 'address'])->name('user.address');
    Route::get('/dashboardd', [DashboardController::class, 'test'])->name('user.dashboardd');
});
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/dashboardd', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboardd');


//wishlist
Route::prefix('wishlist')->controller(WishlistController::class)->group(function () {
    Route::get('view', 'view')->name('wishlist.view');
    Route::post('store/{product}', 'store')->name('wishlist.store');
    Route::delete('delete/{product}', 'delete')->name('wishlist.delete');
});
//add to cart
Route::prefix('cart')->controller(CartController::class)->group(function () {
    Route::get('view', 'view')->name('cart.view');
    Route::post('store/{product}', 'store')->name('cart.store');
    Route::patch('update/{product}', 'update')->name('cart.update');
    Route::delete('delete/{product}', 'delete')->name('cart.delete');
    Route::post('store-multiple', 'storeMultiple')->name('cart.storeMultiple');
});
//detail
Route::prefix('detail')->controller(DetailController::class)->group(function () {
    Route::get('view/{id}', 'view')->name('detail.view');
    Route::post('/view/{id}/store', [DetailController::class, 'store'])->name('detail.store');
    Route::patch('chirps/update{id}', [DetailController::class, 'update'])->name('detail.update');
});
Route::prefix('products')->controller(ProductListController::class)->group(function () {
    Route::get('/', 'index')->name('products.index');
    Route::get('/search', 'search')->name('products.search'); // Thêm route tìm kiếm vào group

});
//search
// Route::get('/products/search', [ProductListController::class, 'search'])->name('product.search');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('checkout')->controller(CheckoutController::class)->group(function () {
        Route::post('order', 'store')->name('checkout.store');
        Route::post('COD', 'COD')->name('checkout.COD');
        Route::get('success', 'success')->name('checkout.success');
        Route::get('cancel', 'cancel')->name('checkout.cancel');
        Route::post('vnpay', 'vnpayPayment')->name('checkout.vnpay')->middleware('csp');;
        Route::get('vnpay/return', 'vnpayReturn')->name('checkout.vnpay.return');
    });
    Route::get('/vnpay', [PaymentController::class, 'vnpayPayment'])->name('vnpay.index');;
});
// Route::resource('chirps', ChirpController::class)
//     ->only(['index', 'store'])
//     ->middleware(['auth', 'verified']);

//admin route
Route::group(['prefix' => 'admin', 'middleware' => 'redirectAdmin'], function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    //product route
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/products/store', [ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/products/update/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/image/{id}', [ProductController::class, 'deleteImage'])->name('admin.products.image.delete');
    Route::post('/products/upload-images', [ProductController::class, 'upload'])->name('admin.products.image');
    Route::delete('/products/destroy/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    //brand route
    Route::get('/brands', [BrandController::class, 'index'])->name('admin.brands.index');
    Route::post('/brands/store', [BrandController::class, 'store'])->name('admin.brands.store');
    Route::put('/brands/update/{id}', [BrandController::class, 'update'])->name('admin.brands.update');
    Route::delete('/brands/destroy/{id}', [BrandController::class, 'destroy'])->name('admin.brands.destroy');
    //category route
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories/store', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/update/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/destroy/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    //order route
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::put('/orders/update/{id}', [OrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/destroy/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
    //payment route
    Route::get('/payments', [PaymentsController::class, 'index'])->name('admin.payments.index');
    Route::post('/payments/store', [PaymentsController::class, 'store'])->name('admin.payments.store');
    Route::put('/payments/update/{id}', [PaymentsController::class, 'update'])->name('admin.payments.update');
    Route::delete('/payments/destroy/{id}', [PaymentsController::class, 'destroy'])->name('admin.payments.destroy');
    //manage user route
    Route::get('/users', [UsersController::class, 'index'])->name('admin.users.index');
    Route::put('/users/update/{id}', [UsersController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/destroy/{id}', [UsersController::class, 'destroy'])->name('admin.users.destroy');
    //manage banner route
    Route::get('/banners', [BannerController::class, 'index'])->name('admin.banners.index');
    Route::post('/banners/store', [BannerController::class, 'store'])->name('admin.banners.store');
    Route::put('/banners/update/{id}', [BannerController::class, 'update'])->name('admin.banners.update');
    Route::delete('/banners/destroy/{id}', [BannerController::class, 'destroy'])->name('admin.banners.destroy');
    //manage flash sale route
    Route::get('/sale', [ProductSaleController::class, 'index'])->name('admin.sale.index');
    Route::post('/sale/store', [ProductSaleController::class, 'store'])->name('admin.sale.store');
    Route::put('/sale/update/{id}', [ProductSaleController::class, 'update'])->name('admin.sale.update');
    Route::delete('/sale/destroy/{id}', [ProductSaleController::class, 'destroy'])->name('admin.sale.destroy');
});
Route::fallback(function () {
    return Inertia::render('NotFound', [], 404);
});
require __DIR__ . '/auth.php';
