<?php

namespace Modules\Entite\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\Entite\Services\ActiviteService;

class ActiviteController extends BaseController
{

    public function __construct(ActiviteService $service)
    {
        parent::__construct(
            $service,
            'Activite',
            'entite::pages.activite.index'
        );
    }
}
