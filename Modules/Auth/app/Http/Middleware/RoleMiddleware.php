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
     * Vérifie que l'utilisateur a le rôle requis
     * 
     * Usage: middleware('role:admin') ou middleware('role:admin,superviseur')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->type_compte;

        // L'admin a tous les droits
        if ($userRole === Constantes::COMPTE_ADMIN) {
            return $next($request);
        }

        // Vérifier si le rôle de l'utilisateur est dans la liste des rôles autorisés
        if (!empty($roles) && !in_array($userRole, $roles)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas les droits nécessaires pour accéder à cette ressource.'
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
        }

        return $next($request);
    }

    /**
     * Vérifier si l'utilisateur actuel est admin
     */
    public static function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->type_compte === Constantes::COMPTE_ADMIN;
    }

    /**
     * Vérifier si l'utilisateur actuel est superviseur
     */
    public static function isSuperviseur(): bool
    {
        return Auth::check() && Auth::user()->type_compte === Constantes::COMPTE_SUPERVISEUR;
    }

    /**
     * Vérifier si l'utilisateur actuel est gestionnaire
     */
    public static function isGestionnaire(): bool
    {
        return Auth::check() && Auth::user()->type_compte === Constantes::COMPTE_GESTIONNAIRE;
    }

    /**
     * Obtenir le type de compte de l'utilisateur actuel
     */
    public static function getUserRole(): ?string
    {
        return Auth::check() ? Auth::user()->type_compte : null;
    }

    /**
     * Vérifier si l'utilisateur a une permission spécifique
     */
    public static function hasPermission(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Constantes::hasPermission(Auth::user()->type_compte, $permission);
    }
}
