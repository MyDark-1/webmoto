<?php

namespace App\Controllers;

use App\Models\Feedback;
use App\Utils\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

class FeedbackController {
    public function store(): void {
        AuthMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);
        $authUser = $_SERVER['auth_user'];

        if (empty($data['message'])) {
            Response::error('Message is required', 400);
            return;
        }

        $feedbackId = Feedback::create($authUser->user_id, $data['message']);

        Response::success(['id' => $feedbackId]);
    }

    public function index(): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $feedback = Feedback::findAll();
        Response::success($feedback);
    }

    public function updateStatus(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['status'])) {
            Response::error('Status is required', 400);
            return;
        }

        if (!Feedback::updateStatus($id, $data['status'])) {
            Response::error('Failed to update feedback status', 500);
            return;
        }

        Response::success(['message' => 'Status updated']);
    }
}