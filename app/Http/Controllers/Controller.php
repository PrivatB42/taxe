<?php

namespace App\Http\Controllers;

use Exception;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

abstract class Controller
{
    public static function authenticatedCompte()
    {
        try {
            if ($user = JWTAuth::authenticate(JWTAuth::getToken())) {
                return $user;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
