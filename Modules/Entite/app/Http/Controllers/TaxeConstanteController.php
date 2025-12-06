<?php

namespace Modules\Entite\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\Entite\Services\TaxeConstanteService;
use Modules\Entite\Services\TaxeService;

class TaxeConstanteController extends BaseController
{
    protected TaxeService $taxeService;

    public function __construct(
        TaxeConstanteService $service,
        TaxeService $taxeService
        )
    {
        parent::__construct(
            $service,
            'Constante',
            'entite::pages.taxe-constante.index'
        );

        $this->taxeService = $taxeService;
    }

    public function index()
    {
        $taxes = $this->taxeService->getWhere('is_active', true);

        return view($this->viewPath, compact('taxes'));

    }
}
