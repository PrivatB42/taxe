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

    public function loginByWeb(array $data)
    {
        // Rechercher le compte par numéro de compte, email ou téléphone
        $compte = Compte::where('numero_compte', $data['identifiant'])
            ->orWhereHas('personne', function ($query) use ($data) {
                $query->where('telephone', $data['identifiant'])
                    ->orWhere('email', $data['identifiant']);
            })
            ->with('personne')
            ->first();

        if (!$compte) {
            return back()->withErrors([
                'identifiant' => 'Identifiants incorrects. Vérifiez votre email, téléphone ou numéro de compte.',
            ])->withInput();
        }

        if (!$compte->is_active) {
            return back()->withErrors([
                'identifiant' => 'Votre compte a été désactivé. Contactez l\'administrateur.',
            ])->withInput();
        }

        // Vérifier le mot de passe
        if (!Auth::attempt(['numero_compte' => $compte->numero_compte, 'password' => $data['password']])) {
            return back()->withErrors([
                'password' => 'Mot de passe incorrect.',
            ])->withInput();
        }

        // Stocker les infos utilisateur en session
        $user = Auth::user();
        session()->put('user', [
            'id' => $user->id,
            'numero_compte' => $user->numero_compte,
            'type_compte' => $user->type_compte,
            'nom_complet' => $user->personne->nom_complet ?? 'Utilisateur',
            'photo' => $user->personne->photo ?? default_photo(),
            'email' => $user->personne->email ?? null,
            'telephone' => $user->personne->telephone ?? null,
        ]);

        // Rediriger selon le rôle
        return $this->redirectByRole($user->type_compte);
    }

    /**
     * Rediriger l'utilisateur selon son rôle
     */
    private function redirectByRole(string $typeCompte)
    {
        return match ($typeCompte) {
            Constantes::COMPTE_GESTIONNAIRE => redirect()->route('contribuables.index')
                ->with('success', 'Bienvenue ! Vous êtes connecté en tant que Gestionnaire.'),
            Constantes::COMPTE_SUPERVISEUR => redirect()->route('dashboard')
                ->with('success', 'Bienvenue ! Vous êtes connecté en tant que Superviseur.'),
            Constantes::COMPTE_ADMIN => redirect()->route('dashboard')
                ->with('success', 'Bienvenue ! Vous êtes connecté en tant qu\'Administrateur.'),
            default => redirect()->route('dashboard')
                ->with('success', 'Connexion réussie !'),
        };
    }

    public static function logoutApi()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function logoutWeb()
    {
        Auth::logout();
        session()->forget('user');
        return redirect('/login');
    }

    public static function refresh()
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }
}
