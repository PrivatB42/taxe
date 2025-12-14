<?php 

namespace Modules\Entite\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Models\Contribuable;

class Commune extends Model
{
    use HasFactory;

    protected $table = 'entite_communes';
    protected $guarded = ['id'];

    public function contribuables()
    {
        return $this->hasMany(Contribuable::class, 'commune_id');
    }
}