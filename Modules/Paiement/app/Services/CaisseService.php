<?php

namespace Modules\Paiement\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\Paiement\Models\Caisse;
use Modules\Paiement\Models\CaisseGestionnaire;

class CaisseService extends BaseService
{

    protected array $datatableConfig = [
        'searchable_columns' => ['nom'],
        'sortable_columns'   => ['nom'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => ['gestionnaire'],
    ];

    protected array $makeSlug = ['nom'];

    public function __construct(Caisse $model)
    {
        parent::__construct($model);
    }

    public function rules($id = null): array
    {
        return [];
    }

    public function beforeStore(array $data)
    {
        $count = $this->model::count();
        $nom = 'Caisse ' . ($count + 1);

        return ['nom' => $nom];
    }

    public function associateGestionnaire(Caisse $caisse, $gestionnaireId)
    {
        $caisse->caisseGestionnaires()->create([
            'gestionnaire_id' => $gestionnaireId,
            'date_debut' => now()->format('Y-m-d'),
            'is_active' => true,
            'date_fin' => null,
        ]);
    }

    public function finAssociationGestionnaire(CaisseGestionnaire $caisseGestionnaire)
    {
        $caisseGestionnaire->update([
            'date_fin' => now()->format('Y-m-d'),
            'is_active' => false
        ]);
    }
}
