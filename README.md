# Radar Extreme — интернет-магазин мотосалона

Монорепозиторий интернет-магазина «Radar Extreme»: клиентский сайт, админ-панель и REST API. Сборка через Turborepo + npm workspaces, фронтенд на Vue 3 + Pinia + Vite, бэкенд на PHP 8 + MySQL.

---

## Содержание

1. [Стек](#стек)
2. [Структура проекта](#структура-проекта)
3. [Требования](#требования)
4. [Установка](#установка)
5. [Настройка БД](#настройка-бд)
6. [Конфигурация окружения](#конфигурация-окружения)
7. [Запуск](#запуск)
8. [Доступные npm-скрипты](#доступные-npm-скрипты)
9. [Сборка для продакшена](#сборка-для-продакшена)
10. [Адреса сервисов](#адреса-сервисов)
11. [Тестовые учётные данные](#тестовые-учётные-данные)
12. [Архитектура frontend](#архитектура-frontend)
13. [API endpoints](#api-endpoints)
14. [Решение типичных проблем](#решение-типичных-проблем)

---

## Стек

| Слой       | Технологии                                                      |
| ---------- | --------------------------------------------------------------- |
| Frontend   | Vue 3.5, Vue Router 4, Pinia 2, Vite 6, TypeScript 5            |
| Admin      | Vue 3.5, Vue Router 4, Pinia 2, Vite 6, TypeScript 5            |
| Backend    | PHP 8.2+, MySQL 8, Firebase JWT, vlucas/phpdotenv, ramsey/uuid  |
| Tooling    | Turborepo 2, npm workspaces, Composer, Prettier, concurrently   |

## Структура проекта

```
www2/
├── apps/
│   ├── frontend/                 # Клиентский сайт (порт 3000)
│   │   └── src/
│   │       ├── components/       # Logo, AppHeader, AppFooter, ProductCard, Toaster
│   │       ├── views/            # Страницы (Home, Cart, Checkout, ...)
│   │       ├── router/           # Маршруты + auth-guard
│   │       ├── stores/           # Pinia: user, cart, catalog
│   │       ├── utils/            # api, format, notify
│   │       ├── assets/styles/    # main.css (общие стили)
│   │       ├── App.vue
│   │       └── main.ts
│   ├── admin/                    # Админ-панель (порт 3001)
│   │   └── src/                  # Аналогичная структура
│   └── backend/                  # PHP API (порт 8000)
│       ├── public/index.php      # Точка входа
│       ├── src/
│       │   ├── Config/
│       │   ├── Controllers/
│       │   ├── Middleware/
│       │   ├── Models/
│       │   └── Utils/
│       └── composer.json
├── packages/
│   ├── shared-types/             # Общие TS-типы
│   └── ui-components/            # Переиспользуемые компоненты
├── database/
│   ├── migrations/               # SQL-миграции (001..009)
│   └── seeds/                    # Тестовые данные
├── package.json                  # workspace root
├── turbo.json                    # Turborepo pipeline
└── README.md
```

## Требования

- **Node.js** ≥ 18 (рекомендуется 20+)
- **npm** ≥ 9
- **PHP** ≥ 8.2 с расширениями `pdo_mysql`, `mbstring`, `openssl`
- **MySQL** ≥ 8.0 (или совместимая MariaDB) — через XAMPP
- **Composer** ≥ 2

Проверить установленные версии:

```bash
node -v
npm -v
php -v
composer --version
mysql --version
```

## Установка

```bash
# Клонировать репозиторий
git clone <repository-url> www2
cd www2

# Установить JS-зависимости (для всех workspaces сразу)
npm install

# Установить PHP-зависимости
cd apps/backend
composer install
cd ../..
```

## Настройка БД

1. Запустите MySQL через XAMPP Control Panel.

2. Создайте базу данных:

   ```sql
   CREATE DATABASE radar_extreme CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Примените миграции (из корня репозитория):

   ```bash
   for f in database/migrations/*.sql; do
     mysql -u root -p radar_extreme < "$f"
   done
   ```

   Или вручную, по одной:

   ```bash
   mysql -u root -p radar_extreme < database/migrations/001_create_users_table.sql
   mysql -u root -p radar_extreme < database/migrations/002_create_categories_table.sql
   mysql -u root -p radar_extreme < database/migrations/003_create_products_table.sql
   mysql -u root -p radar_extreme < database/migrations/004_create_orders_table.sql
   mysql -u root -p radar_extreme < database/migrations/005_create_order_items_table.sql
   mysql -u root -p radar_extreme < database/migrations/006_create_news_table.sql
   mysql -u root -p radar_extreme < database/migrations/007_create_promotions_table.sql
   mysql -u root -p radar_extreme < database/migrations/008_create_feedback_table.sql
   mysql -u root -p radar_extreme < database/migrations/009_create_promo_codes_table.sql
   mysql -u root -p radar_extreme < database/migrations/010_update_products_add_specs.sql
   mysql -u root -p radar_extreme < database/migrations/011_update_products_chars_salons.sql
   ```

   **Важно:** Если у вас уже есть таблица `users` без столбцов `fullname` и `phone`, выполните:

   ```bash
   mysql -u root -p radar_extreme < database/fix_users_table.sql
   ```

   Или пересоздайте таблицу (удалит все данные!):

   ```bash
   mysql -u root -p radar_extreme < database/recreate_users_table.sql
   ```

4. Загрузите тестовые данные:

   ```bash
   mysql -u root -p radar_extreme < database/seeds/categories.sql
   mysql -u root -p radar_extreme < database/seeds/admin_user.sql
   mysql -u root -p radar_extreme < database/seeds/sample_products.sql
   ```

При первом запуске:
1. Создастся Docker-контейнер с MySQL 8.0
2. Данные будут храниться в папке `database/mysql-data/`
3. После готовности MySQL скрипт автоматически продолжит запуск backend

### Применение миграций

После первого запуска выполните миграции вручную (из корня репозитория):

```bash
# Все миграции сразу
for f in database/migrations/*.sql; do
  docker exec radar-mysql mysql -u root -proot radar_extreme < "$f"
done

# Или по одной:
docker exec radar-mysql mysql -u root -proot radar_extreme < database/migrations/001_create_users_table.sql
# ... и так далее для каждой миграции
```

**Важно:** Если у вас уже есть таблица `users` без столбцов `fullname` и `phone`, выполните:

```bash
docker exec radar-mysql mysql -u root -proot radar_extreme < database/fix_users_table.sql
```

Или пересоздайте таблицу (удалит все данные!):

```bash
docker exec radar-mysql mysql -u root -proot radar_extreme < database/recreate_users_table.sql
```

### Загрузка тестовых данных

```bash
docker exec radar-mysql mysql -u root -proot radar_extreme < database/seeds/categories.sql
docker exec radar-mysql mysql -u root -proot radar_extreme < database/seeds/admin_user.sql
docker exec radar-mysql mysql -u root -proot radar_extreme < database/seeds/sample_products.sql
```

### Управление MySQL контейнером

```bash
# Ручной запуск (если нужно)
npm run mysql:start

# Остановка (удаляет контейнер)
npm run mysql:stop

# Посмотреть логи
docker logs radar-mysql

# Подключиться к MySQL из командной строки
docker exec -it radar-mysql mysql -u root -proot radar_extreme
```

## Конфигурация окружения

Создайте/отредактируйте файл `apps/backend/.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=radar_extreme
DB_USER=root
DB_PASSWORD=

JWT_SECRET=замените-на-длинный-секрет
JWT_EXPIRATION=86400

CORS_ORIGIN=http://localhost:3000
```

> **Важно:** `JWT_SECRET` обязательно поменяйте перед деплоем.

Frontend и admin обращаются к API через прокси Vite (`/api → http://localhost:8000`), правки переменных не требуют.

## Запуск

### Запуск всего стека одной командой

Из корня репозитория:

```bash
npm run start
```

Эквивалентно `npm run dev:all` — поднимает PHP-бэкенд, frontend и admin параллельно через `concurrently` с цветным префиксом в логах.

### Раздельный запуск

```bash
# Backend (PHP встроенный сервер)
cd apps/backend
composer dev
# или: php -S localhost:8000 -t public

# Frontend
cd apps/frontend
npm run dev

# Admin
cd apps/admin
npm run dev
```

## Доступные npm-скрипты

Из корня репозитория:

| Команда                | Что делает                                                   |
| ---------------------- | ------------------------------------------------------------ |
| `npm run start`        | Запускает backend + frontend + admin параллельно             |
| `npm run dev:all`      | Алиас `start`                                                |
| `npm run dev`          | Параллельный запуск через Turborepo (`turbo run dev`)        |
| `npm run build`        | Билд frontend и admin (`turbo run build`)                    |
| `npm run lint`         | Линтер для всех приложений                                   |
| `npm run format`       | Prettier по `**/*.{ts,tsx,vue,md}`                           |

В каждом приложении (`apps/frontend`, `apps/admin`):

| Команда           | Что делает                          |
| ----------------- | ----------------------------------- |
| `npm run dev`     | Vite dev-сервер                     |
| `npm run build`   | `vue-tsc` + `vite build`            |
| `npm run preview` | Просмотр продакшен-сборки           |

В `apps/backend`:

| Команда         | Что делает                                |
| --------------- | ----------------------------------------- |
| `composer dev`  | `php -S localhost:8000 -t public`         |
| `composer install` | Установка PHP-зависимостей             |

## Сборка для продакшена

```bash
# Сборка обоих frontend-приложений
npm run build

# Артефакты:
#   apps/frontend/dist/
#   apps/admin/dist/
```

Раздавайте `dist/` любым статическим веб-сервером (nginx, caddy). Для backend используйте PHP-FPM + nginx; entry point — `apps/backend/public/index.php`.

## Адреса сервисов

| Сервис   | URL                       |
| -------- | ------------------------- |
| Frontend | http://localhost:3000     |
| Admin    | http://localhost:3001     |
| API      | http://localhost:8000/api |

## Тестовые учётные данные

**Администратор** (после загрузки `database/seeds/admin_user.sql`):

- **Email**: `admin@radarextreme.ru`
- **Пароль**: `password`

Войдите через **Admin** (http://localhost:3001/login). Обычные пользователи регистрируются на frontend через `/register`.

## Архитектура frontend

Оба Vue-приложения построены по одной схеме:

- **`router/index.ts`** — маршруты + `beforeEach` (auth-guard, редирект гостей/авторизованных) + `afterEach` (document title) + `scrollBehavior`.
- **`stores/`** — Pinia composition stores: `user` (auth + token в `localStorage`), `cart` (с авто-синхронизацией в `localStorage`), `catalog` (товары/категории).
- **`utils/api.ts`** — `apiFetch<T>(url, options)` автоматически подставляет `Authorization: Bearer ...` и сериализует `json: ...`.
- **`utils/notify.ts`** — реактивный массив toast-уведомлений + хелперы `notifySuccess` / `notifyError`. Отображается через `<Toaster />` в `App.vue`.
- **`utils/format.ts`** — `formatPrice`, `formatDate`, `formatDateTime`.
- **`assets/styles/main.css`** — CSS-переменные дизайн-системы (`--color-bg`, `--color-accent`, `--radius-md`...) и базовые классы `.btn`, `.input`, `.tag`, `.container`, `.section`.
- **`components/`** — `Logo`, `AppHeader` (с бургер-меню), `AppFooter`, `ProductCard`, `Toaster`.

## API endpoints

Все ответы возвращаются в формате `{ success: boolean, data?: T, error?: string }`.

### Аутентификация

| Метод | Путь                  | Доступ | Описание                |
| ----- | --------------------- | ------ | ----------------------- |
| POST  | `/api/auth/login`     | public | Логин                   |
| POST  | `/api/auth/register`  | public | Регистрация             |
| GET   | `/api/auth/me`        | user   | Текущий пользователь    |

### Каталог

| Метод  | Путь                  | Доступ | Описание           |
| ------ | --------------------- | ------ | ------------------ |
| GET    | `/api/products`       | public | Список товаров     |
| GET    | `/api/products/:id`   | public | Карточка товара    |
| POST   | `/api/products`       | admin  | Создание           |
| PUT    | `/api/products/:id`   | admin  | Обновление         |
| DELETE | `/api/products/:id`   | admin  | Удаление           |
| GET    | `/api/categories`     | public | Категории          |

### Заказы

| Метод | Путь                       | Доступ | Описание                     |
| ----- | -------------------------- | ------ | ---------------------------- |
| GET   | `/api/orders`              | user   | Заказы текущего пользователя |
| POST  | `/api/orders`              | user   | Оформление заказа            |
| GET   | `/api/orders/all`          | admin  | Все заказы                   |
| PUT   | `/api/orders/:id/status`   | admin  | Смена статуса                |

### Контент

| Метод  | Путь                    | Доступ | Описание         |
| ------ | ----------------------- | ------ | ---------------- |
| GET    | `/api/news`             | public | Список новостей  |
| POST   | `/api/news`             | admin  | Создание         |
| PUT    | `/api/news/:id`         | admin  | Обновление       |
| DELETE | `/api/news/:id`         | admin  | Удаление         |
| GET    | `/api/promotions`       | public | Список акций     |
| POST   | `/api/promotions`       | admin  | Создание         |
| PUT    | `/api/promotions/:id`   | admin  | Обновление       |
| DELETE | `/api/promotions/:id`   | admin  | Удаление         |

### Обратная связь и промокоды

| Метод | Путь                              | Доступ | Описание              |
| ----- | --------------------------------- | ------ | --------------------- |
| POST  | `/api/feedback`                   | user   | Отправить сообщение   |
| GET   | `/api/feedback`                   | admin  | Все сообщения         |
| PUT   | `/api/feedback/:id/status`        | admin  | Смена статуса         |
| POST  | `/api/promo-codes/validate`       | public | Валидация промокода   |
| GET   | `/api/promo-codes`                | admin  | Список промокодов     |
| POST  | `/api/promo-codes`                | admin  | Создание              |
| DELETE | `/api/promo-codes/:id`           | admin  | Удаление              |

## Решение типичных проблем

**`Error: ENOENT ... vendor/autoload.php`**
Не выполнен `composer install` в `apps/backend`.

**`SQLSTATE[HY000] [2002]`** — нет соединения с MySQL.
Проверьте, что MySQL запущен через XAMPP и параметры в `apps/backend/.env` корректны.

**`SQLSTATE[42S22]: Column not found: 1054 Unknown column 'fullname'`**
Таблица `users` устарела. Исправьте с помощью PHP-скрипта:

```bash
cd apps/backend
php -r "
require 'vendor/autoload.php';
\$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
\$dotenv->load();
\App\Config\Database::connect();
\$db = \App\Config\Database::getConnection();
\$columns = \$db->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_COLUMN);
foreach (['fullname', 'phone'] as \$col) {
    if (!in_array(\$col, \$columns)) {
        \$db->exec(\"ALTER TABLE users ADD COLUMN \$col VARCHAR(255) DEFAULT ''\");
        echo \"Добавлен столбец \$col\n\";
    }
}
echo 'Готово!\n';
"
```

**`SQLSTATE[42S22]: Column not found` для таблицы orders**
Таблица `orders` устарела. Исправьте:

```bash
cd apps/backend
php -r "
require 'vendor/autoload.php';
\$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
\$dotenv->load();
\App\Config\Database::connect();
\$db = \App\Config\Database::getConnection();
\$columns = \$db->query('SHOW COLUMNS FROM orders')->fetchAll(PDO::FETCH_COLUMN);
\$cols = [
    ['name' => 'name', 'type' => \"VARCHAR(255) DEFAULT ''\"],
    ['name' => 'phone', 'type' => \"VARCHAR(50) DEFAULT ''\"],
    ['name' => 'email', 'type' => \"VARCHAR(255) DEFAULT ''\"],
    ['name' => 'wishes', 'type' => 'TEXT DEFAULT NULL'],
    ['name' => 'address', 'type' => 'TEXT DEFAULT NULL']
];
foreach (\$cols as \$col) {
    if (!in_array(\$col['name'], \$columns)) {
        \$db->exec(\"ALTER TABLE orders ADD COLUMN {\$col['name']} {\$col['type']}\");
        echo \"Добавлен столбец {\$col['name']}\n\";
    }
}
echo 'Готово!\n';
"
```

Или пересоздайте таблицу (удалит все данные!):

```bash
mysql -u root -p radar_extreme < database/recreate_users_table.sql
```

**`Invalid credentials` при входе после регистрации**
Это следствие проблемы с колонками таблицы. Выполните шаги выше. Также проверьте логи PHP в консоли сервера — там может быть детальная информация.

**CORS-ошибка в браузере**
Frontend ходит на API через Vite-прокси — проверьте, что используете `http://localhost:3000` (а не file://) и `CORS_ORIGIN` в `.env` совпадает.

**Порт 3000/3001/8000 занят**
Закройте занявший процесс или поменяйте порт в `apps/{frontend,admin}/vite.config.ts` / при запуске `php -S localhost:XXXX -t public`.

**Редирект на `/login` сразу после входа**
Проверьте, что в браузерном `localStorage` появился ключ `token` (frontend) или `admin_token` (admin). Если нет — backend не вернул `success: true` с токеном, смотрите ответ `/api/auth/login` в DevTools.

**`vue-tsc` падает на `.vue` импортах**
Должен присутствовать `apps/{frontend,admin}/src/shims-vue.d.ts` с декларацией `*.vue` модуля.

---

## Лицензия

MIT
