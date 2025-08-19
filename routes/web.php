<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

/** Shop Routes */
Route::get('shop', [HomeController::class, 'shop'])->name('shop');

/** Product Detail Routes */
Route::get('product/{slug}', [HomeController::class, 'showProduct'])->where('slug', '[A-Za-z0-9\-]+')->name('product-details');

/** Product Comment Routes */
Route::post('product-review', [HomeController::class, 'handleReview'])->name('product-review');

/** Cart Routes */
Route::post('cart/add', [HomeController::class, 'addCart'])->name('cart.add');
Route::get('cart', [HomeController::class, 'cart'])->name('cart');
Route::post('cart/update', [HomeController::class, 'updateCart'])->name('cart.update');
Route::delete('cart/remove/{id}', [HomeController::class, 'removeCart'])->name('cart.remove');

/** Checkout Routes */
Route::get('checkout', [HomeController::class, 'checkout'])->name('checkout');
Route::post('checkout/store', [HomeController::class, 'checkoutStore'])->name('checkout.store');
Route::get('checkout/success/{order}', [HomeController::class, 'checkoutSuccess'])->name('checkout.success');
Route::get('checkout/cancel/{order}', [HomeController::class, 'checkoutCancel'])->name('checkout.cancel');
