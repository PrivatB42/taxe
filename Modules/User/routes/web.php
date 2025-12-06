<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\ActiviteLogController;
use Modules\User\Http\Controllers\ContribuableActiviteController;
use Modules\User\Http\Controllers\ContribuableController;
use Modules\User\Http\Controllers\ContribuableParametreController;
use Modules\User\Http\Controllers\GestionnaireController;

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::resource('users', UserController::class)->names('user');
// });

/**
 * Routes Contribuables - Accessibles aux Gestionnaires et Superviseurs
 */
$configContribuable = [
    'additional' => [
        'show' => ['method' => 'GET', 'action' => 'show', 'path' => '/{matricule}'],
    ]
];

makeRoutesfx(
    'contribuables',
    ContribuableController::class,
    'contribuables',
    $configContribuable
);

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

/**
 * Routes Supervision - Accessibles uniquement aux Superviseurs
 */
Route::prefix('utilisateurs')->group(function () {
    // Gestion des gestionnaires
    makeRoutesfx('gestionnaires', GestionnaireController::class);
});

Route::prefix('supervision')->group(function () {
    // Suivi des activités des gestionnaires
    Route::get('/activites-log', [ActiviteLogController::class, 'index'])->name('activites-log.index');
    Route::post('/activites-log/data', [ActiviteLogController::class, 'getData'])->name('activites-log.data');
    Route::get('/activites-log/stats', [ActiviteLogController::class, 'stats'])->name('activites-log.stats');
    Route::get('/activites-log/gestionnaire/{id}', [ActiviteLogController::class, 'byGestionnaire'])->name('activites-log.by-gestionnaire');
});
