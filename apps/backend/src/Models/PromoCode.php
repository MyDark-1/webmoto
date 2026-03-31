<?php

namespace App\Models;

use App\Config\Database;

class PromoCode {
    public static function findByCode(string $code): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM promo_codes WHERE code = ? AND active = TRUE AND expires_at > NOW()");
        $stmt->execute([$code]);
        return $stmt->fetch() ?: null;
    }

    public static function findAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM promo_codes ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO promo_codes (code, discount, active, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['code'],
            $data['discount'],
            $data['active'] ?? true,
            $data['expires_at']
        ]);
        return (int)$db->lastInsertId();
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM promo_codes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}