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
use App\Http\Controllers\Student\ProfileController;
use Illuminate\Support\Facades\Session;

Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('locale.switch');

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

// تقييمات المستخدم
Route::get('/users/{user}/ratings', [BrowseController::class, 'userRatings'])->name('users.ratings');

// ============================
// مسارات الطالب (يجب أن يكون مسجلاً للدخول)
// ============================
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {

    // الصفحة الرئيسية للطالب
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // مسارات الملف الشخصي
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

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
    Route::post('/ai/moderate-pdf', [AiController::class, 'moderatePdf'])->name('ai.moderate-pdf');
});
require __DIR__.'/auth.php';