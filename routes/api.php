<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SavingController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// ===== PUBLIC ROUTES =====
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

// ===== PROTECTED ROUTES (auth + verified) =====
Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsurePhoneVerified::class])->group(function () {

    // Auth & Account
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/change-password', [DashboardController::class, 'changePassword']);

    // Dashboard (summary untuk halaman utama Flutter)
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile (data santri)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('/profile/photo', [ProfileController::class, 'photo']);
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto']);

    // Bills (Tagihan)
    Route::get('/bills', [BillController::class, 'index']);
    Route::get('/bills/{id}', [BillController::class, 'show']);
    Route::post('/bills/pay', [BillController::class, 'pay']);
    Route::post('/bills/check-status', [BillController::class, 'checkStatus']);
    Route::get('/bills/history/paid', [BillController::class, 'history']);

    // Payments (Riwayat Pembayaran)
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{id}', [PaymentController::class, 'show']);

    // Savings (Tabungan)
    Route::get('/savings', [SavingController::class, 'index']);
    Route::post('/savings/topup', [SavingController::class, 'topup']);
    Route::get('/savings/history', [SavingController::class, 'history']);

    // Exams (Ujian Online)
    Route::get('/exams', [ExamController::class, 'index']);
    Route::post('/exams/start', [ExamController::class, 'start']);
    Route::post('/exams/save-answer', [ExamController::class, 'saveAnswer']);
    Route::post('/exams/submit', [ExamController::class, 'submit']);
    Route::get('/exams/result', [ExamController::class, 'result']);

    // Reports (Raport)
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);
    Route::get('/reports/download/{id}', [ReportController::class, 'download']);
});

// ===== ADMIN ROUTES =====
Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsureIsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
});
