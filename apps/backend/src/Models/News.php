<?php

namespace App\Models;

use App\Config\Database;

class News {
    public static function findAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM news ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO news (title, content, image) VALUES (?, ?, ?)");
        $stmt->execute([$data['title'], $data['content'], $data['image']]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE news SET title = ?, content = ?, image = ? WHERE id = ?");
        return $stmt->execute([$data['title'], $data['content'], $data['image'], $id]);
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM news WHERE id = ?");
        return $stmt->execute([$id]);
    }
}