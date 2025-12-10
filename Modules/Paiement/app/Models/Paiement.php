<?php

namespace Modules\Paiement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
