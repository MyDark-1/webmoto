<?php

namespace App\Controllers;

use App\Models\Characteristic;
use App\Models\ProductCharacteristic;
use App\Utils\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

class CharacteristicController {
    public function index(): void {
        $categoryId = $_GET['category_id'] ?? null;
        if ($categoryId) {
            $categoryId = (int)$categoryId;
            $withValues = ($_GET['with_values'] ?? '') === '1';
            if ($withValues) {
                $chars = Characteristic::findWithValuesByCategory($categoryId);
            } else {
                $chars = Characteristic::findByCategory($categoryId);
            }
        } else {
            $chars = Characteristic::findAllGrouped();
        }
        Response::success($chars);
    }

    public function store(): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name']) || empty($data['category_id'])) {
            Response::error('Missing required fields', 400);
            return;
        }
        $id = Characteristic::create($data);
        Response::success(['id' => $id]);
    }

    public function update(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();
        $data = json_decode(file_get_contents('php://input'), true);
        Characteristic::update($id, $data);
        Response::success(['message' => 'Updated']);
    }

    public function delete(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();
        Characteristic::delete($id);
        Response::success(['message' => 'Deleted']);
    }
}