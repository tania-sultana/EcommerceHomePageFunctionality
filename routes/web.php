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

    Route::get('/home', 'index')->name('index');

    // Data API (Product Load)
    Route::get('/api/products', 'getProducts')->name('api.products');

    // Action API
    Route::post('/cart/add/{id}', 'addToCart')->name('cart.add');
    Route::post('/wishlist/toggle/{id}', 'toggleWishlist')->name('wishlist.toggle');
    Route::get('/product-details/{id}', 'show')->name('product.details');

    // Drawer/Content APIs
    Route::get('/wishlist-content', 'getWishlistContent')->name('wishlist.index');
    Route::get('/cart-content', 'getCartContent')->name('cart.index');
    Route::post('/checkout-details', 'getCheckoutDetails')->name('checkout.details');
    Route::get('/order-history', 'getOrdersContent')->name('order.index');
});