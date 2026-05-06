<template>
  <div class="dashboard">
    <h1>Дашборд</h1>
    <p class="dashboard__greeting">Добро пожаловать в админ-панель Radar Extreme.</p>

    <div class="dashboard__stats">
      <div class="stat-card">
        <span class="stat-card__icon">📦</span>
        <div class="stat-card__body">
          <strong class="stat-card__value">{{ stats.products }}</strong>
          <span class="stat-card__label">Товаров</span>
        </div>
      </div>
      <div class="stat-card">
        <span class="stat-card__icon">🛒</span>
        <div class="stat-card__body">
          <strong class="stat-card__value">{{ stats.orders }}</strong>
          <span class="stat-card__label">Заказов</span>
        </div>
      </div>
      <div class="stat-card">
        <span class="stat-card__icon">📰</span>
        <div class="stat-card__body">
          <strong class="stat-card__value">{{ stats.news }}</strong>
          <span class="stat-card__label">Новостей</span>
        </div>
      </div>
      <div class="stat-card">
        <span class="stat-card__icon">🏷️</span>
        <div class="stat-card__body">
          <strong class="stat-card__value">{{ stats.promotions }}</strong>
          <span class="stat-card__label">Акций</span>
        </div>
      </div>
      <div class="stat-card">
        <span class="stat-card__icon">💬</span>
        <div class="stat-card__body">
          <strong class="stat-card__value">{{ stats.feedback }}</strong>
          <span class="stat-card__label">Сообщений</span>
        </div>
      </div>
    </div>

    <section class="dashboard__section">
      <h2>Последние заказы</h2>
      <table v-if="recentOrders.length > 0" class="dashboard__table">
        <thead>
          <tr>
            <th>#</th>
            <th>Клиент</th>
            <th>Email</th>
            <th>Сумма</th>
            <th>Статус</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in recentOrders" :key="order.id">
            <td>{{ order.id }}</td>
            <td>{{ order.name || '—' }}</td>
            <td>{{ order.email || '—' }}</td>
            <td>{{ formatPrice(order.total) }} ₽</td>
            <td>
              <span class="status-badge" :class="`status-badge--${order.status}`">
                {{ statusLabel(order.status) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
      <p v-else class="dashboard__empty">Нет заказов.</p>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useAdminDataStore } from '../stores/data'

const dataStore = useAdminDataStore()
const recentOrders = ref<any[]>([])

const stats = computed(() => ({
  products: dataStore.products.length,
  orders: dataStore.orders.length,
  news: dataStore.news.length,
  promotions: dataStore.promotions.length,
  feedback: dataStore.feedback.length,
}))

const formatPrice = (price: number) =>
  new Intl.NumberFormat('ru-RU').format(price)

const statusLabel = (s: string) =>
  ({
    pending: 'Ожидает',
    processing: 'В обработке',
    completed: 'Завершён',
    cancelled: 'Отменён',
  } as Record<string, string>)[s] || s

onMounted(async () => {
  await Promise.all([
    dataStore.fetchProducts(),
    dataStore.fetchOrders(),
    dataStore.fetchNews(),
    dataStore.fetchPromotions(),
    dataStore.fetchFeedback(),
  ])
  // последние 5 заказов
  recentOrders.value = dataStore.orders.slice(0, 5)
})
</script>

<style scoped>
.dashboard {
  padding: 20px;
}
.dashboard h1 {
  color: #ff6600;
  margin-bottom: 4px;
}
.dashboard__greeting {
  color: #888;
  margin-bottom: 24px;
}

/* ───── Статистика ───── */
.dashboard__stats {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 32px;
}
.stat-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #1a1a1a;
  border: 1px solid #333;
  border-radius: 10px;
  padding: 18px;
}
.stat-card__icon {
  font-size: 28px;
  flex-shrink: 0;
}
.stat-card__body {
  display: flex;
  flex-direction: column;
}
.stat-card__value {
  font-size: 24px;
  font-weight: 800;
  color: #fff;
  line-height: 1.1;
}
.stat-card__label {
  font-size: 13px;
  color: #888;
}

/* ───── Таблица ───── */
.dashboard__section h2 {
  color: #ff6600;
  margin-bottom: 14px;
}
.dashboard__table {
  width: 100%;
  border-collapse: collapse;
  background: #1a1a1a;
  border-radius: 8px;
  overflow: hidden;
}
.dashboard__table th,
.dashboard__table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #333;
  font-size: 14px;
}
.dashboard__table th {
  color: #888;
  font-weight: 700;
}
.dashboard__table td {
  color: #ccc;
}
.status-badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}
.status-badge--pending {
  background: rgba(255, 174, 0, 0.2);
  color: #ffae00;
}
.status-badge--processing {
  background: rgba(31, 142, 255, 0.2);
  color: #5fb1ff;
}
.status-badge--completed {
  background: rgba(46, 204, 113, 0.2);
  color: #2ecc71;
}
.status-badge--cancelled {
  background: rgba(231, 76, 60, 0.2);
  color: #e74c3c;
}
.dashboard__empty {
  color: #888;
  padding: 16px 0;
}
</style>