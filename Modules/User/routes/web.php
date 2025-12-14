<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\ContribuableActiviteController;
use Modules\User\Http\Controllers\ContribuableController;
use Modules\User\Http\Controllers\ContribuableParametreController;
use Modules\User\Http\Controllers\ContribuableTaxeController;
use Modules\User\Http\Controllers\GestionnaireController;
use Modules\User\Http\Controllers\PermissionController;

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::resource('users', UserController::class)->names('user');
// });

$configContribuable = [
    'additional' => [
        'show' => ['method' => 'GET', 'action' => 'show', 'path' => '/{matricule}/{action}/{contribuable_activite_id?}'],
    ]
];

Route::middleware('can.permission:' .
    \App\Helpers\Constantes::PERMISSION_GERER_CONTRIBUABLES . ',' .
    \App\Helpers\Constantes::PERMISSION_GERER_CAISSES . ',' .
    \App\Helpers\Constantes::PERMISSION_IMPRIMER_RECU . ',' .
    \App\Helpers\Constantes::PERMISSION_OUVRIR_FERMER_CAISSE . ',' .
    \App\Helpers\Constantes::PERMISSION_IMPRIMER_RECU
)->group(function () use ($configContribuable) {
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

    makeRoutesfx(
        'contribuables-taxes',
        ContribuableTaxeController::class
    );
});

Route::prefix('utilisateurs')
    ->middleware('can.permission:' . \App\Helpers\Constantes::PERMISSION_GERER_UTILISATEURS)
    ->group(function () {
        makeRoutesfx('gestionnaires', GestionnaireController::class);
    });

// Routes pour la gestion des permissions
Route::prefix('permissions')
    ->name('permissions.')
    ->middleware('can.permission:' . \App\Helpers\Constantes::PERMISSION_GERER_PERMISSIONS)
    ->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/role/{role}', [PermissionController::class, 'getRolePermissions'])->name('role');
        Route::post('/role/{role}', [PermissionController::class, 'updateRolePermissions'])->name('role.update');
        Route::post('/initialize', [PermissionController::class, 'initialize'])->name('initialize');
    });

// Routes pour la gestion des rôles
Route::prefix('roles')
    ->middleware('can.permission:' . \App\Helpers\Constantes::PERMISSION_GERER_ROLES)
    ->group(function () {
    // Passer une chaîne vide comme préfixe car on est déjà dans un groupe avec préfixe 'roles'
    // Forcer le nom de base à 'roles' pour obtenir roles.index, roles.store, etc.
    makeRoutesfx('', \Modules\User\Http\Controllers\RoleController::class, 'roles');

    // Autoriser aussi la suppression en POST (fallback JS avec _method)
    Route::post('delete/{id}', [\Modules\User\Http\Controllers\RoleController::class, 'delete'])
        ->name('roles.delete');
    });
