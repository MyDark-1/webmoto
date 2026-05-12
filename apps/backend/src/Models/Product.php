<?php

namespace App\Models;

use App\Config\Database;

class Product {
    public static function findAll(string $category = null, bool $all = false, int $page = 1, int $limit = 8, array $charFilters = []): array {
        $db = Database::getConnection();

        $params = [];
        $conditions = [];
        $joins = [];

        if (!$all) {
            $conditions[] = "p.status = 'active'";
        }
        if ($category) {
            $conditions[] = "c.slug = ?";
            $params[] = $category;
        }

        // Фильтрация по характеристикам
        $charIndex = 0;
        foreach ($charFilters as $charId => $value) {
            $alias = 'pc_f' . $charIndex;
            $joins[] = "JOIN product_characteristics $alias ON $alias.product_id = p.id AND $alias.characteristic_id = " . (int)$charId . " AND $alias.value = ?";
            $params[] = $value;
            $charIndex++;
        }

        $where = !empty($conditions) ? " WHERE " . implode(' AND ', $conditions) : '';
        $joinStr = !empty($joins) ? ' ' . implode(' ', $joins) : '';

        $baseSql = "FROM products p 
                    JOIN categories c ON p.category_id = c.id" . $joinStr . $where;

        // Если all=1 — возвращаем все товары без пагинации (для админки)
        if ($all) {
            $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug " . $baseSql . " ORDER BY p.created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll();
            $data = self::attachRelations($data);
            return [
                'data' => $data,
                'total' => count($data),
                'page' => 1,
                'limit' => count($data),
            ];
        }

        // Подсчёт общего количества
        $countSql = "SELECT COUNT(DISTINCT p.id) " . $baseSql;
        $stmt = $db->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        // Выборка с пагинацией
        $offset = ($page - 1) * $limit;
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug " . $baseSql . " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";

        $stmt = $db->prepare($sql);
        $stmt->execute(array_merge($params, [$limit, $offset]));
        $data = $stmt->fetchAll();
        $data = self::attachRelations($data);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                             FROM products p 
                             JOIN categories c ON p.category_id = c.id 
                             WHERE p.id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch() ?: null;
        if ($product) {
            $product['characteristics_list'] = ProductCharacteristic::findByProduct($id);
            $product['salons_list'] = ProductSalon::findByProduct($id);
        }
        return $product;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO products (category_id, title, description, price, image, status, stock_status, article) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['category_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['price'],
            $data['image'],
            $data['status'] ?? 'active',
            $data['stock_status'] ?? 'in_stock',
            $data['article'] ?? null
        ]);
        $productId = (int)$db->lastInsertId();

        // Сохраняем характеристики
        if (!empty($data['characteristics_values']) && is_array($data['characteristics_values'])) {
            ProductCharacteristic::saveForProduct($productId, $data['characteristics_values']);
        }

        // Сохраняем салоны
        if (!empty($data['salons'])) {
            ProductSalon::saveForProduct($productId, $data['salons']);
        }

        return $productId;
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE products SET category_id = ?, title = ?, description = ?, price = ?, image = ?, status = ?, stock_status = ?, article = ? WHERE id = ?");
        $result = $stmt->execute([
            $data['category_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['price'],
            $data['image'],
            $data['status'],
            $data['stock_status'] ?? 'in_stock',
            $data['article'] ?? null,
            $id
        ]);

        // Обновляем характеристики
        if (isset($data['characteristics_values']) && is_array($data['characteristics_values'])) {
            ProductCharacteristic::saveForProduct($id, $data['characteristics_values']);
        }

        // Обновляем салоны
        if (isset($data['salons'])) {
            ProductSalon::saveForProduct($id, $data['salons']);
        }

        return $result;
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private static function attachRelations(array $products): array {
        if (empty($products)) return [];

        $ids = array_column($products, 'id');
        $charsMap = ProductCharacteristic::findByProductIds($ids);
        $salonsMap = ProductSalon::findByProductIds($ids);

        foreach ($products as &$product) {
            $product['characteristics_list'] = $charsMap[$product['id']] ?? [];
            $product['salons_list'] = $salonsMap[$product['id']] ?? [];
        }

        return $products;
    }
}