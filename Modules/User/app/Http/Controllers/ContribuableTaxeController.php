<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\User\Services\ContribuableTaxeService;

class ContribuableTaxeController extends BaseController
{

    public function __construct(ContribuableTaxeService $service)
    {
        parent::__construct(
            $service,
            'Taxe du contribuable',
            ''
        );
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request, function ($query) use ($request) {

            if ($request->get('contribuable_id')) {
                $query->where('contribuable_id', $request->get('contribuable_id'));
            }

            if ($request->get('activite_id')) {
                $query->where('activite_id', $request->get('activite_id'));
            }
        });
    }
}
