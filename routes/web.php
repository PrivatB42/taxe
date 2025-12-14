<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Dashboard\Http\Controllers\DashboardController;

// Route de login directe
Route::get('/login', [AuthController::class, 'pageLogin'])->name('login');
Route::get('/auth/login', [AuthController::class, 'pageLogin'])->name('auth.login');

// Route du dashboard
Route::middleware('can.permission:' . \App\Helpers\Constantes::PERMISSION_TABLEAU_BORD)->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [DashboardController::class, 'index'])->name('home');
});

