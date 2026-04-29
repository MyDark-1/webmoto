<?php

namespace App\Models;

use App\Config\Database;

class Product {
    public static function findAll(string $category = null): array {
        $db = Database::getConnection();
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active'";
        $params = [];

        if ($category) {
            $sql .= " AND c.slug = ?";
            $params[] = $category;
        }

        $sql .= " ORDER BY p.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                             FROM products p 
                             JOIN categories c ON p.category_id = c.id 
                             WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO products (category_id, title, description, characteristics, price, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['category_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['characteristics'] ?? null,
            $data['price'],
            $data['image'],
            $data['status'] ?? 'active'
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE products SET category_id = ?, title = ?, description = ?, characteristics = ?, price = ?, image = ?, status = ? WHERE id = ?");
        return $stmt->execute([
            $data['category_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['characteristics'] ?? null,
            $data['price'],
            $data['image'],
            $data['status'],
            $id
        ]);
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}