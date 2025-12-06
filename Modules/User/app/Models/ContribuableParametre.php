<?php 

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContribuableParametre extends Model
{
    use HasFactory;
    protected $table = 'user_contribuables_parametres';
    protected $guarded = ['id'];

    public function contribuable()
    {
        return $this->belongsTo(Contribuable::class, 'contribuable_id');
    }
}