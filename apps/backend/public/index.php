<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use App\Config\Router;
use App\Config\Cors;

// Загрузка переменных окружения
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Настройка CORS
Cors::setup();

// Подключение к базе данных
Database::connect();

// Маршрутизация
Router::dispatch();