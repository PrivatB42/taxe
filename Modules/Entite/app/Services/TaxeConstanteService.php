<?php

namespace Modules\Entite\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\Entite\Models\Taxe;
use Modules\Entite\Models\TaxeConstante;

class TaxeConstanteService extends BaseService
{

    protected array $datatableConfig = [
        'searchable_columns' => ['nom', 'taxe.nom'],
        'sortable_columns'   => ['nom', 'taxe.nom'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => ['taxe'],
    ];

    public function __construct(TaxeConstante $model)
    {
        parent::__construct($model);
    }

    public function rules($id = null): array
    {
        return [
            'taxe_id' => 'required|numeric|exists:entite_taxes,id',
            'nom' => 'required|string',
            'valeur' => 'required|string',
            'type' => 'required|in:'.implode(',', [Constantes::TYPE_BOOL, Constantes::TYPE_DECIMAL, Constantes::TYPE_INT, Constantes::TYPE_STRING]),
        ];
    }

}
