<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::controller(ProductController::class)->name('products.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{product}/show', 'show')->name('show');
    Route::get('/{product}/edit', 'edit')->name('edit');
    Route::put('/{product}', 'update')->name('update');
    Route::delete('/{product}', 'destroy')->name('destroy');
});

Route::controller(HomeController::class)->group(function () {
    Route::get('/product/index', 'index')->name('index');
    Route::get('/product/{product}', 'show')->name('product.details');

    // Wishlist (Like) System
    Route::get('/wishlist', 'wishlist')->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', 'toggleWishlist')->name('wishlist.toggle');

    // Cart System
    Route::get('/cart', 'cart')->name('cart.index');
    Route::post('/cart/add/{product}', 'addToCart')->name('cart.add');
    Route::post('/cart/update/{id}', 'updateCart')->name('cart.update');
    Route::delete('/cart/remove/{id}', 'removeCart')->name('cart.remove');

    // Checkout & Order
    Route::get('/checkout', 'checkout')->name('checkout');
    Route::post('/order/place', 'placeOrder')->name('order.store');
    Route::get('/orders', 'orders')->name('order.index');
});
