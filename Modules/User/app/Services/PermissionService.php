<?php

namespace Modules\User\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\User\Models\Permission;
use Modules\User\Models\RolePermission;

class PermissionService extends BaseService
{
    protected array $datatableConfig = [
        'searchable_columns' => ['code', 'nom', 'description'],
        'sortable_columns'   => ['code', 'nom'],
        'default_order'      => ['column' => 'nom', 'dir' => 'asc'],
        'filterable_columns' => [],
        'relations'          => [],
    ];

    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public function rules($id = null): array
    {
        return [
            'code' => 'required|string|unique:user_permissions,code,' . $id,
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Récupère toutes les permissions avec leur statut pour un rôle donné
     */
    public function getPermissionsByRole($role): array
    {
        // Si c'est un code de rôle (string), trouver l'ID
        if (is_string($role)) {
            $roleModel = \Modules\User\Models\Role::where('code', $role)->first();
            if (!$roleModel) {
                return [];
            }
            $roleId = $roleModel->id;
        } else {
            $roleId = $role;
        }

        $allPermissions = Permission::all();
        $rolePermissions = RolePermission::where('role_id', $roleId)->pluck('permission_id')->toArray();

        return $allPermissions->map(function ($permission) use ($rolePermissions) {
            return [
                'id' => $permission->id,
                'code' => $permission->code,
                'nom' => $permission->nom,
                'description' => $permission->description,
                'has_permission' => in_array($permission->id, $rolePermissions),
            ];
        })->toArray();
    }

    /**
     * Met à jour les permissions d'un rôle
     */
    public function updateRolePermissions($role, array $permissionIds): void
    {
        // Si c'est un code de rôle (string), trouver l'ID
        if (is_string($role)) {
            $roleModel = \Modules\User\Models\Role::where('code', $role)->first();
            if (!$roleModel) {
                throw new \Exception('Rôle non trouvé : ' . $role);
            }
            $roleId = $roleModel->id;
        } else {
            $roleId = $role;
        }

        // Supprimer toutes les permissions existantes pour ce rôle
        RolePermission::where('role_id', $roleId)->delete();

        // Ajouter les nouvelles permissions
        foreach ($permissionIds as $permissionId) {
            RolePermission::create([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    /**
     * Initialise les permissions depuis les constantes
     */
    public function initializePermissions(): void
    {
        foreach (Constantes::PERMISSIONS as $code => $nom) {
            Permission::firstOrCreate(
                ['code' => $code],
                [
                    'nom' => $nom,
                    'description' => $nom,
                ]
            );
        }
    }

    /**
     * Initialise les permissions par défaut pour chaque rôle
     */
    public function initializeRolePermissions(): void
    {
        foreach (Constantes::ROLE_PERMISSIONS as $roleCode => $permissionCodes) {
            // Trouver le rôle par son code
            $role = \Modules\User\Models\Role::where('code', $roleCode)->first();
            if (!$role) {
                continue; // Ignorer si le rôle n'existe pas
            }

            // Si c'est l'admin, on lui donne toutes les permissions
            if ($roleCode === Constantes::ROLE_ADMIN) {
                $permissionIds = Permission::pluck('id')->toArray();
            } else {
                $permissionIds = Permission::whereIn('code', $permissionCodes)->pluck('id')->toArray();
            }

            // Supprimer les permissions existantes
            RolePermission::where('role_id', $role->id)->delete();

            // Ajouter les nouvelles permissions
            foreach ($permissionIds as $permissionId) {
                RolePermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }
}

