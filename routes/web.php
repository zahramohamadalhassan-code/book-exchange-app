<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\BookController;
use App\Http\Controllers\Student\DigitalNoteController;
use App\Http\Controllers\Student\TransactionController;
use App\Http\Controllers\Student\FavoriteController;
use App\Http\Controllers\Student\RatingController;

// ============================
// المسارات العامة (بدون تسجيل دخول)
// ============================

// الصفحة الرئيسية
Route::get('/', [HomeController::class, 'index'])->name('home');

// تصفح الكتب
Route::get('/books', [BrowseController::class, 'books'])->name('books.browse');
Route::get('/books/{book}', [BrowseController::class, 'showBook'])->name('books.show');

// تصفح الملخصات
Route::get('/notes', [BrowseController::class, 'notes'])->name('notes.browse');
Route::get('/notes/{note}', [BrowseController::class, 'showNote'])->name('notes.show');

// ============================
// مسارات الطالب (يجب أن يكون مسجلاً للدخول)
// ============================
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {

    // الصفحة الرئيسية للطالب
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // مسارات الـ CRUD للكتب والملخصات
    Route::resource('books', BookController::class);
    Route::resource('notes', DigitalNoteController::class);

    // مسارات العمليات والمفضلة والتقييمات
    Route::resource('transactions', TransactionController::class);
    Route::resource('favorites', FavoriteController::class)->only(['index', 'store', 'destroy']);
    Route::resource('ratings', RatingController::class)->only(['index', 'store', 'destroy']);
});

// ============================
// مسارات API للذكاء الاصطناعي (AJAX)
// ============================
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::post('/ai/extract-book', [AiController::class, 'extractBookDetails'])->name('ai.extract-book');
    Route::post('/ai/predict-price', [AiController::class, 'predictPrice'])->name('ai.predict-price');
});
require __DIR__.'/auth.php';