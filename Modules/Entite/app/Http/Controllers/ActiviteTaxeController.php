<?php

namespace Modules\Entite\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\Entite\Services\ActiviteService;
use Modules\Entite\Services\ActiviteTaxeService;
use Modules\Entite\Services\TaxeService;

class ActiviteTaxeController extends BaseController
{
    protected ActiviteService $activiteService;
    protected TaxeService $taxeService;

    public function __construct(
        ActiviteTaxeService $service,
        ActiviteService $activiteService,
        TaxeService $taxeService
        )
    {
        parent::__construct(
            $service,
            'Liaison entre taxe et activite',
            'entite::pages.activite-taxe.index'
        );

        $this->activiteService = $activiteService;
        $this->taxeService = $taxeService;
    }

    public function index()
    {
        $taxes = $this->taxeService->getWhere('is_active', true);
        $activites = $this->activiteService->getWhere('is_active', true);

        return view($this->viewPath, compact('taxes', 'activites'));

    }
}
