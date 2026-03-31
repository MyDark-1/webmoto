<?php

namespace App\Controllers;

use App\Models\Promotion;
use App\Utils\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

class PromotionController {
    public function index(): void {
        $promotions = Promotion::findAll();
        Response::success($promotions);
    }

    public function show(int $id): void {
        $promotion = Promotion::findById($id);

        if (!$promotion) {
            Response::error('Promotion not found', 404);
            return;
        }

        Response::success($promotion);
    }

    public function store(): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['title']) || empty($data['content']) || empty($data['discount'])) {
            Response::error('Title, content and discount are required', 400);
            return;
        }

        $promotionId = Promotion::create($data);
        $promotion = Promotion::findById($promotionId);

        Response::success($promotion);
    }

    public function update(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (!Promotion::update($id, $data)) {
            Response::error('Failed to update promotion', 500);
            return;
        }

        $promotion = Promotion::findById($id);
        Response::success($promotion);
    }

    public function delete(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        if (!Promotion::delete($id)) {
            Response::error('Failed to delete promotion', 500);
            return;
        }

        Response::success(['message' => 'Promotion deleted']);
    }
}