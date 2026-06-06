<?php

use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/products', function () {
    return view('products.index');
})->name('products.index');

Route::get('/products/{slug}', function () {
    return view('products.show');
})->name('products.show');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        return redirect('/');
    })->name('logout');

    Route::get('/cart', function () {
        return view('cart.index');
    })->name('cart.index');

    Route::get('/checkout', function () {
        return view('checkout.index');
    })->name('checkout.index');

    Route::get('/orders', function () {
        return view('orders.index');
    })->name('orders.index');

    Route::get('/orders/{orderNumber}', function () {
        return view('orders.show');
    })->name('orders.show');

    Route::get('/orders/{orderNumber}/payment', function () {
        return view('payments.create');
    })->name('payments.create');

    Route::get('/orders/{orderNumber}/tracking', function () {
        return view('shipments.tracking');
    })->name('shipments.tracking');

    Route::get('/rewards', function () {
        return view('rewards.index');
    })->name('rewards.index');

    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');
});
