<?php

namespace Modules\Paiement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Entite\Models\Taxe;
use Modules\User\Models\Contribuable;
use Modules\User\Models\ContribuableTaxe;
use Modules\User\Models\Gestionnaire;

// use Modules\Paiement\Database\Factories\PaiementFactory;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiement_paiements';
    protected $guarded = ['id'];

    public function caisse()
    {
        return $this->belongsTo(Caisse::class, 'caisse_id');
    }

    public function caissier()
    {
        return $this->belongsTo(Gestionnaire::class, 'caissier_id');
    }

    public function contribuableTaxe()
    {
        return $this->belongsTo(ContribuableTaxe::class, 'contribuable_taxe_id');
    }

    public function contribuable()
    {
        return $this->hasOneThrough(
            Contribuable::class,
            ContribuableTaxe::class,
            'id',
            'id',
            'contribuable_taxe_id',
            'contribuable_id'
        );
    }

    public function taxe()
    {
        return $this->hasOneThrough(
            Taxe::class,
            ContribuableTaxe::class,
            'id',
            'id',
            'contribuable_taxe_id',
            'taxe_id'
        );
    }
}
