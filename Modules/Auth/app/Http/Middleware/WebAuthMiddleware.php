<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class WebAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Vérifier si le token est présent dans la requête (header ou query)
            $token = $request->bearerToken() 
                ?? $request->header('Authorization') 
                ?? $request->query('token');

            // Si pas de token dans la requête, vérifier la session
            if (!$token && session()->has('user')) {
                // L'utilisateur est authentifié via session, continuer
                return $next($request);
            }

            // Si token présent, le valider
            if ($token) {
                // Nettoyer le token si c'est "Bearer token"
                $token = str_replace('Bearer ', '', $token);
                
                if (JWTAuth::setToken($token)->check()) {
                    $user = JWTAuth::authenticate($token);
                    if ($user) {
                        // Mettre à jour la session avec les données du token
                        $claims = JWTAuth::getPayload($token)->toArray();
                        if (isset($claims['user'])) {
                            session()->put('user', $claims['user']);
                        }
                        return $next($request);
                    }
                }
            }

            // Si pas de token valide et pas de session, rediriger vers login
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non authentifié',
                    'error_code' => 'UNAUTHENTICATED'
                ], 401);
            }

            return redirect()->route('auth.login')->with('error', 'Veuillez vous connecter');
        } catch (Exception $e) {
            // En cas d'erreur, vérifier la session
            if (session()->has('user')) {
                return $next($request);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expirée',
                    'error_code' => 'UNAUTHENTICATED'
                ], 401);
            }

            return redirect()->route('auth.login')->with('error', 'Votre session a expiré');
        }
    }
}

