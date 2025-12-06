<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiviteLog extends Model
{
    protected $table = 'user_activites_log';

    protected $guarded = ['id'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relation avec le gestionnaire
     */
    public function gestionnaire(): BelongsTo
    {
        return $this->belongsTo(Gestionnaire::class, 'gestionnaire_id');
    }

    /**
     * Obtenir le modèle concerné
     */
    public function getModelAttribute()
    {
        if ($this->model_type && $this->model_id) {
            $modelClass = "Modules\\User\\Models\\{$this->model_type}";
            if (class_exists($modelClass)) {
                return $modelClass::find($this->model_id);
            }
        }
        return null;
    }

    /**
     * Libellé de l'action
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'create' => 'Création',
            'update' => 'Modification',
            'delete' => 'Suppression',
            'toggle' => 'Changement de statut',
            'view' => 'Consultation',
            default => $this->action,
        };
    }

    /**
     * Couleur du badge selon l'action
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'toggle' => 'warning',
            'view' => 'secondary',
            default => 'primary',
        };
    }

    /**
     * Icône selon l'action
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            'create' => 'fa-plus-circle',
            'update' => 'fa-edit',
            'delete' => 'fa-trash',
            'toggle' => 'fa-toggle-on',
            'view' => 'fa-eye',
            default => 'fa-circle',
        };
    }
}

