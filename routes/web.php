<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RegistrationDetailController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// Customer-facing routes
Route::get('/', fn () => redirect('/register'));
Route::get('/register', [RegistrationController::class, 'show'])->name('register.show');
Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');
Route::get('/register/duplicate', [RegistrationController::class, 'duplicate'])->name('register.duplicate');
Route::get('/register/success', [RegistrationController::class, 'success'])->name('register.success');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });

    // Authenticated admin routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/export', [DashboardController::class, 'export'])->name('export');
        Route::get('/registrations/{id}', [RegistrationDetailController::class, 'show'])->name('registrations.show');
    });
});
