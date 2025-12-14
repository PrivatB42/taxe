<?php

namespace Modules\Entite\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\Entite\Models\ActiviteTaxe;

class ActiviteTaxeService extends BaseService
{

    protected array $datatableConfig = [
         'searchable_columns' => ['taxe.nom', 'activite.nom'],
        'sortable_columns'   => ['taxe.nom', 'activite.nom'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => ['taxe', 'activite'],
    ];

    public function __construct(ActiviteTaxe $model)
    {
        parent::__construct($model);
    }

    public function rules($id = null): array
    {
        return [
            'taxe_id' => 'required|exists:entite_taxes,id',
             'activite_id' => 'required|exists:entite_activites,id',
        ];
    }

}
