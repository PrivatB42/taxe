<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Helpers\Constantes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Models\Contribuable;
use Modules\User\Models\Gestionnaire;
use Modules\User\Models\ActiviteLog;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord selon le rôle
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->type_compte;

        // Les gestionnaires sont redirigés vers la page contribuables
        if ($role === Constantes::COMPTE_GESTIONNAIRE) {
            return redirect()->route('contribuables.index');
        }

        // Pour superviseur et admin, afficher le dashboard
        $data = $this->getDashboardData($role);

        return view('dashboard::pages.index', $data);
    }

    /**
     * Obtenir les données du dashboard selon le rôle
     */
    private function getDashboardData(string $role): array
    {
        $data = [
            'role' => $role,
            'user' => Auth::user(),
        ];

        // Statistiques pour superviseur et admin
        if (in_array($role, [Constantes::COMPTE_SUPERVISEUR, Constantes::COMPTE_ADMIN])) {
            $data['stats'] = [
                'gestionnaires' => [
                    'total' => Gestionnaire::count(),
                    'actifs' => Gestionnaire::where('is_active', true)->count(),
                    'inactifs' => Gestionnaire::where('is_active', false)->count(),
                ],
                'activites' => [
                    'total' => ActiviteLog::count(),
                    'today' => ActiviteLog::whereDate('created_at', today())->count(),
                    'this_week' => ActiviteLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                ],
            ];

            // Dernières activités
            $data['dernieres_activites'] = ActiviteLog::with(['gestionnaire.personne'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        // Statistiques supplémentaires pour admin
        if ($role === Constantes::COMPTE_ADMIN) {
            $data['stats']['contribuables'] = [
                'total' => Contribuable::count(),
                'actifs' => Contribuable::where('is_active', true)->count(),
            ];
        }

        return $data;
    }

    /**
     * API pour les statistiques du dashboard
     */
    public function stats(Request $request)
    {
        $role = Auth::user()->type_compte;
        
        if (!in_array($role, [Constantes::COMPTE_SUPERVISEUR, Constantes::COMPTE_ADMIN])) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $stats = [
            'gestionnaires' => [
                'total' => Gestionnaire::count(),
                'actifs' => Gestionnaire::where('is_active', true)->count(),
            ],
            'activites' => [
                'total' => ActiviteLog::count(),
                'today' => ActiviteLog::whereDate('created_at', today())->count(),
                'this_week' => ActiviteLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            ],
        ];

        if ($role === Constantes::COMPTE_ADMIN) {
            $stats['contribuables'] = [
                'total' => Contribuable::count(),
                'actifs' => Contribuable::where('is_active', true)->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
