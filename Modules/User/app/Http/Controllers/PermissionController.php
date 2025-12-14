<?php

namespace Modules\User\Http\Controllers;

use App\Helpers\Constantes;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\User\Services\PermissionService;

class PermissionController extends BaseController
{
    public function __construct(PermissionService $service)
    {
        parent::__construct(
            $service,
            'Permission',
            'user::pages.permission.index'
        );
    }

    /**
     * Affiche la page de gestion des permissions par rôle
     */
    public function index()
    {
        // Récupérer les rôles depuis la table
        $roles = \Modules\User\Models\Role::where('is_active', true)
            ->orderBy('nom')
            ->get()
            ->map(function($role) {
                return ['id' => $role->code, 'nom' => $role->nom];
            })
            ->toArray();
        
        $permissions = $this->service->getModel()->all();
        
        return view('user::pages.permission.index', compact('roles', 'permissions'));
    }

    /**
     * Récupère les permissions d'un rôle
     */
    public function getRolePermissions(Request $request, string $role)
    {
        $permissions = $this->service->getPermissionsByRole($role);
        return response()->json($permissions);
    }

    /**
     * Met à jour les permissions d'un rôle
     */
    public function updateRolePermissions(Request $request, string $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:user_permissions,id',
        ]);

        $this->service->updateRolePermissions($role, $request->permissions);

        return response()->json([
            'success' => true,
            'message' => 'Permissions mises à jour avec succès',
        ]);
    }

    /**
     * Initialise les permissions depuis les constantes
     */
    public function initialize()
    {
        $this->service->initializePermissions();
        $this->service->initializeRolePermissions();

        return response()->json([
            'success' => true,
            'message' => 'Permissions initialisées avec succès',
        ]);
    }
}

