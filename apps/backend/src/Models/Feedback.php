<?php

namespace App\Models;

use App\Config\Database;

class Feedback {
    public static function create(int $userId, string $message): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO feedback (user_id, message) VALUES (?, ?)");
        $stmt->execute([$userId, $message]);
        return (int)$db->lastInsertId();
    }

    public static function findAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT f.*, u.email FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $id, string $status): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE feedback SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}