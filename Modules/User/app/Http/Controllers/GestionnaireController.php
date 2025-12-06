<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Modules\User\Services\GestionnaireService;

class GestionnaireController extends BaseController
{

    public function __construct(GestionnaireService $service)
    {
        parent::__construct(
            $service,
            'Gestionnaire',
            'user::pages.gestionnaire.index'
        );
    }
}
