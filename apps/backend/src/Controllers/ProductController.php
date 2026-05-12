<?php

namespace App\Controllers;

use App\Models\Product;
use App\Utils\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

class ProductController {
    public function index(): void {
        $category = $_GET['category'] ?? null;
        $all = ($_GET['all'] ?? '') === '1';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = max(1, min(100, (int)($_GET['limit'] ?? 8)));

        // Фильтрация по характеристикам (?chars[characteristic_id]=value)
        $charFilters = [];
        if (isset($_GET['chars']) && is_array($_GET['chars'])) {
            foreach ($_GET['chars'] as $charId => $value) {
                $charId = (int)$charId;
                $value = trim($value);
                if ($charId > 0 && $value !== '') {
                    $charFilters[$charId] = $value;
                }
            }
        }

        $result = Product::findAll($category, $all, $page, $limit, $charFilters);

        Response::success([
            'items' => $result['data'],
            'total' => $result['total'],
            'page' => $result['page'],
            'limit' => $result['limit'],
            'pages' => (int)ceil($result['total'] / $result['limit']),
        ]);
    }

    public function show(int $id): void {
        $product = Product::findById($id);

        if (!$product) {
            Response::error('Product not found', 404);
            return;
        }

        Response::success($product);
    }

    public function store(): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['title']) || empty($data['category_id']) || empty($data['price'])) {
            Response::error('Missing required fields', 400);
            return;
        }

        $productId = Product::create($data);
        $product = Product::findById($productId);

        Response::success($product);
    }

    public function update(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (!Product::update($id, $data)) {
            Response::error('Failed to update product', 500);
            return;
        }

        $product = Product::findById($id);
        Response::success($product);
    }

    public function delete(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        if (!Product::delete($id)) {
            Response::error('Failed to delete product', 500);
            return;
        }

        Response::success(['message' => 'Product deleted']);
    }
}