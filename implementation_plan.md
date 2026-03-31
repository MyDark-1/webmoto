# Implementation Plan

## [Overview]

Создание интернет-магазина мотосалона "Radar Extreme" с использованием монорепозитория Turborepo, Vue.js фронтендом, PHP бэкендом и MySQL базой данных. Проект включает в себя основной сайт с каталогом товаров (мотоциклы, квадроциклы, снегоходы, лодки, лодочные моторы), систему корзины и фейковой оплаты, админ-панель для управления контентом, а также разделы новостей и акций.

## [Types]

Пакет shared-types будет содержать все переиспользуемые типы для frontend и backend:

```typescript
// Пользователи
export enum UserRole {
  ADMIN = 'admin',
  USER = 'user'
}

export interface User {
  id: number;
  email: string;
  role: UserRole;
  created_at: string;
}

export interface AuthResponse {
  token: string;
  user: User;
}

// Категории товаров
export enum ProductCategory {
  MOTORCYCLES = 'motorcycles',
  ATV = 'atv',
  SNOWMOBILES = 'snowmobiles',
  BOATS = 'boats',
  OUTBOARD_MOTORS = 'outboard_motors'
}

export interface Category {
  id: number;
  name: string;
  slug: ProductCategory;
}

// Товары
export enum ProductStatus {
  ACTIVE = 'active',
  INACTIVE = 'inactive'
}

export interface Product {
  id: number;
  category_id: number;
  title: string;
  description: string;
  price: number;
  image: string;
  status: ProductStatus;
  created_at: string;
  updated_at: string;
}

// Заказы
export enum OrderStatus {
  PENDING = 'pending',
  PROCESSING = 'processing',
  COMPLETED = 'completed',
  CANCELLED = 'cancelled'
}

export interface Order {
  id: number;
  user_id: number;
  status: OrderStatus;
  total: number;
  created_at: string;
}

export interface OrderItem {
  id: number;
  order_id: number;
  product_id: number;
  quantity: number;
  price: number;
  product?: Product;
}

// Корзина
export interface CartItem {
  product: Product;
  quantity: number;
}

// Новости
export interface News {
  id: number;
  title: string;
  content: string;
  image: string;
  created_at: string;
}

// Акции
export interface Promotion {
  id: number;
  title: string;
  content: string;
  image: string;
  discount: number;
  active: boolean;
  created_at: string;
}

// Обратная связь
export interface Feedback {
  id: number;
  user_id: number;
  message: string;
  status: 'new' | 'read' | 'replied';
  created_at: string;
}

// API Response
export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  error?: string;
}

// Промокоды
export interface PromoCode {
  id: number;
  code: string;
  discount: number;
  active: boolean;
  expires_at: string;
}
```

## [Files]

### Новые файлы для создания:

**Корневые файлы монорепозитория:**
- `package.json` - корневой package.json с workspace конфигурацией
- `turbo.json` - конфигурация Turborepo
- `.gitignore` - игнорируемые файлы
- `README.md` - документация проекта

**Пакет shared-types:**
- `packages/shared-types/package.json`
- `packages/shared-types/tsconfig.json`
- `packages/shared-types/src/index.ts` - экспорт всех типов
- `packages/shared-types/src/user.types.ts`
- `packages/shared-types/src/product.types.ts`
- `packages/shared-types/src/order.types.ts`
- `packages/shared-types/src/news.types.ts`
- `packages/shared-types/src/api.types.ts`

**Пакет ui-components:**
- `packages/ui-components/package.json`
- `packages/ui-components/tsconfig.json`
- `packages/ui-components/src/index.ts`
- `packages/ui-components/src/components/Button.vue`
- `packages/ui-components/src/components/Input.vue`
- `packages/ui-components/src/components/Card.vue`
- `packages/ui-components/src/components/Modal.vue`
- `packages/ui-components/src/components/Loader.vue`
- `packages/ui-components/src/styles/variables.css`

**Frontend (Vue):**
- `apps/frontend/package.json`
- `apps/frontend/vite.config.ts`
- `apps/frontend/tsconfig.json`
- `apps/frontend/index.html`
- `apps/frontend/src/main.ts`
- `apps/frontend/src/App.vue`
- `apps/frontend/src/router/index.ts`
- `apps/frontend/src/store/index.ts`
- `apps/frontend/src/store/modules/auth.ts`
- `apps/frontend/src/store/modules/cart.ts`
- `apps/frontend/src/store/modules/products.ts`
- `apps/frontend/src/api/index.ts`
- `apps/frontend/src/api/auth.ts`
- `apps/frontend/src/api/products.ts`
- `apps/frontend/src/api/orders.ts`
- `apps/frontend/src/views/Home.vue`
- `apps/frontend/src/views/Products.vue`
- `apps/frontend/src/views/ProductDetail.vue`
- `apps/frontend/src/views/Cart.vue`
- `apps/frontend/src/views/Checkout.vue`
- `apps/frontend/src/views/News.vue`
- `apps/frontend/src/views/Promotions.vue`
- `apps/frontend/src/views/Login.vue`
- `apps/frontend/src/views/Register.vue`
- `apps/frontend/src/views/Profile.vue`
- `apps/frontend/src/components/Header.vue`
- `apps/frontend/src/components/Footer.vue`
- `apps/frontend/src/components/ProductCard.vue`
- `apps/frontend/src/components/ParticleBackground.vue`
- `apps/frontend/src/styles/main.css`
- `apps/frontend/src/styles/variables.css`

