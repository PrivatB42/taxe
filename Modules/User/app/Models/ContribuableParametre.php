<?php 

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Entite\Models\Activite;

class ContribuableParametre extends Model
{
    use HasFactory;
    protected $table = 'user_contribuables_parametres';
    protected $guarded = ['id'];

    public function contribuableActivite()
    {
        return $this->belongsTo(ContribuableActivite::class, 'contribuable_activite_id');
    }

    public function contribuable()
    {
        return $this->hasOneThrough(
            Contribuable::class,
            ContribuableActivite::class,
            'id', // Clé primaire de contribuable_activite
            'id', // Clé primaire de contribuable
            'contribuable_activite_id', // Clé étrangère dans parametres
            'contribuable_id' // Clé étrangère dans contribuable_activite
        );
    }

    public function activite()
    {
        return $this->hasOneThrough(
            Activite::class,
            ContribuableActivite::class,
            'id', // Clé primaire de contribuable_activite
            'id', // Clé primaire de activite
            'contribuable_activite_id', // Clé étrangère dans parametres
            'activite_id' // Clé étrangère dans contribuable_activite
        );
    }

     public function scopeForContribuable($query, $contribuableId)
    {
        return $query->whereHas('contribuableActivite', function($q) use ($contribuableId) {
            $q->where('contribuable_id', $contribuableId);
        });
    }

    public function scopeForActivite($query, $activiteId)
    {
        return $query->whereHas('contribuableActivite', function($q) use ($activiteId) {
            $q->where('activite_id', $activiteId);
        });
    }

    public function scopeForContribuableActivite($query, $contribuableId, $activiteId)
    {
        return $query->whereHas('contribuableActivite', function($q) use ($contribuableId, $activiteId) {
            $q->where('contribuable_id', $contribuableId)
              ->where('activite_id', $activiteId);
        });
    }

}