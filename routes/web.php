<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\ProductListController;
use App\Http\Controllers\User\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//user rotues

Route::get('/', [UserController::class,'index'])->name('home');

//user route
Route::get('/dashboard', [DashboardController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');

//add to cart
Route::prefix('cart')->controller(CartController::class)->group(function(){
    Route::get('view','view')->name('cart.view');
    Route::post('store/{product}','store')->name('cart.store');
    Route::patch('update/{product}','update')->name('cart.update');
    Route::delete('delete/{product}','delete')->name('cart.delete');

});

Route::prefix('products')->controller(ProductListController::class)->group(function(){
    Route::get('/','index')->name('products.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('checkout')->controller(CheckoutController::class)->group(function(){
        Route::post('order','store')->name('checkout.store');
        Route::get('success','success')->name('checkout.success');
        Route::get('cancel','cancel')->name('checkout.cancel');
        // Route::post('payment','vnpay')->name('checkout.vnpay');
    });
    // Route::post('/vnpay',[PaymentController::class,'index'])->name('vnpay.index');;
});
Route::post('/vnpay',[CheckoutController::class,'vnpay'])->name('checkout.vnpay');;

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
    Route::delete('/products/image/{id}',[ProductController::class,'deleteImage'])->name('admin.products.image.delete');
    Route::post('/products/upload-images', [ProductController::class, 'upload'])->name('admin.products.image');
    Route::delete('/products/destroy/{id}',[ProductController::class,'destroy'])->name('admin.products.destroy');
    //brand route
    Route::get('/brands',[BrandController::class,'index'])->name('admin.brands.index');
    Route::post('/brands/store',[BrandController::class,'store'])->name('admin.brands.store');
    Route::put('/brands/update/{id}', [BrandController::class, 'update'])->name('admin.brands.update');
    Route::delete('/brands/destroy/{id}',[BrandController::class,'destroy'])->name('admin.brands.destroy');
    //category route
    Route::get('/categories',[CategoryController::class,'index'])->name('admin.categories.index');
    Route::post('/categories/store',[CategoryController::class,'store'])->name('admin.categories.store');
    Route::put('/categories/update/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/destroy/{id}',[CategoryController::class,'destroy'])->name('admin.categories.destroy');
    //order route
    Route::get('/orders',[OrderController::class,'index'])->name('admin.orders.index');
    Route::put('/orders/update/{id}', [OrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/destroy/{id}',[OrderController::class,'destroy'])->name('admin.orders.destroy');
});

require __DIR__.'/auth.php';

