<?php

namespace Modules\User\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\User\Models\Contribuable;

class ContribuableService extends BaseService
{
    protected PersonneService $personneService;

    protected array $datatableConfig = [
        'searchable_columns' => ['matricule', 'personne.nom_complet', 'personne.telephone'],
        'sortable_columns'   => ['matricule', 'personne.nom_complet', 'personne.telephone'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => ['personne'],
    ];

    public function __construct(Contribuable $model, PersonneService $personneService)
    {
        parent::__construct($model);
        $this->personneService = $personneService;
    }

    public function rules($id = null): array
    {
        $personne = $id ? $this->personneService->find($id) : null;
        return array_merge($this->personneService->rules($personne?->id), [
            'adresse_complete' => 'required|string'
        ]);
    }

    private function generateMatricule(): string
    {
        $counter = $this->model::count() + 1;
        return makeMatricule(Constantes::PREFIX_CONTRIBUABLE, $counter, 6);
    }

    public function beforeStore(array $data)
    {
        $personne = $this->personneService->store(
            [
                'nom_complet' => $data['nom_complet'],
                'telephone' => $data['telephone'],
            ]
        );

        return [
            'personne_id' => $personne->id,
            'matricule' => $this->generateMatricule(),
            'adresse_complete' => $data['adresse_complete'],
            'commune_id' => Constantes::COMMUNE_ID
        ];
    }

    public function beforeUpdate($contribuable, array $data)
    {
        $this->personneService->update($contribuable->personne, [
            'nom_complet' => $data['nom_complet'],
            'telephone' => $data['telephone'],
        ]);

        return [
            'adresse_complete' => $data['adresse_complete'],
        ];
    }
}
