<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () { return redirect('/login'); });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes (Wali Santri)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::get('/bills', [DashboardController::class, 'bills'])->name('bills');
    Route::post('/bills/pay', [DashboardController::class, 'payBill'])->name('bills.pay');
    Route::get('/payments', [DashboardController::class, 'payments'])->name('payments');
    Route::get('/savings', [DashboardController::class, 'savings'])->name('savings');
    Route::post('/savings/topup', [DashboardController::class, 'topupSaving'])->name('savings.topup');
    Route::get('/exams', [DashboardController::class, 'exams'])->name('exams');
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
    Route::get('/exams', [AdminController::class, 'exams'])->name('admin.exams');
    Route::get('/exams/create', [AdminController::class, 'createExam'])->name('admin.exams.create');
    Route::post('/exams', [AdminController::class, 'storeExam'])->name('admin.exams.store');
});
