<?php 

namespace Modules\Entite\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxe extends Model
{
    use HasFactory;

    protected $table = 'entite_taxes';
    protected $guarded = ['id']; 
     public function activites()
    {
        return $this->belongsToMany(Activite::class, 'entite_activites_taxes')->withTimestamps();
    }

    public function constantes(){
        return $this->hasMany(TaxeConstante::class, 'taxe_id');
    }
}