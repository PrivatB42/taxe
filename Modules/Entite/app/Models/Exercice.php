<?php 

namespace Modules\Entite\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercice extends Model
{
    use HasFactory;

    protected $table = 'entite_exercices';
    protected $guarded = ['id'];

}