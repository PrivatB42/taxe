<?php

namespace Modules\User\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Modules\User\Models\ActiviteLog;
use Modules\User\Models\Gestionnaire;

class ActiviteLogService
{
    /**
     * Enregistrer une activité
     */
    public static function log(
        string $action,
        string $modelType,
        ?int $modelId,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ?ActiviteLog {
        $user = Auth::guard('api')->user() ?? Auth::user();
        
        if (!$user) {
            return null;
        }

        // Trouver le gestionnaire associé au compte
        $gestionnaire = Gestionnaire::where('personne_id', $user->personne_id)->first();
        
        if (!$gestionnaire) {
            return null;
        }

        return ActiviteLog::create([
            'gestionnaire_id' => $gestionnaire->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log de création
     */
    public static function logCreate(string $modelType, int $modelId, string $description, ?array $newValues = null): ?ActiviteLog
    {
        return self::log('create', $modelType, $modelId, $description, null, $newValues);
    }

    /**
     * Log de modification
     */
    public static function logUpdate(string $modelType, int $modelId, string $description, ?array $oldValues = null, ?array $newValues = null): ?ActiviteLog
    {
        return self::log('update', $modelType, $modelId, $description, $oldValues, $newValues);
    }

    /**
     * Log de suppression
     */
    public static function logDelete(string $modelType, int $modelId, string $description, ?array $oldValues = null): ?ActiviteLog
    {
        return self::log('delete', $modelType, $modelId, $description, $oldValues, null);
    }

    /**
     * Log de changement de statut
     */
    public static function logToggle(string $modelType, int $modelId, string $description, bool $oldStatus, bool $newStatus): ?ActiviteLog
    {
        return self::log('toggle', $modelType, $modelId, $description, ['is_active' => $oldStatus], ['is_active' => $newStatus]);
    }

    /**
     * Obtenir les activités d'un gestionnaire
     */
    public static function getByGestionnaire(int $gestionnaireId, int $limit = 50)
    {
        return ActiviteLog::where('gestionnaire_id', $gestionnaireId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir toutes les activités récentes
     */
    public static function getRecent(int $limit = 100)
    {
        return ActiviteLog::with('gestionnaire.personne')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Statistiques par gestionnaire
     */
    public static function getStatsByGestionnaire(int $gestionnaireId): array
    {
        $logs = ActiviteLog::where('gestionnaire_id', $gestionnaireId);
        
        return [
            'total' => $logs->count(),
            'today' => (clone $logs)->whereDate('created_at', today())->count(),
            'this_week' => (clone $logs)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'by_action' => ActiviteLog::where('gestionnaire_id', $gestionnaireId)
                ->selectRaw('action, count(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
        ];
    }
}


