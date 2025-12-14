<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'user_roles';
    protected $guarded = ['id'];

    public function permissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id');
    }

    public function gestionnaires()
    {
        return $this->hasMany(Gestionnaire::class, 'role', 'code');
    }
}

