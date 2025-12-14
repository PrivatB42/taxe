<?php

namespace Modules\User\Services;

use App\Services\BaseService;
use Modules\User\Models\Role;

class RoleService extends BaseService
{
    protected array $datatableConfig = [
        'searchable_columns' => ['code', 'nom', 'description'],
        'sortable_columns'   => ['code', 'nom', 'is_active'],
        'default_order'      => ['column' => 'nom', 'dir' => 'asc'],
        'filterable_columns' => ['is_active'],
        'relations'          => [],
    ];

    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function rules($id = null): array
    {
        return [
            'code' => 'required|string|max:50|unique:user_roles,code,' . $id,
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function beforeStore(array $data)
    {
        // S'assurer que le code est en minuscules et sans espaces
        $data['code'] = strtolower(str_replace(' ', '_', trim($data['code'])));
        return $data;
    }

    public function beforeUpdate($role, array $data)
    {
        // S'assurer que le code est en minuscules et sans espaces
        if (isset($data['code'])) {
            $data['code'] = strtolower(str_replace(' ', '_', trim($data['code'])));
        }
        return $data;
    }

    public function beforeDelete($role)
    {
        // Vérifier si le rôle est utilisé par des gestionnaires
        if ($role->gestionnaires()->count() > 0) {
            throw new \Exception('Ce rôle ne peut pas être supprimé car il est utilisé par ' . $role->gestionnaires()->count() . ' utilisateur(s).');
        }

        // Ne pas permettre la suppression du rôle admin
        if ($role->code === 'admin') {
            throw new \Exception('Le rôle administrateur ne peut pas être supprimé.');
        }
    }
}

