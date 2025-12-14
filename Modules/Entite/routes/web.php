<?php

use Illuminate\Support\Facades\Route;
use Modules\Entite\Http\Controllers\EntiteController;
use Modules\Entite\Http\Controllers\ActiviteController;
use Modules\Entite\Http\Controllers\ActiviteTaxeController;
use Modules\Entite\Http\Controllers\TaxeConstanteController;
use Modules\Entite\Http\Controllers\TaxeController;

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::resource('entites', EntiteController::class)->names('entite');
// });

Route::prefix('configurations')
    ->middleware('can.permission:' . \App\Helpers\Constantes::PERMISSION_GERER_TAXES)
    ->group(function () {
        makeRoutesfx('activites' , ActiviteController::class);
        makeRoutesfx('taxes' , TaxeController::class);
        makeRoutesfx('activites-taxes' , ActiviteTaxeController::class);
        makeRoutesfx('taxes-constantes' , TaxeConstanteController::class);
    });
