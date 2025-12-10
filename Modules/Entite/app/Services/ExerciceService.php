<?php

namespace Modules\Entite\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\Entite\Models\Taxe;

class ExerciceService extends BaseService
{

    protected array $datatableConfig = [
        'searchable_columns' => ['slug', 'date_debut', 'date_fin'],
        'sortable_columns'   => ['slug', 'date_debut', 'date_fin'],
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
            'date_debut' => 'required|date_format:Y-m-d|before:date_fin',
            'date_fin' => 'required|date_format:Y-m-d|after:date_debut',
        ];
    }

    public function exercice_actuel()
    {
        return $this->model->where('is_active', true)->first();
    }

}
