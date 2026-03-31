<?php

namespace App\Models;

use App\Config\Database;

class Promotion {
    public static function findAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM promotions WHERE active = TRUE ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM promotions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO promotions (title, content, image, discount, active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['content'],
            $data['image'],
            $data['discount'],
            $data['active'] ?? true
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE promotions SET title = ?, content = ?, image = ?, discount = ?, active = ? WHERE id = ?");
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['image'],
            $data['discount'],
            $data['active'],
            $id
        ]);
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM promotions WHERE id = ?");
        return $stmt->execute([$id]);
    }
}