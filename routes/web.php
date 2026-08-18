<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| পাবলিক সাইট
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::post('/track', [TrackController::class, 'store'])->name('track');

Route::get('/media/{name}', [MediaController::class, 'show'])->name('media')->where('name', '[^/]+');

Route::get('/manifest.webmanifest', [ManifestController::class, 'customer'])->name('manifest');
Route::get('/admin/manifest.webmanifest', [ManifestController::class, 'admin'])->name('admin.manifest');

require __DIR__.'/admin.php';
