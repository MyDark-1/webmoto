<?php

namespace App\Models;

use App\Config\Database;

class ProductCharacteristic {
    public static function findByProduct(int $productId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT pc.*, ch.name as characteristic_name, ch.category_id
                             FROM product_characteristics pc
                             JOIN characteristics ch ON pc.characteristic_id = ch.id
                             WHERE pc.product_id = ?
                             ORDER BY ch.sort ASC, ch.id ASC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public static function findByProductIds(array $productIds): array {
        if (empty($productIds)) return [];
        $db = Database::getConnection();
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $db->prepare("SELECT pc.*, ch.name as characteristic_name, ch.category_id
                             FROM product_characteristics pc
                             JOIN characteristics ch ON pc.characteristic_id = ch.id
                             WHERE pc.product_id IN ($placeholders)
                             ORDER BY ch.sort ASC, ch.id ASC");
        $stmt->execute($productIds);
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['product_id']][] = $row;
        }
        return $map;
    }

    public static function saveForProduct(int $productId, array $values): void {
        $db = Database::getConnection();

        // Удаляем старые
        $stmt = $db->prepare("DELETE FROM product_characteristics WHERE product_id = ?");
        $stmt->execute([$productId]);

        // Вставляем новые
        if (!empty($values)) {
            $stmt = $db->prepare("INSERT INTO product_characteristics (product_id, characteristic_id, value) VALUES (?, ?, ?)");
            foreach ($values as $item) {
                if (!empty($item['characteristic_id']) && isset($item['value'])) {
                    $stmt->execute([$productId, (int)$item['characteristic_id'], $item['value']]);
                }
            }
        }
    }
}