<?php

namespace App\Config;

use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Controllers\CategoryController;
use App\Controllers\OrderController;
use App\Controllers\NewsController;
use App\Controllers\PromotionController;
use App\Controllers\FeedbackController;
use App\Controllers\PromoCodeController;

class Router {
    public static function dispatch(): void {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Удаляем префикс /api из URI
        $uri = str_replace('/api', '', $uri);

        // Маршруты аутентификации
        if ($uri === '/auth/login' && $method === 'POST') {
            (new AuthController())->login();
        } elseif ($uri === '/auth/register' && $method === 'POST') {
            (new AuthController())->register();
        } elseif ($uri === '/auth/me' && $method === 'GET') {
            (new AuthController())->me();
        }
        // Маршруты товаров
        elseif ($uri === '/products' && $method === 'GET') {
            (new ProductController())->index();
        } elseif (preg_match('#^/products/(\d+)$#', $uri, $matches) && $method === 'GET') {
            (new ProductController())->show((int)$matches[1]);
        } elseif ($uri === '/products' && $method === 'POST') {
            (new ProductController())->store();
        } elseif (preg_match('#^/products/(\d+)$#', $uri, $matches) && $method === 'PUT') {
            (new ProductController())->update((int)$matches[1]);
        } elseif (preg_match('#^/products/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
            (new ProductController())->delete((int)$matches[1]);
        }
        // Маршруты категорий
        elseif ($uri === '/categories' && $method === 'GET') {
            (new CategoryController())->index();
        }
        // Маршруты заказов
        elseif ($uri === '/orders' && $method === 'GET') {
            (new OrderController())->index();
        } elseif ($uri === '/orders' && $method === 'POST') {
            (new OrderController())->store();
        } elseif (preg_match('#^/orders/(\d+)$#', $uri, $matches) && $method === 'GET') {
            (new OrderController())->show((int)$matches[1]);
        } elseif ($uri === '/orders/all' && $method === 'GET') {
            (new OrderController())->allOrders();
        } elseif (preg_match('#^/orders/(\d+)/status$#', $uri, $matches) && $method === 'PUT') {
            (new OrderController())->updateStatus((int)$matches[1]);
        }
        // Маршруты новостей
        elseif ($uri === '/news' && $method === 'GET') {
            (new NewsController())->index();
        } elseif (preg_match('#^/news/(\d+)$#', $uri, $matches) && $method === 'GET') {
            (new NewsController())->show((int)$matches[1]);
        } elseif ($uri === '/news' && $method === 'POST') {
            (new NewsController())->store();
        } elseif (preg_match('#^/news/(\d+)$#', $uri, $matches) && $method === 'PUT') {
            (new NewsController())->update((int)$matches[1]);
        } elseif (preg_match('#^/news/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
            (new NewsController())->delete((int)$matches[1]);
        }
        // Маршруты акций
        elseif ($uri === '/promotions' && $method === 'GET') {
            (new PromotionController())->index();
        } elseif (preg_match('#^/promotions/(\d+)$#', $uri, $matches) && $method === 'GET') {
            (new PromotionController())->show((int)$matches[1]);
        } elseif ($uri === '/promotions' && $method === 'POST') {
            (new PromotionController())->store();
        } elseif (preg_match('#^/promotions/(\d+)$#', $uri, $matches) && $method === 'PUT') {
            (new PromotionController())->update((int)$matches[1]);
        } elseif (preg_match('#^/promotions/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
            (new PromotionController())->delete((int)$matches[1]);
        }
        // Маршруты обратной связи
        elseif ($uri === '/feedback' && $method === 'POST') {
            (new FeedbackController())->store();
        } elseif ($uri === '/feedback' && $method === 'GET') {
            (new FeedbackController())->index();
        } elseif (preg_match('#^/feedback/(\d+)/status$#', $uri, $matches) && $method === 'PUT') {
            (new FeedbackController())->updateStatus((int)$matches[1]);
        }
        // Маршруты промокодов
        elseif ($uri === '/promo-codes/validate' && $method === 'POST') {
            (new PromoCodeController())->validate();
        } elseif ($uri === '/promo-codes' && $method === 'GET') {
            (new PromoCodeController())->index();
        } elseif ($uri === '/promo-codes' && $method === 'POST') {
            (new PromoCodeController())->store();
        } elseif (preg_match('#^/promo-codes/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
            (new PromoCodeController())->delete((int)$matches[1]);
        }
        // 404
        else {
            http_response_code(404);
            echo json_encode(['error' => 'Route not found']);
        }
    }
}