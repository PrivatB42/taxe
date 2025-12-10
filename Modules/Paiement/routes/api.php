<?php

use Illuminate\Support\Facades\Route;
use Modules\Paiement\Http\Controllers\PaiementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('paiements', PaiementController::class)->names('paiement');
});
