<?php

namespace App\Controllers;

use App\Models\User;
use App\Utils\Response;
use App\Middleware\AuthMiddleware;

class UserController {
    
    public function updateProfile(): void {
        AuthMiddleware::handle();
        
        $authUser = $_SERVER['auth_user'];
        $data = json_decode(file_get_contents('php://input'), true);
        
        $updated = User::updateProfile(
            $authUser->user_id,
            $data['fullname'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null
        );
        
        if ($updated) {
            $user = User::findById($authUser->user_id);
            unset($user['password']);
            Response::success($user);
        } else {
            Response::error('Ошибка при обновлении профиля', 500);
        }
    }
}