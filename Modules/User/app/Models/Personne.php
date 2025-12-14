<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Modules\Auth\Models\Compte;

class Personne extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'user_personnes';
    protected $guarded = ['id'];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

     public function compte()
    {
        return $this->hasOne(Compte::class, 'personne_id');
    }

    public function contribuable()
    {
        return $this->hasOne(Contribuable::class, 'personne_id');
    }

    public function gestionnaire()
    {
        return $this->hasOne(Gestionnaire::class, 'personne_id');
    }

}
