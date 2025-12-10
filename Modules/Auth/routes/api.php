<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
// use Modules\Auth\Http\Controllers\CompteController; // TODO: Créer ce contrôleur

Route::prefix('auth')->group(function () {
    Route::post('/inscrire', [AuthController::class, 'register']);
    Route::post('/connexion', [AuthController::class, 'login']);
    Route::get('/fresh', [AuthController::class, 'refresh'])->middleware('auth.api');

    // TODO: Implémenter ces routes quand CompteController sera créé
    // Route::post('/reset-password', [CompteController::class, 'resetPassword']);
});


Route::middleware(['auth.api'])->group(function () {
    // TODO: Implémenter ces routes quand CompteController sera créé
    // Route::put('/user/change-password', [CompteController::class, 'changePassword']);
    Route::post('/user/logout', [AuthController::class, 'logout']);
});
