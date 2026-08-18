<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| অ্যাডমিন প্যানেল — /admin (পাসওয়ার্ড-সুরক্ষিত)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // লগইন (সুরক্ষার বাইরে)
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    Route::middleware('admin.auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('password', [AuthController::class, 'changePassword'])->name('password');

        // অর্ডার
        Route::get('/', [OrderController::class, 'index'])->name('orders');
        Route::post('orders/{id}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('status', [OrderController::class, 'status'])->name('status');
        Route::post('steadfast', [OrderController::class, 'steadfast'])->name('steadfast');
        Route::post('steadfast/bulk', [OrderController::class, 'steadfastBulk'])->name('steadfast.bulk');
        Route::get('labels', [OrderController::class, 'labels'])->name('labels');

        // প্রোডাক্ট
        Route::get('products', [ProductController::class, 'index'])->name('products');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('upload', [ProductController::class, 'upload'])->name('upload');

        // রিভিউ
        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews');
        Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::delete('reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        // অ্যানালিটিক্স
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');

        // সেটিংস
        Route::get('settings', [SettingsController::class, 'edit'])->name('settings');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});
