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
    Route::get('/product/{product}', 'show');
    Route::post('/product-toggle/{product}', 'toggle')->name('product.toggle');
    Route::post('/cart-sync/{product}', 'cartSync');
    Route::post('/checkout', 'checkout')->name('checkout');
});
