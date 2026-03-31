<?php

namespace App\Models;

use App\Config\Database;

class Category {
    public static function findAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll();
    }

    public static function findBySlug(string $slug): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }
}