<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {

            if (!JWTAuth::parseToken()->authenticate()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Votre session a expiré',
                        'error_code' => 'UNAUTHENTICATED'
                    ],
                    401
                );
            }
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'vous n\'etes pas authentifié',
                    'error_code' => 'UNAUTHENTICATED'
                ],
                401
            );
        }

        return $next($request);
    }
}
