<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;

// Halaman Awal / Landing Page (Publik)
Route::get('/', function () {
    return view('welcome_landing');
})->name('home');

// Guest Routes (Hanya untuk pengguna yang BELUM login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes (HARUS LOGIN terlebih dahulu)
Route::middleware('auth')->group(function () {
    // 1. Tampilkan Halaman Form Input
    Route::get('/rekomendasi', [RecommendationController::class, 'index'])->name('recommend.form');

    // 2. Proses Logika KNN (POST)
    Route::post('/rekomendasi', [RecommendationController::class, 'recommend'])->name('recommend.process');

    // 3. Tampilkan Halaman Hasil Output (GET) <-- DITARUH DI SINI
    Route::get('/rekomendasi/hasil', [RecommendationController::class, 'result'])->name('recommend.result');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/message', [ChatController::class, 'send'])->name('chat.send');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});