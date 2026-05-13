<?php
/**
 * Полный скрипт для загрузки базы данных:
 * 1. Создает базу данных (если не существует)
 * 2. Выполняет все миграции по порядку
 * 3. Загружает все сиды
 */

// Загружаем .env вручную
$envFile = __DIR__ . '/apps/backend/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbname = $_ENV['DB_NAME'] ?? 'radar_extreme';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

echo "=== Загрузка базы данных: $dbname ===\n\n";

// 1. Подключаемся без БД, чтобы создать её
echo "1. Подключение к MySQL... ";
try {
    $pdo = new PDO(
        "mysql:host=$host;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "OK\n";
} catch (PDOException $e) {
    die("ОШИБКА подключения: " . $e->getMessage() . "\n");
}

// 2. Создаем БД
echo "2. Создание базы данных '$dbname'... ";
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "OK\n";
} catch (PDOException $e) {
    die("ОШИБКА создания БД: " . $e->getMessage() . "\n");
}

// 3. Переключаемся на БД
$pdo->exec("USE `$dbname`");

// 4. Выполняем миграции
echo "\n3. Выполнение миграций:\n";
$migrationsDir = __DIR__ . '/database/migrations';
$migrationFiles = glob($migrationsDir . '/*.sql');
sort($migrationFiles);

foreach ($migrationFiles as $file) {
    $filename = basename($file);
    echo "   - $filename... ";
    
    $sql = file_get_contents($file);
    
    // Разделяем на отдельные запросы
    $statements = explode(';', $sql);
    $success = true;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;
        
        // Пропускаем комментарии
        $lines = explode("\n", $statement);
        $cleanLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || strpos($trimmed, '--') === 0) continue;
            $cleanLines[] = $line;
        }
        
        $cleanStmt = trim(implode("\n", $cleanLines));
        if (empty($cleanStmt)) continue;
        
        try {
            $pdo->exec($cleanStmt);
        } catch (PDOException $e) {
            // Пропускаем ошибки "already exists" для CREATE TABLE IF NOT EXISTS
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo " (уже существует)";
            } else {
                echo "ОШИБКА: " . $e->getMessage() . "\n";
                $success = false;
            }
        }
    }
    
    if ($success) {
        echo "OK\n";
    }
}

// 5. Выполняем сиды (в правильном порядке - сначала категории, потом товары)
echo "\n4. Загрузка данных (сиды):\n";
$seedsDir = __DIR__ . '/database/seeds';
$orderedSeeds = [
    'categories.sql',
    'admin_user.sql',
    'sample_products.sql',
    '100_products.sql',
];

foreach ($orderedSeeds as $seedName) {
    $file = $seedsDir . '/' . $seedName;
    if (!file_exists($file)) {
        echo "   - $seedName... ФАЙЛ НЕ НАЙДЕН\n";
        continue;
    }
    $filename = basename($file);
    echo "   - $filename... ";
    
    $sql = file_get_contents($file);
    
    // Разделяем на отдельные запросы
    $statements = explode(';', $sql);
    $success = true;
    $rowCount = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;
        
        // Пропускаем комментарии
        $lines = explode("\n", $statement);
        $cleanLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || strpos($trimmed, '--') === 0) continue;
            $cleanLines[] = $line;
        }
        
        $cleanStmt = trim(implode("\n", $cleanLines));
        if (empty($cleanStmt)) continue;
        
        try {
            $count = $pdo->exec($cleanStmt);
            if ($count !== false) {
                $rowCount += $count;
            }
        } catch (PDOException $e) {
            // Пропускаем дубликаты при повторном запуске
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                echo " (уже загружено)";
            } else {
                echo "ОШИБКА: " . $e->getMessage() . "\n";
                $success = false;
            }
        }
    }
    
    if ($success) {
        echo "OK (добавлено записей: $rowCount)\n";
    }
}

echo "\n=== База данных '$dbname' успешно загружена! ===\n";