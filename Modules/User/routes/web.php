<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\ActiviteLogController;
use Modules\User\Http\Controllers\ContribuableActiviteController;
use Modules\User\Http\Controllers\ContribuableController;
use Modules\User\Http\Controllers\ContribuableParametreController;
use Modules\User\Http\Controllers\ContribuableTaxeController;
use Modules\User\Http\Controllers\GestionnaireController;

/*
|--------------------------------------------------------------------------
| Routes protégées par authentification
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.web'])->group(function () {

    // ====================================
    // CONTRIBUABLES - Gestionnaire + Admin
    // ====================================
    Route::middleware(['role:gestionnaire,admin'])->group(function () {
        // Routes de base pour les contribuables
        makeRoutesfx(
            'contribuables',
            ContribuableController::class,
            'contribuables'
        );

        // Route show séparée avec ses propres paramètres
        Route::get('contribuables/{matricule}/{action}/{contribuable_activite_id?}', [ContribuableController::class, 'show'])
            ->name('contribuables.show');

        $configContribuableActivite = [
            'except' => ['index'],
        ];

        makeRoutesfx(
            'contribuables-activites',
            ContribuableActiviteController::class,
            'contribuables-activites',
            $configContribuableActivite
        );

        $configContribuableParametre = [
            'except' => ['index'],
        ];

        makeRoutesfx(
            'contribuables-parametres',
            ContribuableParametreController::class,
            'contribuables-parametres',
            $configContribuableParametre
        );

        makeRoutesfx(
            'contribuables-taxes',
            ContribuableTaxeController::class
        );
    });

    // ====================================
    // SUPERVISION - Superviseur + Admin
    // Gestion des gestionnaires et suivi des activités
    // ====================================
    Route::middleware(['role:superviseur,admin'])->prefix('supervision')->group(function () {
        
        // Gestionnaires
        makeRoutesfx('gestionnaires', GestionnaireController::class);

        // Journal des activités des gestionnaires
        Route::prefix('activites-log')->name('activites-log.')->group(function () {
            Route::get('/', [ActiviteLogController::class, 'index'])->name('index');
            Route::get('/data', [ActiviteLogController::class, 'data'])->name('data');
            Route::get('/stats', [ActiviteLogController::class, 'stats'])->name('stats');
            Route::get('/{id}', [ActiviteLogController::class, 'show'])->name('show');
        });
    });

    // ====================================
    // UTILISATEURS - Admin uniquement (pour gestion directe)
    // ====================================
    Route::middleware(['role:admin'])->prefix('utilisateurs')->group(function () {
        makeRoutesfx('gestionnaires', GestionnaireController::class, 'admin.gestionnaires');
    });

});
