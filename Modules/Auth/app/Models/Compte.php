<?php

namespace Modules\Auth\Models;

use App\Helpers\Constantes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\User\Models\Personne;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Compte extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'auth_comptes';

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relation avec la personne
     */
    public function personne()
    {
        return $this->belongsTo(Personne::class, 'personne_id');
    }

    /**
     * Vérifier si c'est un admin
     */
    public function isAdmin(): bool
    {
        return $this->type_compte === Constantes::COMPTE_ADMIN;
    }

    /**
     * Vérifier si c'est un superviseur
     */
    public function isSuperviseur(): bool
    {
        return $this->type_compte === Constantes::COMPTE_SUPERVISEUR;
    }

    /**
     * Vérifier si c'est un gestionnaire
     */
    public function isGestionnaire(): bool
    {
        return $this->type_compte === Constantes::COMPTE_GESTIONNAIRE;
    }

    /**
     * Obtenir le libellé du type de compte
     */
    public function getTypeLabelAttribute(): string
    {
        return Constantes::COMPTES_LABELS[$this->type_compte] ?? $this->type_compte;
    }

    /**
     * Obtenir la couleur du badge du type de compte
     */
    public function getTypeColorAttribute(): string
    {
        return Constantes::COMPTES_COLORS[$this->type_compte] ?? 'secondary';
    }

    /**
     * Vérifier si l'utilisateur a une permission
     */
    public function hasPermission(string $permission): bool
    {
        return Constantes::hasPermission($this->type_compte, $permission);
    }
}
