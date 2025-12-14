<?php

namespace App\Http\Controllers;

use App\Traits\ControllerTrait;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    use ControllerTrait;

    public function __construct($service, $instanceName, $viewPath)
    {
        $this->service = $service;
        $this->instanceName = $instanceName;
        $this->viewPath = $viewPath;
    }
    
}
