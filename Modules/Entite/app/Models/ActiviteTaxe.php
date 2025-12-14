<?php 

namespace Modules\Entite\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiviteTaxe extends Model
{
    use HasFactory;

    protected $table = 'entite_activites_taxes';
    protected $guarded = ['id'];

    public function activite(){
        return $this->belongsTo(Activite::class , 'activite_id');
    }

    public function taxe() {
        return $this->belongsTo(Taxe::class, 'taxe_id');
    }
}