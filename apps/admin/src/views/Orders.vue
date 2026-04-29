<template>
  <div class="orders-admin">
    <h1>Управление заказами</h1>
    
    <div class="orders-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Пользователь</th>
            <th>Сумма</th>
            <th>Статус</th>
            <th>Дата</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id">
            <td>{{ order.id }}</td>
            <td>{{ order.email }}</td>
            <td>{{ formatPrice(order.total) }} ₽</td>
            <td>
              <span class="status" :class="order.status">{{ getStatusText(order.status) }}</span>
            </td>
            <td>{{ formatDate(order.created_at) }}</td>
            <td>
              <button class="view-btn" @click="openOrder(order)">👁️ Детали</button>
              <select @change="updateStatus(order.id, $event.target.value)" :value="order.status">
                <option value="pending">Ожидает</option>
                <option value="processing">В обработке</option>
                <option value="completed">Завершен</option>
                <option value="cancelled">Отменен</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Модальное окно деталей заказа -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Заказ №{{ selectedOrder?.id }}</h2>
          <button class="modal-close" @click="closeModal">×</button>
        </div>
        <div class="modal-body" v-if="selectedOrder">
          
          <div class="order-section">
            <h3>📋 Основная информация</h3>
            <div class="order-row">
              <span class="order-label">Статус:</span>
              <span class="order-value status" :class="selectedOrder.status">{{ getStatusText(selectedOrder.status) }}</span>
            </div>
            <div class="order-row">
              <span class="order-label">Дата создания:</span>
              <span class="order-value">{{ formatDate(selectedOrder.created_at) }} {{ new Date(selectedOrder.created_at).toLocaleTimeString('ru-RU') }}</span>
            </div>
          </div>

          <div class="order-section">
            <h3>👤 Контактные данные</h3>
            <div class="order-row">
              <span class="order-label">Email:</span>
              <span class="order-value">{{ selectedOrder.email }}</span>
            </div>
            <div class="order-row">
              <span class="order-label">Телефон:</span>
              <span class="order-value">{{ selectedOrder.phone || 'Не указан' }}</span>
            </div>
            <div class="order-row">
              <span class="order-label">Имя:</span>
              <span class="order-value">{{ selectedOrder.name || 'Не указано' }}</span>
            </div>
          </div>

          <div class="order-section">
            <h3>🛒 Состав заказа</h3>
            <div class="order-items">
              <div class="order-item" v-for="item in selectedOrder.items || []" :key="item.id">
                <span>{{ item.name }} × {{ item.quantity }}</span>
                <span>{{ formatPrice(item.price * item.quantity) }} ₽</span>
              </div>
            </div>
            <div class="total-row">
              <span>Итого:</span>
              <span>{{ formatPrice(selectedOrder.total) }} ₽</span>
            </div>
          </div>

          <div class="order-section" v-if="selectedOrder.wishes">
            <h3>📝 Пожелания клиента</h3>
            <div class="wishes-box">
              {{ selectedOrder.wishes }}
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useAdminUserStore } from '../stores/user'
import { useAdminDataStore } from '../stores/data'
import { notifyError, notifySuccess } from '../utils/notify'

const userStore = useAdminUserStore()
const dataStore = useAdminDataStore()
const orders = computed(() => dataStore.orders)

const selectedOrder = ref(null)
const showModal = ref(false)

const formatPrice = (price) => new Intl.NumberFormat('ru-RU').format(price)
const formatDate = (d) => new Date(d).toLocaleDateString('ru-RU')

const getStatusText = (status) => {
  const statuses = {
    pending: 'Ожидает',
    processing: 'В обработке',
    completed: 'Завершен',
    cancelled: 'Отменен'
  }
  return statuses[status] || status
}

const updateStatus = async (orderId, status) => {
  const response = await fetch(`/api/orders/${orderId}/status`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userStore.token}`
    },
    body: JSON.stringify({ status })
  })
  const data = await response.json()
  if (data.success) {
    notifySuccess('Статус обновлён')
    dataStore.fetchOrders()
  } else {
    notifyError(data.error || 'Ошибка')
  }
}

const openOrder = (order) => {
  selectedOrder.value = order
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedOrder.value = null
}

onMounted(() => dataStore.fetchOrders())
</script>

<style scoped>
.orders-admin {
  padding: 20px;
}

.orders-admin h1 {
  color: #ff6600;
  margin-bottom: 20px;
}

.orders-table {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: #1a1a1a;
  border-radius: 8px;
  overflow: hidden;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #333;
}

th {
  color: #888;
  font-weight: bold;
}

.status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
}

.status.pending {
  background: #ffa500;
  color: #000;
}

.status.processing {
  background: #007bff;
  color: #fff;
}

.status.completed {
  background: #28a745;
  color: #fff;
}

.status.cancelled {
  background: #dc3545;
  color: #fff;
}

select {
  padding: 5px;
  border-radius: 4px;
  border: 1px solid #333;
  background: #0a0a0a;
  color: #fff;
  margin-left: 8px;
}

.view-btn {
  background: #ff6600;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  color: white;
  cursor: pointer;
  font-size: 13px;
  transition: opacity 0.2s;
}

.view-btn:hover {
  opacity: 0.8;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: #1a1a1a;
  border-radius: 12px;
  max-width: 700px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #333;
}

.modal-header h2 {
  margin: 0;
  color: #ff6600;
}

.modal-close {
  background: transparent;
  border: none;
  font-size: 24px;
  color: #888;
  cursor: pointer;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  color: #fff;
}

.modal-body {
  padding: 20px;
}

.order-section {
  margin-bottom: 20px;
}

.order-section h3 {
  color: #ff6600;
  margin-top: 0;
  margin-bottom: 12px;
  font-size: 16px;
}

.order-row {
  display: grid;
  grid-template-columns: 150px 1fr;
  padding: 8px 0;
  border-bottom: 1px solid #2a2a2a;
}

.order-label {
  color: #888;
}

.order-value {
  color: #fff;
}

.wishes-box {
  background: #0f0f0f;
  border: 1px solid #333;
  border-radius: 6px;
  padding: 12px;
  margin-top: 8px;
  color: #fff;
  line-height: 1.5;
}

.order-items {
  background: #0f0f0f;
  border-radius: 6px;
  overflow: hidden;
}

.order-item {
  padding: 12px;
  border-bottom: 1px solid #222;
  display: flex;
  justify-content: space-between;
}

.order-item:last-child {
  border-bottom: none;
}

.total-row {
  display: flex;
  justify-content: space-between;
  font-weight: bold;
  font-size: 18px;
  padding-top: 12px;
  margin-top: 12px;
  border-top: 2px solid #333;
}
</style>