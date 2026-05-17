# Быстрый старт — без XAMPP

## Требования

- Node.js ≥ 18
- Docker (запускается автоматически)
- PHP ≥ 8.2

## Установка

```bash
# 1. Установить зависимости
npm install
cd apps/backend
composer install
cd ../..

# 2. Запустить всё (MySQL запустится автоматически)
npm run dev:all
```

## Первое использование

При первом запуске:
1. Скрипт создаст Docker-контейнер с MySQL
2. Подождёт готовности базы данных (~30 секунд)
3. Запустит PHP-бэкенд, frontend и admin

## Применение миграций

После первого запуска выполните (в новом терминале):

```bash
# Все миграции сразу
for f in database/migrations/*.sql; do
  docker exec radar-mysql mysql -u root -proot radar_extreme < "$f"
done

# Тестовые данные
docker exec radar-mysql mysql -u root -proot radar_extreme < database/seeds/categories.sql
docker exec radar-mysql mysql -u root -proot radar_extreme < database/seeds/admin_user.sql
docker exec radar-mysql mysql -u root -proot radar_extreme < database/seeds/sample_products.sql
```

## Адреса

- Frontend: http://localhost:3000
- Admin: http://localhost:3001
- API: http://localhost:8000/api

## Управление MySQL

```bash
npm run mysql:start   # Ручной запуск
npm run mysql:stop    # Остановка
docker logs radar-mysql  # Просмотр логов
```
