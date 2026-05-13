-- Скрипт для добавления отсутствующих столбцов в таблицу users
-- Запустить: mysql -u root radar_extreme < fix_users_table.sql

USE radar_extreme;

-- Добавляем fullname, если его нет
ALTER TABLE users ADD COLUMN IF NOT EXISTS fullname VARCHAR(255) DEFAULT '';

-- Добавляем phone, если его нет
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(50) DEFAULT '';

-- Проверяем результат
DESCRIBE users;
