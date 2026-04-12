<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\BookController;
use App\Http\Controllers\Student\DigitalNoteController;
use App\Http\Controllers\Student\TransactionController;
use App\Http\Controllers\Student\FavoriteController;

// مسارات الطالب (يجب أن يكون مسجلاً للدخول)
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    
    // الصفحة الرئيسية للطالب
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // مسارات الـ CRUD للكتب والملخصات
    Route::resource('books', BookController::class);
    Route::resource('notes', DigitalNoteController::class);
    
    // مسارات العمليات والمفضلة
    Route::resource('transactions', TransactionController::class);
    Route::resource('favorites', FavoriteController::class)->only(['index', 'store', 'destroy']);
    
});