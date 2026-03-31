<?php

namespace App\Utils;

class Response {
    public static function json(mixed $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public static function success(mixed $data = null): void {
        self::json(['success' => true, 'data' => $data]);
    }

    public static function error(string $message, int $statusCode = 400): void {
        self::json(['success' => false, 'error' => $message], $statusCode);
    }
}