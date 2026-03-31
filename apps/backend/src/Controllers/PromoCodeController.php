<?php

namespace App\Controllers;

use App\Models\PromoCode;
use App\Utils\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

class PromoCodeController {
    public function validate(): void {
        AuthMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['code'])) {
            Response::error('Promo code is required', 400);
            return;
        }

        $promoCode = PromoCode::findByCode($data['code']);

        if (!$promoCode) {
            Response::error('Invalid or expired promo code', 400);
            return;
        }

        Response::success($promoCode);
    }

    public function index(): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $promoCodes = PromoCode::findAll();
        Response::success($promoCodes);
    }

    public function store(): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['code']) || empty($data['discount']) || empty($data['expires_at'])) {
            Response::error('Code, discount and expires_at are required', 400);
            return;
        }

        $promoCodeId = PromoCode::create($data);
        $promoCode = PromoCode::findByCode($data['code']);

        Response::success($promoCode);
    }

    public function delete(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        if (!PromoCode::delete($id)) {
            Response::error('Failed to delete promo code', 500);
            return;
        }

        Response::success(['message' => 'Promo code deleted']);
    }
}