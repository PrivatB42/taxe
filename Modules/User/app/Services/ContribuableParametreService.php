<?php

namespace Modules\User\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\User\Models\ContribuableParametre;

class ContribuableParametreService extends BaseService
{

    protected array $datatableConfig = [
        'searchable_columns' => ['nom'],
        'sortable_columns'   => ['nom'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => [],
    ];

    public function __construct(ContribuableParametre $model)
    {
        parent::__construct($model);
    }

    public function rules($id = null): array
    {
        return [
            'contribuable_activite_id' => 'required|numeric|exists:user_contribuables_activites,id',
            'nom' => 'required|string',
            'valeur' => 'required|string',
            'type' => 'required|in:'.implode(',', [Constantes::TYPE_BOOL, Constantes::TYPE_DECIMAL, Constantes::TYPE_INT, Constantes::TYPE_STRING]),
        ];
    }

   
}
