<?php 

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Entite\Models\Activite;

class ContribuableActivite extends Model
{
    use HasFactory;
    protected $table = 'user_contribuables_activites';
    protected $guarded = ['id'];

    public function activite()
    {
        return $this->belongsTo(Activite::class, 'activite_id');
    }

    public function contribuable()
    {
        return $this->belongsTo(Contribuable::class, 'contribuable_id');
    }
}