<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::prefix('auth')->group(function(){

    Route::get('login', [AuthController::class, 'pageLogin'])->name('auth.login');
     Route::post('connexion', [AuthController::class, 'login'])->name('auth.connexion');
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
});
