<?php

namespace App\Models;

use App\Config\Database;

class Salon {
    public static function findAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM salons ORDER BY sort ASC, id ASC");
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM salons WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO salons (name, city, address, phone, email, image, brands, sort) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['city'],
            $data['address'],
            $data['phone'],
            $data['email'] ?? null,
            $data['image'] ?? null,
            $data['brands'] ?? null,
            $data['sort'] ?? 0
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE salons SET name = ?, city = ?, address = ?, phone = ?, email = ?, image = ?, brands = ?, sort = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            $data['city'],
            $data['address'],
            $data['phone'],
            $data['email'] ?? null,
            $data['image'] ?? null,
            $data['brands'] ?? null,
            $data['sort'] ?? 0,
            $id
        ]);
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM salons WHERE id = ?");
        return $stmt->execute([$id]);
    }
}