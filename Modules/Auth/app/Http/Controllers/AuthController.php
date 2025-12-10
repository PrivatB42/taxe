<?php

namespace Modules\Auth\Http\Controllers;

use App\Helpers\Constantes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Auth\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Afficher la page de connexion
     */
    public function showLoginForm()
    {
        // Si déjà connecté, rediriger vers le dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth::login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifiant' => 'required|string',
            'password' => 'required|string|min:4',
        ], Constantes::VALIDATION_MESSAGES);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('identifiant'));
        }

        return $this->authService->loginByWeb($validator->validated());
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
