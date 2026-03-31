<?php

namespace App\Utils;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT {
    private static string $secret = 'your-secret-key-here';

    public static function encode(array $payload): string {
        $payload['iat'] = time();
        $payload['exp'] = time() + 3600; // 1 hour
        return FirebaseJWT::encode($payload, self::$secret, 'HS256');
    }

    public static function decode(string $token): object {
        return FirebaseJWT::decode($token, new Key(self::$secret, 'HS256'));
    }

    public static function getAuthUser(): ?object {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

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