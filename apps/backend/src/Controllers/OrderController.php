<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Utils\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

class OrderController {
    public function index(): void {
        AuthMiddleware::handle();

        $authUser = $_SERVER['auth_user'];
        $orders = Order::findByUserId($authUser->user_id);

        foreach ($orders as &$order) {
            $order['items'] = OrderItem::findByOrderId($order['id']);
        }

        Response::success($orders);
    }

    public function show(int $id): void {
        AuthMiddleware::handle();

        $order = Order::findById($id);

        if (!$order) {
            Response::error('Order not found', 404);
            return;
        }

        $order['items'] = OrderItem::findByOrderId($id);
        Response::success($order);
    }

    public function store(): void {
        AuthMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);
        $authUser = $_SERVER['auth_user'];

        if (empty($data['items']) || !is_array($data['items'])) {
            Response::error('Items are required', 400);
            return;
        }

        $total = 0;
        foreach ($data['items'] as $item) {
            $product = Product::findById($item['product_id']);
            if (!$product) {
                Response::error('Product not found: ' . $item['product_id'], 404);
                return;
            }
            $total += $product['price'] * $item['quantity'];
        }

        $orderId = Order::create($authUser->user_id, $total);

        foreach ($data['items'] as $item) {
            $product = Product::findById($item['product_id']);
            OrderItem::create($orderId, $item['product_id'], $item['quantity'], $product['price']);
        }

        $order = Order::findById($orderId);
        $order['items'] = OrderItem::findByOrderId($orderId);

        Response::success($order);
    }

    public function allOrders(): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $orders = Order::findAll();

        foreach ($orders as &$order) {
            $order['items'] = OrderItem::findByOrderId($order['id']);
        }

        Response::success($orders);
    }

    public function updateStatus(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['status'])) {
            Response::error('Status is required', 400);
            return;
        }

        if (!Order::updateStatus($id, $data['status'])) {
            Response::error('Failed to update order status', 500);
            return;
        }

        $order = Order::findById($id);
        Response::success($order);
    }
}