<?php

namespace Modules\Auth\Services;

use App\Helpers\Constantes;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Models\Compte;
use Modules\User\Services\PersonneService;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected Compte $compte;
    protected PersonneService $personneService;
    protected CompteService $compteService;
    public function __construct(Compte $compte, PersonneService $personneService, CompteService $compteService)
    {
        $this->compte = $compte;
        $this->personneService = $personneService;
        $this->compteService = $compteService;
    }

    public function register(array $data, $typeCompte)
    {
        $dataUser = [
            'nom' => $data['nom'],
            'prenoms' => $data['prenoms'],
            'telephone' => $data['telephone'],
            'email' => $data['email'],
            'sexe' => $data['sexe'],
            'date' => $data['date_naissance'] ?? null,
            'lieu_naissance' => $data['lieu_naissance'] ?? null
        ];

        $user = $this->personneService->store($dataUser);

        $this->compteService->create(
            $user->id,
            $typeCompte,
            $data['password']
        );
    }

    
    public function loginByApi(array $data)
    {
        $query = $this->personneService->getModel();
        $user = $query->where('telephone', $data['identifiant'])
            ->orWhere('email', $data['identifiant'])
            ->with('compte')
            ->first();

        if (!$user) {
            return ['success' => false, 'message' => 'Identifiants incorrects', 'code' => 401];
        }

        if (!$user->compte->is_active) {
            return ['success' => false, 'message' => 'Compte desactivé', 'code' => 401];
        }

        $dataUser = [
            'slug' => $user->slug,
            'nom_complet' => $user->nom_complet,
            'type_compte' => $user->compte->type_compte,
            'photo' => $user->photo ?? default_photo(),
        ];

        $dataConnexion = [
            'user_id' => $user->id ?? 'error',
            'password' => $data['password']
        ];

        if (!$token = JWTAuth::claims(['user' => $dataUser])->attempt($dataConnexion)) {
            return ['success' => false, 'message' => 'Identifiants incorrects', 'code' => 401];
        }

        return [
            'success' => true,
            'message' => 'Connexion réussie',
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'token' => $token,
            'code' => 200
        ];
    }

    public function loginByWeb(array $data){

        $query = $this->personneService->getModel();
        $user = $query->where('telephone', $data['identifiant'])
            ->orWhere('email', $data['identifiant'])
            ->with('compte')
            ->first();

        $message = null;

        if (!$user) {
            //return ['success' => false, 'message' => 'Identifiants incorrects', 'code' => 401];
            $message = 'Identifiants incorrects';
        }

        if (!$user->compte->is_active) {
           // return ['success' => false, 'message' => 'Compte desactivé', 'code' => 401];
           $message = 'Compte desactivé';
        }

        $dataUser = [
            'slug' => $user->slug,
            'nom_complet' => $user->nom_complet,
            'type_compte' => $user->compte->type_compte,
            'photo' => $user->photo ?? default_photo(),
        ];

        $dataConnexion = [
            'user_id' => $user->id ?? 'error',
            'password' => $data['password']
        ];

        if (Auth::attempt($dataConnexion) && !$message) {
            session()->put('user', $dataUser);
            return redirect()->intended('/'); 
        }

        return back()->withErrors([
            'form' => $message ?? 'Identifiant ou mot de passe incorrecte.',
        ])->onlyInput('login');
    }

    public static function logoutApi()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function logoutWeb()
    {
        Auth::logout();

        return redirect('/login'); 
    }


    public static function refresh()
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }
}
