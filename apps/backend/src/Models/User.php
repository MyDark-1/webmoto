<?php

namespace App\Models;

use App\Config\Database;

class User {
    public static function findByEmail(string $email): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(string $email, string $password, string $role = 'user'): int {
        $db = Database::getConnection();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashedPassword, $role]);
        return (int)$db->lastInsertId();
    }

    public static function updateProfile(int $id, ?string $fullname = null, ?string $email = null, ?string $phone = null): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET fullname = COALESCE(?, fullname), email = COALESCE(?, email), phone = COALESCE(?, phone) WHERE id = ?");
        return $stmt->execute([$fullname, $email, $phone, $id]);
    }
}
