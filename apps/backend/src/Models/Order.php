<?php

namespace App\Models;

use App\Config\Database;

class Order {
    public static function findByUserId(int $userId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT o.*, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
        return $stmt->fetchAll();
    }

    public static function create(int $userId, float $total, ?string $name = null, ?string $phone = null, ?string $email = null, ?string $wishes = null): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO orders (user_id, total, name, phone, email, wishes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $total, $name, $phone, $email, $wishes]);
        return (int)$db->lastInsertId();
    }

    public static function updateStatus(int $id, string $status): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}