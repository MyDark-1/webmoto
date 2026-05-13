<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=radar_extreme;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

echo "Таблицы в БД radar_extreme:\n";
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) echo "  - $t\n";

echo "\nДанные:\n";
echo "  Категории: " . $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn() . "\n";
echo "  Товары: " . $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() . "\n";
echo "  Пользователи: " . $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() . "\n";
echo "  Салоны: " . $pdo->query('SELECT COUNT(*) FROM salons')->fetchColumn() . "\n";
echo "  Характеристики: " . $pdo->query('SELECT COUNT(*) FROM characteristics')->fetchColumn() . "\n";
echo "  ProductSalons: " . $pdo->query('SELECT COUNT(*) FROM product_salons')->fetchColumn() . "\n";