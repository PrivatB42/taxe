<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Entite\Models\Activite;
use Modules\Entite\Models\Commune;

class Contribuable extends Model
{
    use HasFactory;
    protected $table = 'user_contribuables';
    protected $guarded = ['id'];

    protected $appends = ['nom_complet', 'photo', 'telephone'];

    public function personne()
    {
        return $this->belongsTo(Personne::class, 'personne_id');
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class, 'commune_id');
    }

    public function contribuableActivites()
    {
        return $this->hasMany(ContribuableActivite::class, 'contribuable_id');
    }

    public function activites()
    {
        return $this->belongsToMany(Activite::class, 'user_contribuables_activites')
            ->withPivot('annee_debut')
            ->withTimestamps();
    }

    public function contribuableTaxe(){
        return $this->hasMany(ContribuableTaxe::class, 'contribuable_id');
    }


    public function getNomCompletAttribute()
    {
        return $this->personne->nom_complet;
    }

    public function getPhotoAttribute()
    {
        return $this->personne->photo ?? default_photo();
    }

    public function getTelephoneAttribute()
    {
        return $this->personne->telephone;
    }

    public function parametres()
    {
        //return $this->hasMany(ContribuableParametre::class, 'contribuable_id');

         return $this->hasManyThrough(
            ContribuableParametre::class,
            ContribuableActivite::class,
            'contribuable_id', // Clé étrangère dans contribuable_activite
            'contribuable_activite_id', // Clé étrangère dans parametres
            'id', // Clé primaire de contribuable
            'id' // Clé primaire de contribuable_activite
        );
    }

    public function parametresByActivite(int $activiteId)
    {
        return $this->parametres()->whereHas('contribuableActivite', function($q) use ($activiteId) {
            $q->where('activite_id', $activiteId);
        });
    }

    public function parametresByContribuableActivite(int $contribuableActiviteId)
    {
        return $this->parametres()->where('contribuable_activite_id', $contribuableActiviteId);
    }

    public function getParam($key, $default = null)
    {
        $param = $this->parametres->where('nom', $key)->first();

        if (!$param) {
            return $default;
        }

        return match ($param->type) {
            'int' => (int) $param->valeur,
            'decimal' => (float) $param->valeur,
            'bool' => (bool) $param->valeur,
            default => $param->valeur,
        };
    }
}
