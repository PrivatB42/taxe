<?php

namespace Modules\Paiement\Http\Controllers;

use App\Helpers\Constantes;
use App\Http\Controllers\BaseController;
use Modules\Paiement\Models\CaisseGestionnaire;
use Modules\Paiement\Services\CaisseService;
use Modules\User\Services\GestionnaireService;

class CaisseController extends BaseController
{
    protected GestionnaireService $gestionnaireService;

    public function __construct(CaisseService $service, GestionnaireService $gestionnaireService)
    {
        parent::__construct(
            $service,
            'Caisse',
            'paiement::pages.caisse.index'
        );

        $this->gestionnaireService = $gestionnaireService;
    }

    public function associateGestionnaire($caisseId, $gestionnaireId)
    {
        try {
            $caisse = $this->service->find($caisseId);
            if (!$caisse) {
                return $this->notFoundResponse();
            }

            // Vérifier que le gestionnaire existe et est bien un caissier
            $gestionnaire = $this->gestionnaireService->getModel()
                ->where('id', $gestionnaireId)
                ->where('role', Constantes::ROLE_CAISSIER)
                ->first();

            if (!$gestionnaire) {
                return $this->errorResponse('Gestionnaire introuvable ou non autorisé (caissier requis)');
            }

            // Désactiver l'association courante uniquement si elle existe
            $caisse->caisseGestionnaires()
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'date_fin' => now()->format('Y-m-d')
                ]);

            // Associer le nouveau caissier
            $this->service->associateGestionnaire($caisse, $gestionnaireId);

            return $this->successResponse('Caissier associé avec succès à la caisse');
        } catch (\Throwable $e) {
            return $this->errorResponse('Erreur lors de l\'association du caissier : ' . $e->getMessage(), 400);
        }
    }

    public function finAssociationGestionnaire(int $caisseGestionnaireId)
    {
        $caisseGestionnaire = CaisseGestionnaire::find($caisseGestionnaireId);
        if (!$caisseGestionnaire) {
            return $this->errorResponse('Caisse gestionnaire introuvable');
        }

        $this->service->finAssociationGestionnaire($caisseGestionnaire);

        return $this->successResponse('Caisse gestionnaire fini avec succes');
    }

    public function ouvrirOrFermerCaisse(string $action){

        $user = session('user');

        $gestionnaire = $this->gestionnaireService->getModel()
        ->where('id', $user['gestionnaire_id'] ?? null)
        ->where('role', Constantes::ROLE_CAISSIER)
        ->first();

        if (!$gestionnaire) {
           return $this->errorResponse('Vous n\'etes pas un(e) caissier(e)');
        }

        $caisse = $gestionnaire?->caisses()?->first();

        if (!$caisse) {
           return $this->errorResponse('Vous n\'avez pas accès a une caisse');
        }

        if (!in_array($action, [Constantes::STATUT_OUVERT, Constantes::STATUT_FERMER])) {
           return $this->errorResponse('Action non reconnue');
        }

        $caisse->statut = $action;
        $caisse->save();

        session()->put('user.caisse', $caisse);
        session()->save();
        session()->regenerate();

        return $this->successResponse('Caisse '. $action == Constantes::STATUT_OUVERT ? 'ouverte' : 'fermée');

    }
}
