<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
});

// Authenticated routes (auth protection handled client-side by Alpine.js)
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');

Route::get('/orders/{orderNumber}/payment', [PaymentController::class, 'create'])->name('payments.create');

Route::get('/orders/{orderNumber}/tracking', [ShipmentController::class, 'show'])->name('shipments.tracking');

Route::get('/rewards', function () {
    return view('rewards.index');
})->name('rewards.index');

Route::get('/profile', function () {
    return view('profile.edit');
})->name('profile.edit');
