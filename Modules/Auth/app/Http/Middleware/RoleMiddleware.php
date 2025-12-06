<?php

namespace Modules\Auth\Http\Middleware;

use App\Helpers\Constantes;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Les rôles autorisés
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non authentifié',
                    'error_code' => 'UNAUTHENTICATED'
                ], 401);
            }
            return redirect()->route('login');
        }

        $userRole = $user->type_compte;

        // Vérifier si l'utilisateur a l'un des rôles requis
        if (!in_array($userRole, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires.',
                    'error_code' => 'FORBIDDEN'
                ], 403);
            }
            abort(403, 'Accès non autorisé');
        }

        return $next($request);
    }

    /**
     * Vérifie si l'utilisateur peut accéder à une route spécifique
     */
    public static function canAccess(string $routeName): bool
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        
        if (!$user) {
            return false;
        }

        $userRole = $user->type_compte;
        $permissions = Constantes::PERMISSIONS[$userRole] ?? [];

        foreach ($permissions as $permission) {
            // Permission avec wildcard (ex: contribuables.*)
            if (str_ends_with($permission, '.*')) {
                $prefix = str_replace('.*', '', $permission);
                if (str_starts_with($routeName, $prefix)) {
                    return true;
                }
            }
            // Permission exacte
            if ($permission === $routeName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur est un superviseur
     */
    public static function isSuperviseur(): bool
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        return $user && $user->type_compte === Constantes::COMPTE_SUPERVISEUR;
    }

    /**
     * Vérifie si l'utilisateur est un gestionnaire
     */
    public static function isGestionnaire(): bool
    {
        $user = Auth::guard('api')->user() ?? Auth::user();
        return $user && $user->type_compte === Constantes::COMPTE_GESTIONNAIRE;
    }
}


