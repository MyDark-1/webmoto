<?php

namespace App\Middleware;

use App\Utils\Response;

class AdminMiddleware {
    public static function handle(): void {
        $user = $_SERVER['auth_user'] ?? null;

        if (!$user || $user->role !== 'admin') {
            Response::error('Forbidden', 403);
            exit;
        }
    }
}