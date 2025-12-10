<?php

namespace Modules\User\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\Entite\Models\Taxe;
use Modules\Entite\Services\ExerciceService;
use Modules\Entite\Services\TaxeService;
use Modules\User\Models\Contribuable;
use Modules\User\Models\ContribuableTaxe;

class ContribuableTaxeService extends BaseService
{

    protected array $datatableConfig = [
        'searchable_columns' => ['taxe.nom'],
        'sortable_columns'   => ['taxe.nom'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => ['taxe'],
    ];

    protected ExerciceService $exerciceService;
    protected TaxeService $taxeService;

    public function __construct(ContribuableTaxe $model, ExerciceService $exerciceService, TaxeService $taxeService)
    {
        parent::__construct($model);
        $this->exerciceService = $exerciceService;
        $this->taxeService = $taxeService;
    }

    public function rules($id = null): array
    {
        return [
            'taxes' => 'required|array|min:1',
            'taxes.*.taxe_id' => 'required|numeric|exists:entite_taxes,id',
            'taxes.*.montant' => 'required|numeric|min:100',
            'activite_id' => 'required|numeric|exists:entite_activites,id',
            'contribuable_id' => 'required|numeric|exists:user_contribuables,id',
        ];
    }


    public function store(array $data)
    {
        $exercice = $this->exerciceService->exercice_actuel();

        foreach ($data['taxes'] as $taxe) {
            $_taxe = $this->taxeService->find($taxe['taxe_id']);

            if (!$_taxe) {
                throw new \Exception('Taxe introuvable');
            }

            $this->model->create([
                'taxe_id' => $taxe['taxe_id'],
                'montant' => $taxe['montant'],
                'montant_a_payer' => $taxe['montant'] * $_taxe->multiplicateur,
                'exercice_id' => $exercice->id,
                'contribuable_id' => $data['contribuable_id'],
                'activite_id' => $data['activite_id'],
            ]);
        }
    }
}
