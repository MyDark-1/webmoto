<?php

namespace App\Middleware;

use App\Utils\JWT;
use App\Utils\Response;

class AuthMiddleware {
    public static function handle(): void {
        $user = JWT::getAuthUser();

        if (!$user) {
            Response::error('Unauthorized', 401);
            exit;
        }

        $_SERVER['auth_user'] = $user;
    }
}