<?php

namespace App\Controllers;

use App\Models\Category;
use App\Utils\Response;

class CategoryController {
    public function index(): void {
        $categories = Category::findAll();
        Response::success($categories);
    }
}