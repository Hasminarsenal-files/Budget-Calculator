<?php

use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
});

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Budgets
    Route::resource('budgets', BudgetController::class)->only(['index', 'show', 'store', 'destroy']);

    // Financial Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    // Transactions / Expenses
    Route::resource('transactions', TransactionController::class)->only(['index', 'store', 'destroy']);

    // Incomes
    Route::resource('incomes', IncomeController::class)->only(['index', 'store', 'destroy']);

    // Savings Goals
    Route::get('/savings-goals', [SavingsGoalController::class, 'index'])->name('savings-goals.index');
    Route::post('/savings-goals', [SavingsGoalController::class, 'store'])->name('savings-goals.store');
    Route::post('/savings-goals/contribute', [SavingsGoalController::class, 'contribute'])->name('savings-goals.contribute');
    Route::delete('/savings-goals/{savingsGoal}', [SavingsGoalController::class, 'destroy'])->name('savings-goals.destroy');

    // Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Categories
    Route::resource('categories', CategoryController::class)->only(['index', 'store']);

    // Profile & User Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // PWA Offline Sync & Bootstrap Endpoints
    Route::get('/api/v1/bootstrap', [SyncController::class, 'bootstrap'])->name('api.bootstrap');
    Route::post('/api/v1/sync', [SyncController::class, 'sync'])->name('api.sync');
});
