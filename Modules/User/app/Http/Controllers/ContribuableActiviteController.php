<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\User\Services\ContribuableActiviteService;

class ContribuableActiviteController extends BaseController
{

    public function __construct(ContribuableActiviteService $service)
    {
        parent::__construct(
            $service,
            'Activite du contribuable',
            ''
        );
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request, function ($query) use ($request) {
           
            if ($request->get('contribuable_id')) {
                $query->where('contribuable_id', $request->get('contribuable_id'));
            }
        });
    }

}
