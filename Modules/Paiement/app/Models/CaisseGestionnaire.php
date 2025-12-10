<?php

namespace Modules\Paiement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\Gestionnaire;
// use Modules\Paiement\Database\Factories\CaisseGestionnaireFactory;

class CaisseGestionnaire extends Model
{
    use HasFactory;

    protected $table = 'paiement_caisses_gestionnaires';
    protected $guarded = ['id'];

    public function caisse()
    {
        return $this->belongsTo(Caisse::class, 'caisse_id');
    }

    public function gestionnaire()
    {
        return $this->belongsTo(Gestionnaire::class, 'gestionnaire_id');
    }
}
