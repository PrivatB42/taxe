<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
// use Modules\Auth\Http\Controllers\CompteController;

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('auths', AuthController::class)->names('auth');
// });

Route::prefix('auth')->group(function () {
    Route::post('/inscrire', [AuthController::class, 'register']);
    Route::post('/connexion', [AuthController::class, 'login']);
    Route::get('/fresh', [AuthController::class, 'refresh'])->middleware('auth.api');

    // Route::post('/reset-password', [CompteController::class, 'resetPassword']);
});


Route::middleware(['auth.api'])->group(function () {

    //Route::prefix('user')->group(function () {
        // Route::put('/user/change-password', [CompteController::class, 'changePassword']);
        Route::post('/user/logout', [AuthController::class, 'logout']);
   // });
});
