<?php

namespace Modules\Entite\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\Entite\Models\Taxe;

class TaxeService extends BaseService
{

    protected array $datatableConfig = [
        'searchable_columns' => ['nom', 'code'],
        'sortable_columns'   => ['nom', 'code'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => [],
    ];

    public function __construct(Taxe $model)
    {
        parent::__construct($model);
    }

    public function rules($id = null): array
    {
        return [
            'nom' => 'required|string',
            'code' => 'required|string',
            'formule' => 'nullable|string',
            'multiplicateur' => 'nullable|numeric|in:'.implode(',', [1, 2, 3, 4, 6, 12] ),
        ];
    }

}
