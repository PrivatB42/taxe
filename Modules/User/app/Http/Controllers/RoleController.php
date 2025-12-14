<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\User\Services\RoleService;

class RoleController extends BaseController
{
    public function __construct(RoleService $service)
    {
        parent::__construct(
            $service,
            'Role',
            'user::pages.role.index'
        );
    }
}

