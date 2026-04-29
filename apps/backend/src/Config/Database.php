<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $connection = null;

    public static function connect(): void {
        if (self::$connection === null) {
            try {
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $dbname = $_ENV['DB_NAME'] ?? 'radar_extreme';
                $username = $_ENV['DB_USER'] ?? 'root';
                $password = $_ENV['DB_PASSWORD'] ?? '';

                self::$connection = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );

                // Принудительная установка кодировки для Windows MySQL
                self::$connection->exec("SET NAMES utf8mb4");
                self::$connection->exec("SET CHARACTER SET utf8mb4");
                self::$connection->exec("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");
            } catch (PDOException $e) {
                die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
            }
        }
    }

    public static function getConnection(): PDO {
        return self::$connection;
    }
}