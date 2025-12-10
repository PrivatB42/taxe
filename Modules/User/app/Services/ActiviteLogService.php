<?php

namespace Modules\User\Services;

use App\Helpers\Constantes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Modules\User\Models\ActiviteLog;
use Modules\User\Models\Gestionnaire;

class ActiviteLogService
{
    /**
     * Enregistrer une activité du gestionnaire
     */
    public static function log(
        string $action,
        string $modelType,
        ?int $modelId,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ?ActiviteLog {
        // Vérifier que l'utilisateur est un gestionnaire
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        
        // Seuls les gestionnaires sont suivis
        if ($user->type_compte !== Constantes::COMPTE_GESTIONNAIRE) {
            return null;
        }

        // Récupérer le gestionnaire associé
        $gestionnaire = Gestionnaire::whereHas('personne', function ($query) use ($user) {
            $query->where('id', $user->personne_id);
        })->first();

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
     * Logger une création
     */
    public static function logCreate(string $modelType, int $modelId, string $description, array $values = []): ?ActiviteLog
    {
        return self::log('create', $modelType, $modelId, $description, null, $values);
    }

    /**
     * Logger une modification
     */
    public static function logUpdate(string $modelType, int $modelId, string $description, array $oldValues, array $newValues): ?ActiviteLog
    {
        return self::log('update', $modelType, $modelId, $description, $oldValues, $newValues);
    }

    /**
     * Logger une suppression
     */
    public static function logDelete(string $modelType, int $modelId, string $description, array $oldValues = []): ?ActiviteLog
    {
        return self::log('delete', $modelType, $modelId, $description, $oldValues, null);
    }

    /**
     * Logger un changement de statut
     */
    public static function logToggle(string $modelType, int $modelId, string $description, bool $oldStatus, bool $newStatus): ?ActiviteLog
    {
        return self::log('toggle', $modelType, $modelId, $description, ['is_active' => $oldStatus], ['is_active' => $newStatus]);
    }

    /**
     * Logger une consultation
     */
    public static function logView(string $modelType, int $modelId, string $description): ?ActiviteLog
    {
        return self::log('view', $modelType, $modelId, $description);
    }

    /**
     * Obtenir les statistiques des activités
     */
    public static function getStats(?int $gestionnaireId = null, ?string $dateDebut = null, ?string $dateFin = null): array
    {
        $query = ActiviteLog::query();

        if ($gestionnaireId) {
            $query->where('gestionnaire_id', $gestionnaireId);
        }

        if ($dateDebut) {
            $query->whereDate('created_at', '>=', $dateDebut);
        }

        if ($dateFin) {
            $query->whereDate('created_at', '<=', $dateFin);
        }

        $total = $query->count();
        
        $parAction = (clone $query)->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();

        $parModelType = (clone $query)->selectRaw('model_type, COUNT(*) as count')
            ->groupBy('model_type')
            ->pluck('count', 'model_type')
            ->toArray();

        return [
            'total' => $total,
            'par_action' => $parAction,
            'par_model' => $parModelType,
            'creations' => $parAction['create'] ?? 0,
            'modifications' => $parAction['update'] ?? 0,
            'suppressions' => $parAction['delete'] ?? 0,
            'changements_statut' => $parAction['toggle'] ?? 0,
        ];
    }
}
