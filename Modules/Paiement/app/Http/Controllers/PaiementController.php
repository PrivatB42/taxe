<?php

namespace Modules\Paiement\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Paiement\Services\PaiementService;

class PaiementController extends BaseController
{
    public function __construct(PaiementService $service)
    {
        return parent::__construct(
            $service,
            'Paiement',
            'paiement::pages.paiement.index'
        );
    }

    public function index()
    {
        $totalPaiementToday = $this->service->sum(['date_paiement' => now()]);
        $totalPaiementTodayActive = $this->service->sum(['date_activement' => now()]);

        return view($this->viewPath, compact('totalPaiementToday', 'totalPaiementTodayActive'));
    }

    public function storeResponseWithData($request, $paiement, $validated){

       return [
            'paiement' => $paiement
        ];

    }

    public function recu(string $reference){
        $paiement = $this->service->getWhere('reference', $reference, null, 'first');

        if (!$paiement) {
            return $this->notFoundResponse();
        }

        return view('paiement::components.paiement.paiement-recu', compact('paiement'));
    }

    public function activerPaiement(int $paiement_id){
        $paiement = $this->service->find($paiement_id);
        if (!$paiement) {
            return $this->notFoundResponse();
        }
        $this->service->activerPaiement($paiement);
        return $this->successResponse('Paiement activer avec succes');
    }

    public function sum(Request $request){
        
        return $this->service->sum($request->all());
    }
}
