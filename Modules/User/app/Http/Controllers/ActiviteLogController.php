<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\User\Models\ActiviteLog;
use Modules\User\Models\Gestionnaire;
use Modules\User\Services\ActiviteLogService;

class ActiviteLogController extends Controller
{
    /**
     * Afficher la page des activités
     */
    public function index()
    {
        $gestionnaires = Gestionnaire::with('personne')->get();
        return view('user::pages.activites-log.index', compact('gestionnaires'));
    }

    /**
     * Données pour le DataTable
     */
    public function getData(Request $request)
    {
        $query = ActiviteLog::with(['gestionnaire.personne']);

        // Filtre par gestionnaire
        if ($request->filled('gestionnaire_id')) {
            $query->where('gestionnaire_id', $request->gestionnaire_id);
        }

        // Filtre par action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtre par date
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Recherche globale
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%")
                  ->orWhereHas('gestionnaire.personne', function($q2) use ($search) {
                      $q2->where('nom_complet', 'like', "%{$search}%");
                  });
            });
        }

        $totalRecords = ActiviteLog::count();
        $filteredRecords = $query->count();

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        // Tri
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        
        $columns = ['id', 'gestionnaire_id', 'action', 'description', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';

        $data = $query->orderBy($orderBy, $orderDir)
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'gestionnaire' => $log->gestionnaire?->personne?->nom_complet ?? 'N/A',
                    'gestionnaire_photo' => $log->gestionnaire?->photo ?? default_photo(),
                    'action' => $log->action,
                    'action_label' => $log->action_label,
                    'action_color' => $log->action_color,
                    'action_icon' => $log->action_icon,
                    'model_type' => $log->model_type,
                    'description' => $log->description,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->format('d/m/Y H:i'),
                    'created_at_human' => $log->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Statistiques des activités
     */
    public function stats()
    {
        $stats = [
            'total' => ActiviteLog::count(),
            'today' => ActiviteLog::whereDate('created_at', today())->count(),
            'this_week' => ActiviteLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'by_action' => ActiviteLog::selectRaw('action, count(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
            'by_gestionnaire' => ActiviteLog::with('gestionnaire.personne')
                ->selectRaw('gestionnaire_id, count(*) as count')
                ->groupBy('gestionnaire_id')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'gestionnaire' => $item->gestionnaire?->personne?->nom_complet ?? 'N/A',
                        'count' => $item->count,
                    ];
                }),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    /**
     * Activités d'un gestionnaire spécifique
     */
    public function byGestionnaire(int $gestionnaireId)
    {
        $gestionnaire = Gestionnaire::with('personne')->findOrFail($gestionnaireId);
        $stats = ActiviteLogService::getStatsByGestionnaire($gestionnaireId);
        $recentActivities = ActiviteLogService::getByGestionnaire($gestionnaireId, 20);

        return response()->json([
            'success' => true,
            'data' => [
                'gestionnaire' => $gestionnaire,
                'stats' => $stats,
                'recent_activities' => $recentActivities,
            ]
        ]);
    }
}


