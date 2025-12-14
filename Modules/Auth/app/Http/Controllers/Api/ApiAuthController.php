<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Helpers\Constantes;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Auth\Services\AuthService;

class ApiAuthController extends Controller
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



    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'identifiant' => 'required|string',
                'password' => 'required|string|min:8',
            ],
            Constantes::VALIDATION_MESSAGES
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->all()], 422);
        }

        try {
            $connexion = $this->authService->loginByApi(
                $validator->validated(),
            );
        } catch (\Exception $e) {
            return errorExeption($e);
        }

        return response()->json($connexion, $connexion['code']);
    }

    public function logout()
    {
        $this->authService->logoutApi();
        return response()->json(['success' => true, 'message' => 'Deconnexion reussie'], 200);
    }



    public function refresh()
    {
        return response()->json([
            'success' => true,
            'message' => 'Jetons mis à jour',
            'token' => $this->authService->refresh(),
        ], 200);
    }
}
