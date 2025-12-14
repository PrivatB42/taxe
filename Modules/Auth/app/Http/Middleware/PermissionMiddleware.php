<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;
use Modules\User\Models\RolePermission;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('can.permission:encaisser,ouvrir_fermer_caisse')
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Si aucun besoin explicite, laisser passer
        if (empty($permissions)) {
            return $next($request);
        }

        // Récupérer le rôle depuis la session (web)
        $user = session('user');
        $roleCode = $user['role'] ?? null;

        if (!$roleCode) {
            return $this->deny($request, 'Aucun rôle associé à l’utilisateur.');
        }

        // Admin : accès total
        if ($roleCode === \App\Helpers\Constantes::ROLE_ADMIN) {
            return $next($request);
        }

        // Récupérer le rôle et ses permissions
        $role = Role::where('code', $roleCode)->first();
        if (!$role) {
            return $this->deny($request, 'Rôle introuvable ou inactif.');
        }

        $rolePermissionCodes = RolePermission::where('role_id', $role->id)
            ->with('permission:id,code')
            ->get()
            ->pluck('permission.code')
            ->filter()
            ->unique()
            ->toArray();

        $hasPermission = collect($permissions)->contains(function ($required) use ($rolePermissionCodes) {
            return in_array($required, $rolePermissionCodes, true);
        });

        if (!$hasPermission) {
            return $this->deny($request, 'Accès refusé : permission manquante.');
        }

        return $next($request);
    }

    protected function deny(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        }

        abort(403, $message);
    }
}

