<?php

use App\Http\Controllers\Admin\AdminAuthenticationController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('login', [AdminAuthenticationController::class, 'login'])->name('login');
Route::post('login', [AdminAuthenticationController::class, 'handleLogin'])->name('handle-login');
Route::post('logout', [AdminAuthenticationController::class, 'logout'])->name('logout');

/** Reset Password */
Route::get('forgot-password', [AdminAuthenticationController::class, 'forgotPassword'])->name('forgot-password');
Route::post('forgot-password', [AdminAuthenticationController::class, 'sendResetLink'])->name('forgot-pasword.send');
Route::get('reset-password/{token}', [AdminAuthenticationController::class, 'resetPassword'])->name('reset-password');
Route::post('reset-password', [AdminAuthenticationController::class, 'handleResetPassword'])->name('reset-password.send');


Route::middleware('admin')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /** Profile Route */
    Route::put('profile-password-update/{id}', [ProfileController::class, 'passwordUpdate'])->name('profile-password.update');
    Route::resource('profile', ProfileController::class);

    /** Categories Route */
    Route::resource('categories', CategoryController::class);

    /** Brands Route */
    Route::resource('brands', BrandController::class);

    /** Products Route */
    Route::get('product-copy/{id}', [ProductController::class, 'copyProduct'])->name('product-copy');
    Route::resource('products', ProductController::class);

});
