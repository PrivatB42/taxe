<?php

namespace Modules\Paiement\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\Paiement\Services\CaisseService;

class CaisseController extends BaseController
{

    public function __construct(CaisseService $service)
    {
        parent::__construct(
            $service,
            'Caisse',
            'paiement::pages.caisse.index'
        );
    }
}