**Админ-панель:**
- `apps/admin/package.json`
- `apps/admin/vite.config.ts`
- `apps/admin/tsconfig.json`
- `apps/admin/index.html`
- `apps/admin/src/main.ts`
- `apps/admin/src/App.vue`
- `apps/admin/src/router/index.ts`
- `apps/admin/src/store/index.ts`
- `apps/admin/src/views/Dashboard.vue`
- `apps/admin/src/views/Products.vue`
- `apps/admin/src/views/ProductEdit.vue`
- `apps/admin/src/views/Orders.vue`
- `apps/admin/src/views/News.vue`
- `apps/admin/src/views/Promotions.vue`
- `apps/admin/src/views/Feedback.vue`
- `apps/admin/src/views/Login.vue`
- `apps/admin/src/components/Sidebar.vue`
- `apps/admin/src/components/Header.vue`

**Backend (PHP):**
- `apps/backend/composer.json`
- `apps/backend/public/index.php`
- `apps/backend/src/Config/Database.php`
- `apps/backend/src/Config/Router.php`
- `apps/backend/src/Config/Cors.php`
- `apps/backend/src/Controllers/AuthController.php`
- `apps/backend/src/Controllers/ProductController.php`
- `apps/backend/src/Controllers/CategoryController.php`
- `apps/backend/src/Controllers/OrderController.php`
- `apps/backend/src/Controllers/NewsController.php`
- `apps/backend/src/Controllers/PromotionController.php`
- `apps/backend/src/Controllers/FeedbackController.php`
- `apps/backend/src/Controllers/PromoCodeController.php`
- `apps/backend/src/Models/User.php`
- `apps/backend/src/Models/Product.php`
- `apps/backend/src/Models/Category.php`
- `apps/backend/src/Models/Order.php`
- `apps/backend/src/Models/OrderItem.php`
- `apps/backend/src/Models/News.php`
- `apps/backend/src/Models/Promotion.php`
- `apps/backend/src/Models/Feedback.php`
- `apps/backend/src/Models/PromoCode.php`
- `apps/backend/src/Middleware/AuthMiddleware.php`
- `apps/backend/src/Middleware/AdminMiddleware.php`
- `apps/backend/src/Services/AuthService.php`
- `apps/backend/src/Services/ProductService.php`
- `apps/backend/src/Services/OrderService.php`
- `apps/backend/src/Utils/Response.php`
- `apps/backend/src/Utils/Validator.php`
- `apps/backend/src/Utils/JWT.php`

**База данных:**
- `database/migrations/001_create_users_table.sql`
- `database/migrations/002_create_categories_table.sql`
- `database/migrations/003_create_products_table.sql`
- `database/migrations/004_create_orders_table.sql`
- `database/migrations/005_create_order_items_table.sql`
- `database/migrations/006_create_news_table.sql`
- `database/migrations/007_create_promotions_table.sql`
- `database/migrations/008_create_feedback_table.sql`
- `database/migrations/009_create_promo_codes_table.sql`
- `database/seeds/categories.sql`
- `database/seeds/admin_user.sql`
- `database/seeds/sample_products.sql`

## [Functions]

### Frontend функции:

**API сервисы:**
- `login(email: string, password: string): Promise<AuthResponse>` - авторизация
- `register(email: string, password: string): Promise<AuthResponse>` - регистрация
- `getProducts(category?: string): Promise<Product[]>` - получение списка товаров
- `getProduct(id: number): Promise<Product>` - получение товара по ID
- `getCategories(): Promise<Category[]>` - получение категорий
- `createOrder(items: CartItem[], promoCode?: string): Promise<Order>` - создание заказа
- `getOrders(): Promise<Order[]>` - получение заказов пользователя
- `getNews(): Promise<News[]>` - получение новостей
- `getPromotions(): Promise<Promotion[]>` - получение акций
- `sendFeedback(message: string): Promise<void>` - отправка обратной связи
- `validatePromoCode(code: string): Promise<PromoCode>` - проверка промокода

**Vuex Actions:**
- `auth/login({ email, password })` - вход в систему
- `auth/register({ email, password })` - регистрация
- `auth/logout()` - выход из системы
- `cart/addProduct(product)` - добавление в корзину
- `cart/removeProduct(productId)` - удаление из корзины
- `cart/updateQuantity(productId, quantity)` - обновление количества
- `cart/clear()` - очистка корзины
- `products/fetchProducts()` - загрузка товаров
- `products/fetchProduct(id)` - загрузка товара

