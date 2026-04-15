<?php

namespace App\Utils;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT {
    private static function getSecret(): string {
        return $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?: 'your-secret-key-here';
    }

    public static function encode(array $payload): string {
        $payload['iat'] = time();
        $payload['exp'] = time() + 3600; // 1 hour
        return FirebaseJWT::encode($payload, self::getSecret(), 'HS256');
    }

    public static function decode(string $token): object {
        return FirebaseJWT::decode($token, new Key(self::getSecret(), 'HS256'));
    }

    public static function getAuthUser(): ?object {
        // Правильная обработка заголовка Authorization для всех версий PHP и веб-серверов
        $authHeader = null;
        
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } else {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (!str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7);
        try {
            return self::decode($token);
        } catch (\Exception $e) {
            return null;
        }
    }
}