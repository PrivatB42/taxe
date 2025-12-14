<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'user_permissions';
    protected $guarded = ['id'];

    public function rolePermissions()
    {
        return $this->hasMany(\Modules\User\Models\RolePermission::class, 'permission_id');
    }
}

