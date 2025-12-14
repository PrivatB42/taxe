<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Paiement\Services\PaiementService;

class DashboardController extends Controller
{
    protected PaiementService $paiementService;

    public function __construct(PaiementService $paiementService)
    {
        $this->paiementService = $paiementService;
    }

    public function index()
    { 
        $recaps = collect(
            [
                [
                    'label' => 'Total Paiements',
                    'value' => $this->paiementService->sum(['date_activement_not_null' => true]),
                    'color' => 'success',
                    'icon' => 'fas fa-money-bills',
                    'size' => '6'
                ],
                [
                    'label' => 'Total Année',
                    'value' => $this->paiementService->sum([
                        'date_activement_not_null' => true,
                        'annee' => date('Y')
                    ]),
                    'color' => 'primary',
                    'icon' => 'fas fa-money-bills',
                    'size' => '6'
                ],
                [
                    'label' => 'Total Mois',
                    'value' => $this->paiementService->sum([
                        'date_activement_not_null' => true,
                        'annee' => date('Y'),
                        'mois' => date('m')
                    ]),
                    'color' => 'info',
                    'icon' => 'fas fa-money-bills',
                    'size' => '4'
                ],
                [
                    'label' => 'Total Semaine',
                    'value' => $this->paiementService->sum([
                        'date_activement_not_null' => true,
                        //'mois' => date('m'),
                        'annee' => date('Y'),
                        'semaine' => date('W')
                    ]),
                    'color' => 'warning',
                    'icon' => 'fas fa-money-bills',
                    'size' => '4'
                ],
                [
                    'label' => 'Total Jour',
                    'value' => $this->paiementService->sum([
                        'date_activement' => now(),
                    ]),
                    'color' => 'danger',
                    'icon' => 'fas fa-money-bills',
                    'size' => '4'
                ],

            ]
        );
        return view('dashboard::pages.index', compact('recaps'));
    }
}
