# Radar Extreme - Интернет-магазин мотосалона

## Описание

Интернет-магазин мотосалона "Radar Extreme" с использованием монорепозитория Turborepo, Vue.js фронтендом, PHP бэкендом и MySQL базой данных.

## Структура проекта

```
radar-extreme/
├── apps/
│   ├── frontend/          # Vue.js фронтенд для клиентов
│   ├── admin/             # Vue.js админ-панель
│   └── backend/           # PHP API бэкенд
├── packages/
│   ├── shared-types/      # Общие TypeScript типы
│   └── ui-components/     # Переиспользуемые UI компоненты
├── database/
│   ├── migrations/        # SQL миграции
│   └── seeds/             # Тестовые данные
├── package.json           # Корневой package.json
├── turbo.json             # Конфигурация Turborepo
└── README.md              # Этот файл
```

## Установка и запуск

### Требования

- Node.js 18+
- PHP 8.2+
- MySQL 8.0+
- Composer

### Установка зависимостей

```bash
# Установка npm зависимостей
npm install

# Установка PHP зависимостей
cd apps/backend
composer install
```

### Настройка базы данных

1. Создайте базу данных MySQL:
```sql
CREATE DATABASE radar_extreme;
```

2. Выполните миграции:
```bash
cd database/migrations
mysql -u root -p radar_extreme < 001_create_users_table.sql
mysql -u root -p radar_extreme < 002_create_categories_table.sql
mysql -u root -p radar_extreme < 003_create_products_table.sql
mysql -u root -p radar_extreme < 004_create_orders_table.sql
mysql -u root -p radar_extreme < 005_create_order_items_table.sql
mysql -u root -p radar_extreme < 006_create_news_table.sql
mysql -u root -p radar_extreme < 007_create_promotions_table.sql
mysql -u root -p radar_extreme < 008_create_feedback_table.sql
mysql -u root -p radar_extreme < 009_create_promo_codes_table.sql
```

3. Загрузите тестовые данные:
```bash
cd database/seeds
mysql -u root -p radar_extreme < categories.sql
mysql -u root -p radar_extreme < admin_user.sql
mysql -u root -p radar_extreme < sample_products.sql
```

### Настройка окружения

1. Отредактируйте файл `apps/backend/.env`:
```
DB_HOST=localhost
DB_NAME=radar_extreme
DB_USER=root
DB_PASSWORD=your_password
JWT_SECRET=your-secret-key-here
```

### Запуск приложения

```bash
# Запуск всех приложений
npm run dev

# Или запуск по отдельности:
# Backend (PHP)
cd apps/backend
php -S localhost:8000 -t public

# Frontend (Vue)
cd apps/frontend
npm run dev

# Admin Panel (Vue)
cd apps/admin
npm run dev
```

## Функционал

### Клиентская часть (Frontend)
- Каталог товаров с фильтрацией по категориям
- Детальная страница товара
- Корзина покупок
- Оформление заказа с промокодами
- Новости и акции
- Регистрация и авторизация
- Личный кабинет с историей заказов
- Обратная связь

### Админ-панель
- Дашборд со статистикой
- Управление товарами (CRUD)
- Управление заказами (изменение статусов)
- Управление новостями (CRUD)
- Управление акциями (CRUD)
- Просмотр обратной связи

### API (Backend)
- RESTful API для всех операций
- JWT аутентификация
- Разделение прав доступа (admin/user)
- Валидация данных
- CORS поддержка

## Технологии

### Frontend & Admin
- Vue.js 3
- Vue Router
- Vuex
- Axios
- Vite
- TypeScript

### Backend
- PHP 8.2+
- MySQL 8.0+
- Firebase JWT
- PHPDotenv

### Инструменты
- Turborepo (монорепозиторий)
- npm workspaces
- Composer

## API Endpoints

### Аутентификация
- `POST /api/auth/login` - Вход
- `POST /api/auth/register` - Регистрация
- `GET /api/auth/me` - Получение текущего пользователя

### Товары
- `GET /api/products` - Список товаров
- `GET /api/products/:id` - Детали товара
- `POST /api/products` - Создание товара (admin)
- `PUT /api/products/:id` - Обновление товара (admin)
- `DELETE /api/products/:id` - Удаление товара (admin)

### Категории
- `GET /api/categories` - Список категорий

### Заказы
- `GET /api/orders` - Заказы пользователя
- `POST /api/orders` - Создание заказа
- `GET /api/orders/all` - Все заказы (admin)
- `PUT /api/orders/:id/status` - Обновление статуса (admin)

### Новости
- `GET /api/news` - Список новостей
- `POST /api/news` - Создание новости (admin)
- `PUT /api/news/:id` - Обновление новости (admin)
- `DELETE /api/news/:id` - Удаление новости (admin)

### Акции
- `GET /api/promotions` - Список акций
- `POST /api/promotions` - Создание акции (admin)
- `PUT /api/promotions/:id` - Обновление акции (admin)
- `DELETE /api/promotions/:id` - Удаление акции (admin)

### Обратная связь
- `POST /api/feedback` - Отправка сообщения
- `GET /api/feedback` - Список сообщений (admin)
- `PUT /api/feedback/:id/status` - Обновление статуса (admin)

### Промокоды
- `POST /api/promo-codes/validate` - Проверка промокода
- `GET /api/promo-codes` - Список промокодов (admin)
- `POST /api/promo-codes` - Создание промокода (admin)
- `DELETE /api/promo-codes/:id` - Удаление промокода (admin)

## Тестовые данные

### Администратор
- Email: admin@radarextreme.ru
- Пароль: password

### Тестовые товары
- Yamaha YZF-R1 (мотоцикл)
- Kawasaki Ninja ZX-10R (мотоцикл)
- Polaris Sportsman 570 (квадроцикл)
- Yamaha VK Professional II (снегоход)
- Казанка-5 (лодка)
- Yamaha F150 (лодочный мотор)

## Лицензия

MIT License