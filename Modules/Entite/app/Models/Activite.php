<?php 

namespace Modules\Entite\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activite extends Model
{
    use HasFactory;

    protected $table = 'entite_activites';
    protected $guarded = ['id'];

    public function taxes()
    {
        return $this->belongsToMany(Taxe::class, 'entite_activites_taxes')->withTimestamps();
    }
}