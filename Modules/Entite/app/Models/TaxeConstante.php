<?php 

namespace Modules\Entite\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxeConstante extends Model
{
    use HasFactory;

    protected $table = 'entite_taxes_constantes';
    protected $guarded = ['id'];

    public function taxe() {
        return $this->belongsTo(Taxe::class, 'taxe_id');
    }
}