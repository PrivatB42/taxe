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

     public function activites()
    {
        return $this->belongsToMany(Activite::class, 'user_contribuables_activites')
                    ->withPivot('annee_debut')
                    ->withTimestamps();
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
}