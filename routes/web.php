<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::post('enquiry', [EnquiryController::class, 'store'])->middleware('throttle:5,1')->name('enquiry.store');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
