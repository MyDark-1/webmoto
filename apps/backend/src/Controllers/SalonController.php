<?php

namespace App\Controllers;

use App\Models\Salon;
use App\Utils\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

class SalonController {
    public function index(): void {
        $salons = Salon::findAll();
        Response::success($salons);
    }

    public function store(): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name']) || empty($data['city'])) {
            Response::error('Missing required fields', 400);
            return;
        }
        $id = Salon::create($data);
        Response::success(['id' => $id]);
    }

    public function update(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();
        $data = json_decode(file_get_contents('php://input'), true);
        Salon::update($id, $data);
        Response::success(['message' => 'Updated']);
    }

    public function delete(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();
        Salon::delete($id);
        Response::success(['message' => 'Deleted']);
    }
}