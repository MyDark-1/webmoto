<?php

namespace App\Controllers;

use App\Models\News;
use App\Utils\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

class NewsController {
    public function index(): void {
        $news = News::findAll();
        Response::success($news);
    }

    public function show(int $id): void {
        $news = News::findById($id);

        if (!$news) {
            Response::error('News not found', 404);
            return;
        }

        Response::success($news);
    }

    public function store(): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['title']) || empty($data['content'])) {
            Response::error('Title and content are required', 400);
            return;
        }

        $newsId = News::create($data);
        $news = News::findById($newsId);

        Response::success($news);
    }

    public function update(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (!News::update($id, $data)) {
            Response::error('Failed to update news', 500);
            return;
        }

        $news = News::findById($id);
        Response::success($news);
    }

    public function delete(int $id): void {
        AuthMiddleware::handle();
        AdminMiddleware::handle();

        if (!News::delete($id)) {
            Response::error('Failed to delete news', 500);
            return;
        }

        Response::success(['message' => 'News deleted']);
    }
}