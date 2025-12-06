<?php

namespace Modules\Entite\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\Entite\Models\Activite;

class ActiviteService extends BaseService
{

    protected array $datatableConfig = [
        'searchable_columns' => ['nom'],
        'sortable_columns'   => ['nom'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => [],
    ];

    protected array $makeSlug = ['nom'];

    public function __construct(Activite $model)
    {
        parent::__construct($model);
    }

    public function rules($id = null): array
    {
        return [
            'nom' => 'required|string'
        ];
    }

}
