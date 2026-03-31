<?php

namespace App\Models;

use App\Config\Database;

class OrderItem {
    public static function create(int $orderId, int $productId, int $quantity, float $price): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $productId, $quantity, $price]);
        return (int)$db->lastInsertId();
    }

    public static function findByOrderId(int $orderId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT oi.*, p.title as product_title, p.image as product_image 
                             FROM order_items oi 
                             JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}