### Backend функции:

**AuthController:**
- `login(Request $request): Response` - авторизация пользователя
- `register(Request $request): Response` - регистрация пользователя
- `me(Request $request): Response` - получение текущего пользователя

**ProductController:**
- `index(Request $request): Response` - список товаров
- `show(int $id): Response` - детали товара
- `store(Request $request): Response` - создание товара (admin)
- `update(int $id, Request $request): Response` - обновление товара (admin)
- `delete(int $id): Response` - удаление товара (admin)

**OrderController:**
- `index(Request $request): Response` - список заказов пользователя
- `store(Request $request): Response` - создание заказа
- `show(int $id): Response` - детали заказа
- `allOrders(Request $request): Response` - все заказы (admin)
- `updateStatus(int $id, Request $request): Response` - обновление статуса (admin)

**NewsController:**
- `index(): Response` - список новостей
- `show(int $id): Response` - детали новости
- `store(Request $request): Response` - создание новости (admin)
- `update(int $id, Request $request): Response` - обновление новости (admin)
- `delete(int $id): Response` - удаление новости (admin)

**PromotionController:**
- `index(): Response` - список акций
- `show(int $id): Response` - детали акции
- `store(Request $request): Response` - создание акции (admin)
- `update(int $id, Request $request): Response` - обновление акции (admin)
- `delete(int $id): Response` - удаление акции (admin)

**FeedbackController:**
- `store(Request $request): Response` - отправка обратной связи
- `index(Request $request): Response` - список обращений (admin)
- `updateStatus(int $id, Request $request): Response` - обновление статуса (admin)

**PromoCodeController:**
- `validate(Request $request): Response` - проверка промокода
- `index(): Response` - список промокодов (admin)
- `store(Request $request): Response` - создание промокода (admin)
- `delete(int $id): Response` - удаление промокода (admin)

## [Classes]

### Backend классы:

**Models (наследуют базовый Model):**
- `User` - модель пользователя
- `Product` - модель товара
- `Category` - модель категории
- `Order` - модель заказа
- `OrderItem` - модель позиции заказа
- `News` - модель новости
- `Promotion` - модели акции
- `Feedback` - модели обратной связи
- `PromoCode` - модели промокода

**Controllers (наследуют базовый Controller):**
- `AuthController` - контроллер аутентификации
- `ProductController` - контроллер товаров
- `CategoryController` - контроллер категорий
- `OrderController` - контроллер заказов
- `NewsController` - контроллер новостей
- `PromotionController` - контроллер акций
- `FeedbackController` - контроллер обратной связи
- `PromoCodeController` - контроллер промокодов

**Middleware:**
- `AuthMiddleware` - проверка JWT токена
- `AdminMiddleware` - проверка прав администратора

**Services:**
- `AuthService` - бизнес-логика аутентификации
- `ProductService` - бизнес-логика товаров
- `OrderService` - бизнес-логика заказов

**Utils:**
- `Response` - утилита для формирования ответов API
- `Validator` - утилита валидации данных
- `JWT` - утилита для работы с JWT токенами

## [Dependencies]

### Frontend зависимости:
- `vue` ^3.4.0
- `vue-router` ^4.2.0
- `vuex` ^4.1.0
- `axios` ^1.6.0
- `@vitejs/plugin-vue` ^5.0.0
- `vite` ^5.0.0
- `typescript` ^5.3.0

### Backend зависимости:
- `php` >= 8.2
- `firebase/php-jwt` ^6.10
- `vlucas/phpdotenv` ^5.6
- `ramsey/uuid` ^4.7

### Dev зависимости:
- `turbo` ^1.11.0
- `prettier` ^3.2.0
- `eslint` ^8.56.0

## [Testing]

### Подход к тестированию:

**Frontend:**
- Модульные тесты для Vuex store
- Интеграционные тесты для API сервисов
- E2E тесты для критических пользовательских сценариев

**Backend:**
- Модульные тесты для сервисов и моделей
- Интеграционные тесты для контроллеров
- API тесты для всех эндпоинтов

**Стратегия валидации:**
- Валидация на frontend и backend
- Проверка типов через TypeScript
- SQL инъекции предотвращаются через prepared statements
- XSS защита через экранирование вывода

## [Implementation Order]

1. **Настройка монорепозитория** - создание структуры Turborepo, корневых конфигураций
2. **Пакет shared-types** - создание всех типов и интерфейсов
3. **База данных** - создание миграций и seed данных
4. **Backend основа** - настройка PHP, подключение к БД, базовые классы
5. **Backend API** - реализация контроллеров и сервисов
6. **UI компоненты** - создание переиспользуемых компонентов
7. **Frontend основа** - настройка Vue, роутер, store
8. **Frontend страницы** - реализация всех страниц
9. **Админ-панель** - создание отдельного приложения для управления
10. **Анимация и стили** - добавление частиц и финальная стилизация
11. **Интеграция и тестирование** - связка всех компонентов, тестирование