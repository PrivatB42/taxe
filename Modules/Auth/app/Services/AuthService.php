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
            ->with('compte', 'gestionnaire')
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

        // Vérifier le mot de passe manuellement
        $compte = $user->compte;
        if (!\Hash::check($data['password'], $compte->password)) {
            return ['success' => false, 'message' => 'Identifiants incorrects', 'code' => 401];
        }

        // Générer le token JWT avec l'ID du compte
        $token = JWTAuth::claims(['user' => $dataUser])->fromUser($compte);
        
        if (!$token) {
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
        $query = $this->personneService->getModel();
        $user = $query->where('telephone', $data['identifiant'])
            ->orWhere('email', $data['identifiant'])
            ->with('compte', 'gestionnaire')
            ->first();

        try {
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Identifiants incorrects',
                    'code' => 401
                ];
            }

            if (!$user->compte) {
                return [
                    'success' => false,
                    'message' => 'Compte inexistant',
                    'code' => 401
                ];
            }

            if (!$user->compte->is_active) {
                return [
                    'success' => false,
                    'message' => 'Compte désactivé',
                    'code' => 401
                ];
            }

            $caisse = null;
            if ($user->gestionnaire?->role == Constantes::ROLE_CAISSIER) {
                $caisse = $user->gestionnaire?->caisses()?->first();
            }

            $dataUser = [
                'id' => $user->id,
                'slug' => $user->slug,
                'nom_complet' => $user->nom_complet,
                'type_compte' => $user->compte->type_compte,
                'photo' => $user->photo ?? default_photo(),
                'role' => $user?->gestionnaire?->role,
                'gestionnaire_id' => $user?->gestionnaire?->id,
                'caisse' => $caisse
            ];

            // Authentification avec le compte pour générer le token JWT
            // JWT utilise l'ID du compte (auth_comptes.id) pour l'authentification
            $compte = $user->compte;
            
            // Vérifier le mot de passe manuellement
            if (!\Hash::check($data['password'], $compte->password)) {
                return [
                    'success' => false,
                    'message' => 'Identifiant ou mot de passe incorrect',
                    'code' => 401
                ];
            }

            // Générer le token JWT avec l'ID du compte
            $token = JWTAuth::claims(['user' => $dataUser])->fromUser($compte);
            
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Identifiant ou mot de passe incorrect',
                    'code' => 401
                ];
            }

            // Authentifier aussi avec la session pour compatibilité
            Auth::loginUsingId($user->compte->id);
            session()->put('user', $dataUser);
            session()->save();

            // Déterminer la page d'atterrissage : priorité caissier -> /contribuables
            if ($user?->gestionnaire?->role === Constantes::ROLE_CAISSIER) {
                $redirect = '/contribuables';
            } else {
                $redirect = $this->getLandingUrl();
            }

            return [
                'success' => true,
                'message' => 'Connexion réussie',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => $dataUser,
                'redirect' => $redirect,
                'code' => 200
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 500
            ];
        }
    }

    public static function logoutApi()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function logoutWeb()
    {
        try {
            // Invalider le token JWT si présent
            $token = request()->bearerToken() 
                ?? request()->header('Authorization') 
                ?? request()->query('token');
            
            if ($token) {
                $token = str_replace('Bearer ', '', $token);
                try {
                    JWTAuth::setToken($token)->invalidate();
                } catch (\Exception $e) {
                    // Token déjà invalide ou expiré, continuer
                }
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs de token
        }

        Auth::logout();
        session()->forget('user');
        session()->flush();

        // Si c'est une requête AJAX, retourner JSON
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie',
                'redirect' => route('auth.login')
            ]);
        }

        return redirect(route('auth.login'));
    }


    public static function refresh()
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    /**
     * Retourne une URL d'atterrissage en fonction des permissions/rôle.
     */
    protected function getLandingUrl(): string
    {
        if (function_exists('can_permission')) {
            if (can_permission(\App\Helpers\Constantes::PERMISSION_TABLEAU_BORD)) {
                return '/dashboard';
            }
            if (can_permission(\App\Helpers\Constantes::PERMISSION_GERER_CAISSES) ||
                can_permission(\App\Helpers\Constantes::PERMISSION_OUVRIR_FERMER_CAISSE)) {
                return '/caisses';
            }
            if (can_permission(\App\Helpers\Constantes::PERMISSION_ENCASSER)) {
                return '/paiements';
            }
            if (can_permission(\App\Helpers\Constantes::PERMISSION_GERER_UTILISATEURS)) {
                return '/utilisateurs/gestionnaires';
            }
            if (can_permission(\App\Helpers\Constantes::PERMISSION_GERER_CONTRIBUABLES)) {
                return '/contribuables';
            }
        }
        // Fallback
        return '/dashboard';
    }
}
