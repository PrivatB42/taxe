<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\Entite\Services\ActiviteService;
use Modules\Entite\Services\ActiviteTaxeService;
use Modules\User\Services\ContribuableService;
use Modules\User\Services\ContribuableTaxeService;

class ContribuableController extends BaseController
{

    protected ActiviteService $activiteService;
    protected ActiviteTaxeService $activiteTaxeService;
   // protected ContribuableTaxeService $contribuableTaxeService;


    public function __construct(
        ContribuableService $service,
        ActiviteService $activiteService,
        ActiviteTaxeService $activiteTaxeService,
        //ContribuableTaxeService $contribuableTaxeService
    ) {
        parent::__construct(
            $service,
            'Contribuable',
            'user::pages.contribuable.index'
        );

        $this->activiteService = $activiteService;
        $this->activiteTaxeService = $activiteTaxeService;
       // $this->contribuableTaxeService = $contribuableTaxeService;
    }

    public function show($matricule, $action, ?int $contribuable_activite_id = null)
    {

        $contribuable = $this->service->getWhere('matricule', $matricule, null, 'first');

        $contribuable->load('personne');


        $activites = $this->activiteService->getWhere('is_active', true);

        $component = null;

        $taxes = null;

        $contribuableActivite = null;

        $contribuableTaxes = null;

        if (is_int($contribuable_activite_id)) {
            $contribuableActivite = $contribuable->contribuableActivites()->where('id', $contribuable_activite_id)->first();
        }

        switch ($action) {
            case 'activites':
                $component = 'contribuable-activites';
                break;
            case 'constantes':
                $component = 'contribuable-constantes';
                break;
            case 'taxes':
                // $contribuable->load('activites'); 

                if ($contribuableActivite) {
                    $activites_ids = [$contribuableActivite->activite_id];
                } else {
                    $activites_ids = $contribuable->activites->pluck('id')->toArray();
                }

                $taxesActivites = $this->activiteTaxeService->getModel()
                    ->with('taxe')
                    ->where('is_active', true)
                    ->whereIn('activite_id', $activites_ids)
                    ->get();

                if ($taxesActivites->isEmpty()) {
                    return back()->with('error', 'Aucune taxe pour le contribuable');
                }

                $taxes = $taxesActivites->pluck('taxe')->unique();

                if ($contribuableActivite) {
                     $contribuableTaxes  = $contribuable->contribuableTaxe()
                     ->whereIn('activite_id', $activites_ids)
                     ->get();
                    $component = 'contribuable-taxes-activite';
                } else {
                    $component = 'contribuable-taxes';
                }

                break;
        }

        if (!$component) {
            return back()->with('error', 'Action non trouvée pour le contribuable');
        }

        if ($action == "constantes" && !$contribuable_activite_id) {
            return back()->with('error', 'Action non trouvée pour le contribuable et l\'activité');
        }


        return view('user::pages.contribuable.show', compact(
            'contribuable',
            'activites',
            'component',
            'action',
            'taxes',
            'contribuableActivite',
            'contribuableTaxes'
        ));
    }
}
