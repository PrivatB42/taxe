<?php

namespace Modules\User\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\User\Models\Gestionnaire;

class GestionnaireService extends BaseService
{
    protected PersonneService $personneService;

    protected array $datatableConfig = [
        'searchable_columns' => ['personne.nom_complet', 'personne.telephone', 'personne.email', 'role'],
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
        // $id correspond à l'id du gestionnaire, pas de la personne
        $gestionnaire = $id ? $this->model->find($id) : null;
        $personneId = $gestionnaire?->personne_id;

        // Règles côté personne (email/téléphone uniques en excluant la personne actuelle)
        $personRules = $this->personneService->rules($personneId);
        // Email requis pour un gestionnaire
        $personRules['email'] = 'required|email|unique:user_personnes,email,' . $personneId;
        // Nom complet requis
        $personRules['nom_complet'] = 'required|string';
        // Téléphone requis
        $personRules['telephone'] = 'required|numeric|digits:10|unique:user_personnes,telephone,' . $personneId;

        return array_merge($personRules, [
            // Rôle doit exister dans la table user_roles (validation dynamique)
            'role' => 'required|exists:user_roles,code',
        ]);
    }

    public function beforeStore(array $data)
    {
        $personne = $this->personneService->store(
            [
                'nom_complet' => $data['nom_complet'],
                'telephone' => $data['telephone'],
                'email' => $data['email'],
            ]
        );

        return [
            'personne_id' => $personne->id,
            'commune_id' => Constantes::COMMUNE_ID,
            'role' => $data['role']
        ];
    }

    public function beforeUpdate($contribuable, array $data)
    {
        $this->personneService->update($contribuable->personne, [
            'nom_complet' => $data['nom_complet'],
            'telephone' => $data['telephone'],
            'email' => $data['email']
        ]);

        // Retourner les données (éventuellement épurées) pour l'update du gestionnaire
        return [
            'role' => $data['role']
        ];
    }
}
