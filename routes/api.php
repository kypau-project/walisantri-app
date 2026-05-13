<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SavingController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

// Protected routes (requires auth + phone verified)
Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsurePhoneVerified::class])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('/profile/photo', [ProfileController::class, 'photo']);

    // Bills
    Route::get('/bills', [BillController::class, 'index']);
    Route::post('/bills/pay', [BillController::class, 'pay']);
    Route::get('/bills/history', [BillController::class, 'history']);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{id}', [PaymentController::class, 'show']);

    // Savings
    Route::get('/savings', [SavingController::class, 'index']);
    Route::post('/savings/topup', [SavingController::class, 'topup']);
    Route::get('/savings/history', [SavingController::class, 'history']);

    // Exams
    Route::get('/exams', [ExamController::class, 'index']);
    Route::post('/exams/start', [ExamController::class, 'start']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/download', [ReportController::class, 'download']);
});

// Admin routes
Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsureIsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
});
