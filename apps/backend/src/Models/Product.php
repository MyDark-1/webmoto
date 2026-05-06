<?php

namespace App\Models;

use App\Config\Database;

class Product {
    public static function findAll(string $category = null, bool $all = false): array {
        $db = Database::getConnection();
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM products p 
                JOIN categories c ON p.category_id = c.id";
        $params = [];

        $conditions = [];
        if (!$all) {
            $conditions[] = "p.status = 'active'";
        }
        if ($category) {
            $conditions[] = "c.slug = ?";
            $params[] = $category;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
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
        $stmt = $db->prepare("INSERT INTO products (category_id, title, description, characteristics, specifications, price, image, status, stock_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['category_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['characteristics'] ?? null,
            $data['specifications'] ?? null,
            $data['price'],
            $data['image'],
            $data['status'] ?? 'active',
            $data['stock_status'] ?? 'in_stock'
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE products SET category_id = ?, title = ?, description = ?, characteristics = ?, specifications = ?, price = ?, image = ?, status = ?, stock_status = ? WHERE id = ?");
        return $stmt->execute([
            $data['category_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['characteristics'] ?? null,
            $data['specifications'] ?? null,
            $data['price'],
            $data['image'],
            $data['status'],
            $data['stock_status'] ?? 'in_stock',
            $id
        ]);
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}