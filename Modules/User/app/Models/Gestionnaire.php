<?php 

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Entite\Models\Commune;
use Modules\Paiement\Models\Caisse;

class Gestionnaire extends Model
{
    use HasFactory;
    protected $table = 'user_gestionnaires';
    protected $guarded = ['id'];

    protected $appends = ['nom_complet', 'photo', 'telephone', 'email'];

    public function personne()
    {
        return $this->belongsTo(Personne::class, 'personne_id');
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class, 'commune_id');
    }

      public function caisses() {
        return $this->belongsToMany(Caisse::class, 'paiement_caisses_gestionnaires', 'gestionnaire_id', 'caisse_id')
            ->where('paiement_caisses_gestionnaires.is_active', true)
            ->where('paiement_caisses_gestionnaires.date_fin', null)
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

    public function getEmailAttribute()
    {
        return $this->personne->email;
    }

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role', 'code');
    }
}