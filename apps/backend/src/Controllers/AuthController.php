<?php

namespace App\Controllers;

use App\Models\User;
use App\Utils\JWT;
use App\Utils\Response;
use App\Utils\Validator;

class AuthController {
    public function login(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        $errors = Validator::validate($data, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (!empty($errors)) {
            Response::error('Validation failed', 400);
            return;
        }

        $user = User::findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            Response::error('Invalid credentials', 401);
            return;
        }

        $token = JWT::encode(['user_id' => $user['id'], 'role' => $user['role']]);

        Response::success([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'fullname' => $user['fullname'] ?? '',
                'phone' => $user['phone'] ?? '',
                'role' => $user['role']
            ]
        ]);
    }

    public function register(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        $errors = Validator::validate($data, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (!empty($errors)) {
            Response::error('Validation failed', 400);
            return;
        }

        if (User::findByEmail($data['email'])) {
            Response::error('Email already exists', 400);
            return;
        }

        $fullname = trim($data['fullname'] ?? '');
        $phone = trim($data['phone'] ?? '');

        $userId = User::create($data['email'], $data['password'], $fullname, $phone);

        $token = JWT::encode(['user_id' => $userId, 'role' => 'user']);

        Response::success([
            'token' => $token,
            'user' => [
                'id' => $userId,
                'email' => $data['email'],
                'fullname' => $fullname,
                'phone' => $phone,
                'role' => 'user'
            ]
        ]);
    }

    public function me(): void {
        $authUser = $_SERVER['auth_user'] ?? null;

        if (!$authUser) {
            Response::error('Unauthorized', 401);
            return;
        }

        $user = User::findById($authUser->user_id);

        if (!$user) {
            Response::error('User not found', 404);
            return;
        }

        Response::success([
            'id' => $user['id'],
            'email' => $user['email'],
            'fullname' => $user['fullname'] ?? '',
            'phone' => $user['phone'] ?? '',
            'role' => $user['role']
        ]);
    }
}