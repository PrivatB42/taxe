<?php

namespace Modules\Auth\Http\Controllers;

use App\Helpers\Constantes;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Auth\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // public function register(Request $request)
    // {
    //     $typeCompte = $request->type_compte;
    //     $rules = [
    //         'nom_complet' => 'required|string|max:255',
    //         'telephone' => 'required|numeric|digits:10|unique:entite_users,telephone',
    //         'email' => 'nullable|email|unique:entite_users,email',
    //         'password' => 'required|string|min:8',
    //         'type_compte' => 'required|in:' . Constantes::COMPTE_ETUDIANT . ',' . Constantes::COMPTE_GESTIONNAIRE 
    //     ];


    //     $validate = Validator::make($request->all(), $rules, Constantes::VALIDATION_MESSAGES);

    //     if ($validate->fails()) {
    //         return response([
    //             'success' => false,
    //             'message' => $validate->errors()->all(),
    //         ], 422);
    //     }

    //     try {
    //         DB::beginTransaction();
    //         $this->authService->register($validate->validated(), $typeCompte);
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return errorExeption($e);
    //     }


    //     return response([
    //         'success' => true,
    //         'message' => 'Inscription réussie',
    //     ], 200);
    // }


    public function pageLogin()
    {
        return view('auth::pages.login.index');
    }


    public function login(Request $request)
    {
        $data = $request->validate([
            'identifiant' => 'required|string',
            'password' => 'required|string|min:8',
        ], Constantes::VALIDATION_MESSAGES);

        $result = $this->authService->loginByWeb($data);

        // Si la requête attend du JSON ou si c'est une requête AJAX
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($result, $result['code'] ?? 200);
        }

        // Sinon, comportement par défaut (redirection ou erreur)
        if ($result['success']) {
            return redirect($result['redirect'] ?? '/dashboard');
        }

        return back()->withErrors([
            'form' => $result['message'] ?? 'Erreur de connexion',
        ])->onlyInput('identifiant');
    }

    public function logout()
    {
       return $this->authService->logoutWeb();
    }

}
