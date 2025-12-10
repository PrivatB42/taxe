<?php

use Illuminate\Support\Facades\Route;
use Modules\Paiement\Http\Controllers\CaisseController;
use Modules\Paiement\Http\Controllers\PaiementController;

Route::prefix('caisses')->group(function(){
    makeRoutesfx('/', CaisseController::class, 'caisses');
});
