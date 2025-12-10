<?php

namespace Modules\Paiement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Paiement\Database\Factories\CaisseFactory;

class Caisse extends Model
{
    use HasFactory;

   protected $table = 'paiement_caisses';
   protected $guarded = ['id'];

   

}
