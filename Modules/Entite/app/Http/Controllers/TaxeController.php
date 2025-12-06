<?php

namespace Modules\Entite\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\Entite\Services\TaxeService;

class TaxeController extends BaseController
{

    public function __construct(TaxeService $service)
    {
        parent::__construct(
            $service,
            'Taxe',
            'entite::pages.taxe.index'
        );
    }
}
