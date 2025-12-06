<?php

namespace Modules\User\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\User\Models\Gestionnaire;

class GestionnaireService extends BaseService
{
    protected PersonneService $personneService;

    protected array $datatableConfig = [
        'searchable_columns' => ['personne.nom_complet', 'personne.telephone', 'personne.email'],
        'sortable_columns'   => ['personne.nom_complet', 'personne.telephone', 'personne.email'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => ['personne'],
    ];

    public function __construct(Gestionnaire $model, PersonneService $personneService)
    {
        parent::__construct($model);
        $this->personneService = $personneService;
    }

    public function rules($id = null): array
    {
        $personne = $id ? $this->personneService->find($id) : null;
        return array_merge($this->personneService->rules($personne?->id), [
            'email' => 'required|email|unique:user_personnes,email,' . $personne?->id
        ]);
    }

    public function beforeStore(array $data)
    {
        $personne = $this->personneService->store(
            [
                'nom_complet' => $data['nom_complet'],
                'telephone' => $data['telephone'],
                'email' => $data['email']
            ]
        );

        return [
            'personne_id' => $personne->id,
            'commune_id' => Constantes::COMMUNE_ID
        ];
    }

    public function beforeUpdate($contribuable, array $data)
    {
        $this->personneService->update($contribuable->personne, [
            'nom_complet' => $data['nom_complet'],
            'telephone' => $data['telephone'],
            'email' => $data['email']
        ]);
    }
}
