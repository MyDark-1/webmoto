<?php
require_once __DIR__ . '/apps/backend/vendor/autoload.php';

use App\Config\Database;

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

Database::connect();
$db = Database::getConnection();

$migrationFile = __DIR__ . '/database/migrations/011_update_products_chars_salons.sql';
$sql = file_get_contents($migrationFile);

// Разделяем на отдельные запросы (каждый заканчивается ;)
$parts = explode(';', $sql);

foreach ($parts as $part) {
    $part = trim($part);
    if (empty($part)) continue;

    // Пропускаем комментарии
    $lines = explode("\n", $part);
    $cleanLines = array_filter($lines, function($l) {
        $t = trim($l);
        return $t !== '' && strpos($t, '--') !== 0;
    });

    $cleanStmt = implode("\n", $cleanLines);
    $cleanStmt = trim($cleanStmt);
    if (empty($cleanStmt)) continue;

    try {
        $db->exec($cleanStmt . ';');
        echo "OK: " . mb_substr(str_replace("\n", ' ', $cleanStmt), 0, 80) . "\n";
    } catch (\Exception $e) {
        echo "ERR: " . $e->getMessage() . "\n";
    }
}

echo "\nMigration complete!\n";