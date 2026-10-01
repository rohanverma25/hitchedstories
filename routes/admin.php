<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HomeContentController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MailSettingsController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::resource('leads', LeadController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::resource('blogs', BlogController::class)->except('show');
    Route::resource('reviews', ReviewController::class)->except('show');
    Route::resource('services', ServiceController::class)->except('show');

    Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::delete('gallery', [GalleryController::class, 'destroyMany'])->name('gallery.destroy-many');
    Route::delete('gallery/{galleryImage}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

    Route::get('content', [HomeContentController::class, 'edit'])->name('content.edit');
    Route::put('content', [HomeContentController::class, 'update'])->name('content.update');

    Route::get('mail', [MailSettingsController::class, 'edit'])->name('mail.edit');
    Route::put('mail', [MailSettingsController::class, 'update'])->name('mail.update');
    Route::post('mail/test', [MailSettingsController::class, 'test'])->middleware('throttle:5,1')->name('mail.test');

    Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
});
