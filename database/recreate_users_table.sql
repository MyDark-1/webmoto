-- Полное пересоздание таблицы users
-- ВНИМАНИЕ: Удаляет все существующие данные!

USE radar_extreme;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') DEFAULT 'user',
  fullname VARCHAR(255) DEFAULT '',
  phone VARCHAR(50) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Добавляем тестового админа
INSERT INTO users (email, password, role) VALUES
('admin@radarextreme.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Пароль: password

SELECT 'Таблица users пересоздана успешно!' AS status;
