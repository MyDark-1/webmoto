<?php

namespace App\Controllers;

use App\Models\Product;
use App\Utils\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

class ProductController {
    public function index(): void {
        $category = $_GET['category'] ?? null;
        $products = Product::findAll($category);
        Response::success($products);
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