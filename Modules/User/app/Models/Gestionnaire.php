<?php 

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Models\Compte;
use Modules\Entite\Models\Commune;

class Gestionnaire extends Model
{
    use HasFactory;
    protected $table = 'user_gestionnaires';
    protected $guarded = ['id'];

    protected $appends = ['nom_complet', 'photo', 'telephone', 'email', 'is_online', 'derniere_connexion'];

    public function personne()
    {
        return $this->belongsTo(Personne::class, 'personne_id');
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class, 'commune_id');
    }

    public function compte()
    {
        return $this->hasOneThrough(
            Compte::class,
            Personne::class,
            'id', // Foreign key on Personne table
            'personne_id', // Foreign key on Compte table
            'personne_id', // Local key on Gestionnaire table
            'id' // Local key on Personne table
        );
    }

    /**
     * Relation avec les logs d'activités
     */
    public function activitesLog(): HasMany
    {
        return $this->hasMany(ActiviteLog::class, 'gestionnaire_id');
    }

    public function getNomCompletAttribute()
    {
        return $this->personne->nom_complet ?? '';
    }

    public function getPhotoAttribute()
    {
        return $this->personne->photo ?? default_photo();
    }

    public function getTelephoneAttribute()
    {
        return $this->personne->telephone ?? '';
    }

    public function getEmailAttribute()
    {
        return $this->personne->email ?? '';
    }

    /**
     * Vérifie si le gestionnaire est en ligne (activité dans les 5 dernières minutes)
     */
    public function getIsOnlineAttribute(): bool
    {
        $lastActivity = $this->activitesLog()->latest()->first();
        if (!$lastActivity) {
            return false;
        }
        return $lastActivity->created_at->diffInMinutes(now()) < 5;
    }

    /**
     * Dernière connexion/activité
     */
    public function getDerniereConnexionAttribute(): ?string
    {
        $lastActivity = $this->activitesLog()->latest()->first();
        return $lastActivity?->created_at?->diffForHumans();
    }

    /**
     * Nombre total d'actions aujourd'hui
     */
    public function getActionsAujourdhuiAttribute(): int
    {
        return $this->activitesLog()->whereDate('created_at', today())->count();
    }

    /**
     * Statistiques du gestionnaire
     */
    public function getStatistiquesAttribute(): array
    {
        return [
            'total_actions' => $this->activitesLog()->count(),
            'actions_aujourdhui' => $this->actions_aujourdhui,
            'actions_semaine' => $this->activitesLog()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];
    }
}