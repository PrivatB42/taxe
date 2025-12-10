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
     * Afficher la liste des activités
     */
    public function index()
    {
        $gestionnaires = Gestionnaire::with('personne')->get();
        
        return view('user::pages.activites-log.index', [
            'gestionnaires' => $gestionnaires,
        ]);
    }

    /**
     * Obtenir les données pour DataTable
     */
    public function data(Request $request)
    {
        $query = ActiviteLog::with(['gestionnaire.personne'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('gestionnaire_id')) {
            $query->where('gestionnaire_id', $request->gestionnaire_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Recherche
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('model_type', 'like', "%{$search}%")
                    ->orWhereHas('gestionnaire.personne', function ($q2) use ($search) {
                        $q2->where('nom_complet', 'like', "%{$search}%");
                    });
            });
        }

        $total = ActiviteLog::count();
        $filtered = $query->count();

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        
        $activites = $query->skip($start)->take($length)->get();

        $data = $activites->map(function ($activite) {
            return [
                'id' => $activite->id,
                'gestionnaire' => [
                    'id' => $activite->gestionnaire->id ?? null,
                    'nom' => $activite->gestionnaire->nom_complet ?? 'N/A',
                    'photo' => $activite->gestionnaire->photo ?? default_photo(),
                ],
                'action' => $activite->action,
                'action_label' => $activite->action_label,
                'action_color' => $activite->action_color,
                'action_icon' => $activite->action_icon,
                'model_type' => $activite->model_type,
                'model_id' => $activite->model_id,
                'description' => $activite->description,
                'ip_address' => $activite->ip_address,
                'created_at' => $activite->created_at->format('d/m/Y H:i'),
                'created_at_human' => $activite->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $data,
        ]);
    }

    /**
     * Obtenir les statistiques
     */
    public function stats(Request $request)
    {
        $stats = ActiviteLogService::getStats(
            $request->gestionnaire_id,
            $request->date_debut,
            $request->date_fin
        );

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Voir les détails d'une activité
     */
    public function show($id)
    {
        $activite = ActiviteLog::with(['gestionnaire.personne'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $activite->id,
                'gestionnaire' => [
                    'nom' => $activite->gestionnaire->nom_complet ?? 'N/A',
                    'photo' => $activite->gestionnaire->photo ?? default_photo(),
                ],
                'action' => $activite->action,
                'action_label' => $activite->action_label,
                'model_type' => $activite->model_type,
                'model_id' => $activite->model_id,
                'description' => $activite->description,
                'old_values' => $activite->old_values,
                'new_values' => $activite->new_values,
                'ip_address' => $activite->ip_address,
                'user_agent' => $activite->user_agent,
                'created_at' => $activite->created_at->format('d/m/Y H:i:s'),
            ],
        ]);
    }
}
