<?php

namespace Modules\Paiement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\User\Models\Gestionnaire;

// use Modules\Paiement\Database\Factories\CaisseFactory;

class Caisse extends Model
{
    use HasFactory;

    protected $table = 'paiement_caisses';
    protected $guarded = ['id'];

    public function gestionnaire(): BelongsToMany
    {
        return $this->belongsToMany(Gestionnaire::class, 'paiement_caisses_gestionnaires', 'caisse_id', 'gestionnaire_id')
        ->where('paiement_caisses_gestionnaires.is_active', true)
            ->withTimestamps();
    }

    public function caisseGestionnaires()
    {
        return $this->hasMany(CaisseGestionnaire::class, 'caisse_id');
    }

}
