<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\Entite\Services\ActiviteService;
use Modules\User\Services\ContribuableService;

class ContribuableController extends BaseController
{

    protected ActiviteService $activiteService;

    public function __construct(ContribuableService $service, ActiviteService $activiteService)
    {
        parent::__construct(
            $service,
            'Contribuable',
            'user::pages.contribuable.index'
        );

        $this->activiteService = $activiteService;
    }

    public function show($matricule){

        $contribuable = $this->service->getWhere('matricule', $matricule, null, 'first');

        $contribuable->load('personne');

        $activites = $this->activiteService->getWhere('is_active', true);

        return view('user::pages.contribuable.show', compact('contribuable', 'activites'));
    }
}
