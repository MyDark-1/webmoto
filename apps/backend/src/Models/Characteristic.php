<?php

namespace App\Models;

use App\Config\Database;

class Characteristic {
    public static function findByCategory(int $categoryId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM characteristics WHERE category_id = ? ORDER BY sort ASC, id ASC");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public static function findAllGrouped(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT ch.*, c.name as category_name FROM characteristics ch JOIN categories c ON ch.category_id = c.id ORDER BY ch.category_id, ch.sort, ch.id");
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO characteristics (category_id, name, sort) VALUES (?, ?, ?)");
        $stmt->execute([$data['category_id'], $data['name'], $data['sort'] ?? 0]);
        return (int)$db->lastInsertId();
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM characteristics WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE characteristics SET category_id = ?, name = ?, sort = ? WHERE id = ?");
        return $stmt->execute([$data['category_id'], $data['name'], $data['sort'] ?? 0, $id]);
    }

    /**
     * Возвращает характеристики категории с возможными значениями
     */
    public static function findWithValuesByCategory(int $categoryId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT ch.*, GROUP_CONCAT(DISTINCT pc.value ORDER BY pc.value ASC SEPARATOR '||') as all_values
            FROM characteristics ch
            LEFT JOIN product_characteristics pc ON pc.characteristic_id = ch.id
            LEFT JOIN products p ON p.id = pc.product_id AND p.status = 'active'
            WHERE ch.category_id = ?
            GROUP BY ch.id
            ORDER BY ch.sort ASC, ch.id ASC
        ");
        $stmt->execute([$categoryId]);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $row['values'] = $row['all_values'] ? explode('||', $row['all_values']) : [];
            unset($row['all_values']);
        }

        return $rows;
    }
}
