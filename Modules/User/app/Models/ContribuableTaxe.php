<?php 

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Entite\Models\Exercice;
use Modules\Entite\Models\Taxe;

class ContribuableTaxe extends Model
{
    use HasFactory;
    protected $table = 'user_contribuables_taxes';
    protected $guarded = ['id'];

    protected $appends = ['montant_restant'];

    public function contribuable()
    {
        return $this->belongsTo(Contribuable::class, 'contribuable_id');
    }

    public function taxe()
    {
        return $this->belongsTo(Taxe::class, 'taxe_id');
    }

    public function exercice()
    {
        return $this->belongsTo(Exercice::class, 'exercice_id');
    }

    public function getMontantRestantAttribute()
    {
        return $this->montant - $this->montant_paye;
    }
}