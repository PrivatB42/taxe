<?php

namespace Modules\User\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\User\Models\ContribuableActivite;

class ContribuableActiviteService extends BaseService
{

    protected array $datatableConfig = [
        'searchable_columns' => ['activite.nom'],
        'sortable_columns'   => ['activite.nom'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => ['activite'],
    ];

    public function __construct(ContribuableActivite $model)
    {
        parent::__construct($model);
    }

    public function rules($id = null): array
    {
        return [
            'activite_id' => 'required|exists:entite_activites,id',
            'contribuable_id' => 'required|exists:user_contribuables,id',
            'annee_debut' => 'required|date_format:Y',
        ];
    }

   
}
