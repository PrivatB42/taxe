<?php

use Illuminate\Support\Facades\Route;
use Modules\Paiement\Http\Controllers\CaisseController;
use Modules\Paiement\Http\Controllers\PaiementController;

Route::prefix('caisses')
    // Admin gestion caisses OU caissier (ouvrir/fermer) OU encaissement peuvent accéder à la page
    ->middleware('can.permission:' .
        \App\Helpers\Constantes::PERMISSION_CREER_CAISSES . ',' .
        \App\Helpers\Constantes::PERMISSION_CREER_CAISSIERS . ',' .
        \App\Helpers\Constantes::PERMISSION_GERER_CAISSIERS . ',' .
        \App\Helpers\Constantes::PERMISSION_GERER_CAISSES . ',' .
        \App\Helpers\Constantes::PERMISSION_OUVRIR_FERMER_CAISSE . ',' .
        \App\Helpers\Constantes::PERMISSION_ENCASSER . ',' .
        \App\Helpers\Constantes::PERMISSION_IMPRIMER_RECU)
    ->group(function(){
        // Gestion des caisses (création / admin) : permissions de gestion
        makeRoutesfx('/', CaisseController::class, 'caisses');

        // Association caissier : nécessite gérer caissiers
        Route::post('associate-gestionnaire/{caisse_id}/{gestionnaire_id}', [CaisseController::class, 'associateGestionnaire'])
            ->name('caisses.associate-gestionnaire')
            ->middleware('can.permission:' . \App\Helpers\Constantes::PERMISSION_CREER_CAISSES);

        Route::post('fin-association-gestionnaire/{caisse_gestionnaire_id}', [CaisseController::class, 'finAssociationGestionnaire'])
            ->name('caisses.fin-association-gestionnaire')
            ->middleware('can.permission:' . \App\Helpers\Constantes::PERMISSION_CREER_CAISSES);

        // Ouverture/fermeture de caisse : caissier
        Route::post('ouvrir-fermer/{action}', [CaisseController::class, 'ouvrirOrFermerCaisse'])
            ->name('caisses.ouvrir-fermer')
            ->middleware('can.permission:' . \App\Helpers\Constantes::PERMISSION_OUVRIR_FERMER_CAISSE);
    });

Route::prefix('paiements')->group(function(){
    $configPaiement = [
        'except' => ['update', 'toggle-active', 'delete'],
        'additional' => [
            'recu' => ['method' => 'GET', 'action' => 'recu', 'path' => 'recu/{matricule}'],
            'activer' => ['method' => 'POST', 'action' => 'activerPaiement', 'path' => 'activer/{paiement_id}'],
            'sum' => ['method' => 'POST', 'action' => 'sum', 'path' => 'sum'],
        ]
    ];
    // Appliquer le middleware sur le groupe, pas sur makeRoutesfx (qui ne retourne rien)
    Route::middleware('can.permission:' . \App\Helpers\Constantes::PERMISSION_ENCASSER)
        ->group(function () use ($configPaiement) {
            makeRoutesfx('/', PaiementController::class, 'paiements', $configPaiement);
        });
});
