<?php

namespace App\Models;

use App\Config\Database;

class ProductSalon {
    public static function findByProduct(int $productId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT ps.*, s.name as salon_name, s.city, s.address, s.phone, s.email
                             FROM product_salons ps
                             JOIN salons s ON ps.salon_id = s.id
                             WHERE ps.product_id = ?
                             ORDER BY s.sort ASC, s.id ASC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public static function findByProductIds(array $productIds): array {
        if (empty($productIds)) return [];
        $db = Database::getConnection();
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = $db->prepare("SELECT ps.*, s.name as salon_name, s.city, s.address, s.phone, s.email
                             FROM product_salons ps
                             JOIN salons s ON ps.salon_id = s.id
                             WHERE ps.product_id IN ($placeholders)
                             ORDER BY s.sort ASC, s.id ASC");
        $stmt->execute($productIds);
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['product_id']][] = $row;
        }
        return $map;
    }

    public static function saveForProduct(int $productId, array $salonIds): void {
        $db = Database::getConnection();

        // Удаляем старые
        $stmt = $db->prepare("DELETE FROM product_salons WHERE product_id = ?");
        $stmt->execute([$productId]);

        // Вставляем новые
        if (!empty($salonIds)) {
            $stmt = $db->prepare("INSERT INTO product_salons (product_id, salon_id, stock_status) VALUES (?, ?, 'in_stock')");
            foreach ($salonIds as $salonId) {
                $salonId = (int)$salonId;
                if ($salonId > 0) {
                    $stmt->execute([$productId, $salonId]);
                }
            }
        }
    }
}