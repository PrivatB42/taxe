<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\User\Services\ContribuableActiviteService;
use Modules\User\Services\ContribuableParametreService;

class ContribuableParametreController extends BaseController
{

    public function __construct(ContribuableParametreService $service)
    {
        parent::__construct(
            $service,
            'Parametre du contribuable',
            ''
        );
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request, function ($query) use ($request) {
           
            // if ($request->get('contribuable_id')) {
            //     $query->where('contribuable_id', $request->get('contribuable_id'));
            // }

            if ($request->get('contribuable_activite_id')) {
                $query->where('contribuable_activite_id', $request->get('contribuable_activite_id'));
            }
        });
    }

}
