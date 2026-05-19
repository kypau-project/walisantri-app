<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ExamController;
use App\Http\Controllers\Web\MidtransController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ExamAdminController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () { return redirect('/login'); });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Public exam join via token (untuk Flutter deep-link, tanpa login wajib)
Route::get('/exams/join/{token}', [ExamController::class, 'joinByToken'])->name('exams.join');

// Midtrans callback (public, no CSRF)
Route::post('/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');
Route::get('/midtrans/finish', [MidtransController::class, 'finish'])->name('midtrans.finish');

// OTP verification (authenticated but not verified)
Route::middleware('auth')->group(function () {
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('verify-otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend-otp');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Authenticated + verified routes (Wali Santri)
Route::middleware(['auth', \App\Http\Middleware\EnsurePhoneVerified::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/bills', [DashboardController::class, 'bills'])->name('bills');
    Route::post('/bills/pay', [DashboardController::class, 'payBill'])->name('bills.pay');
    Route::post('/midtrans/snap-token', [MidtransController::class, 'createSnapToken'])->name('midtrans.snap-token');
    Route::post('/midtrans/saving-snap-token', [MidtransController::class, 'createSavingSnapToken'])->name('midtrans.saving-snap-token');
    Route::get('/payments', [DashboardController::class, 'payments'])->name('payments');
    Route::get('/savings', [DashboardController::class, 'savings'])->name('savings');
    Route::post('/savings/topup', [DashboardController::class, 'topupSaving'])->name('savings.topup');
    Route::get('/exams', [ExamController::class, 'index'])->name('exams');
    Route::get('/exams/{id}/start', [ExamController::class, 'start'])->name('exams.start');
    Route::post('/exams/{id}/begin', [ExamController::class, 'begin'])->name('exams.begin');
    Route::get('/exams/{id}/take', [ExamController::class, 'begin'])->name('exams.take');
    Route::post('/exams/{id}/save-answer', [ExamController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::post('/exams/{id}/submit', [ExamController::class, 'submit'])->name('exams.submit');
    Route::get('/exams/{id}/result', [ExamController::class, 'result'])->name('exams.result');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    Route::get('/reports/{id}/download', [DashboardController::class, 'downloadReport'])->name('reports.download');
});

// Admin routes
Route::middleware(['auth', \App\Http\Middleware\EnsureIsAdmin::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Students
    Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
    Route::get('/students/create', [AdminController::class, 'createStudent'])->name('admin.students.create');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('admin.students.store');
    Route::get('/students/{id}/edit', [AdminController::class, 'editStudent'])->name('admin.students.edit');
    Route::put('/students/{id}', [AdminController::class, 'updateStudent'])->name('admin.students.update');
    Route::delete('/students/{id}', [AdminController::class, 'deleteStudent'])->name('admin.students.delete');

    // Bills
    Route::get('/bills', [AdminController::class, 'bills'])->name('admin.bills');
    Route::get('/bills/create', [AdminController::class, 'createBill'])->name('admin.bills.create');
    Route::post('/bills', [AdminController::class, 'storeBill'])->name('admin.bills.store');

    // Payments
    Route::get('/payments', [AdminController::class, 'payments'])->name('admin.payments');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/create', [AdminController::class, 'createReport'])->name('admin.reports.create');
    Route::post('/reports', [AdminController::class, 'storeReport'])->name('admin.reports.store');

    // Exams
    Route::get('/exams', [ExamAdminController::class, 'index'])->name('admin.exams');
    Route::get('/exams/create', [ExamAdminController::class, 'create'])->name('admin.exams.create');
    Route::post('/exams', [ExamAdminController::class, 'store'])->name('admin.exams.store');
    Route::get('/exams/{id}', [ExamAdminController::class, 'show'])->name('admin.exams.show');
    Route::get('/exams/{id}/edit', [ExamAdminController::class, 'edit'])->name('admin.exams.edit');
    Route::put('/exams/{id}', [ExamAdminController::class, 'update'])->name('admin.exams.update');
    Route::delete('/exams/{id}', [ExamAdminController::class, 'destroy'])->name('admin.exams.destroy');
    Route::get('/exams/{id}/questions', [ExamAdminController::class, 'questions'])->name('admin.exams.questions');
    Route::post('/exams/{id}/questions', [ExamAdminController::class, 'storeQuestion'])->name('admin.exams.questions.store');
    Route::put('/exams/{examId}/questions/{questionId}', [ExamAdminController::class, 'updateQuestion'])->name('admin.exams.questions.update');
    Route::delete('/exams/{examId}/questions/{questionId}', [ExamAdminController::class, 'deleteQuestion'])->name('admin.exams.questions.delete');
    Route::get('/exams/{id}/results', [ExamAdminController::class, 'results'])->name('admin.exams.results');
    Route::get('/exams/{examId}/grade/{studentId}', [ExamAdminController::class, 'gradeStudent'])->name('admin.exams.grade');
    Route::post('/exams/{examId}/grade/{studentId}', [ExamAdminController::class, 'saveGrade'])->name('admin.exams.grade.save');
});
