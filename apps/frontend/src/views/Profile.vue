<template>
  <div class="profile">
    <h1>Профиль</h1>
    
    <div v-if="user" class="profile-content">
      <div class="profile-info">
        <h2>Информация о пользователе</h2>
        <p><strong>Email:</strong> {{ user.email }}</p>
        <p><strong>Роль:</strong> {{ user.role }}</p>
      </div>

      <div class="orders">
        <h2>Мои заказы</h2>
        <div v-if="orders.length === 0" class="no-orders">
          <p>У вас пока нет заказов</p>
        </div>
        <div v-else class="orders-list">
          <div v-for="order in orders" :key="order.id" class="order-card">
            <div class="order-header">
              <span class="order-id">Заказ #{{ order.id }}</span>
              <span class="order-status" :class="order.status">{{ getStatusText(order.status) }}</span>
            </div>
            <div class="order-items">
              <div v-for="item in order.items" :key="item.id" class="order-item">
                <span>{{ item.product_title }} x {{ item.quantity }}</span>
                <span>{{ formatPrice(item.price * item.quantity) }} ₽</span>
              </div>
            </div>
            <div class="order-total">
              <strong>Итого: {{ formatPrice(order.total) }} ₽</strong>
            </div>
            <div class="order-date">
              {{ formatDate(order.created_at) }}
            </div>
          </div>
        </div>
      </div>

      <div class="feedback-section">
        <h2>Обратная связь</h2>
        <form @submit.prevent="sendFeedback" class="feedback-form">
          <textarea v-model="feedbackMessage" placeholder="Ваше сообщение" required></textarea>
          <button type="submit" class="btn">Отправить</button>
        </form>
      </div>
    </div>
    
    <div v-else>
      <p>Пожалуйста, войдите в систему</p>
      <router-link to="/login" class="btn">Войти</router-link>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'

export default {
  name: 'Profile',
  setup() {
    const store = useStore()
    const orders = ref([])
    const feedbackMessage = ref('')

    const user = computed(() => store.state.user)

    const formatPrice = (price) => {
      return new Intl.NumberFormat('ru-RU').format(price)
    }

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('ru-RU')
    }

    const getStatusText = (status) => {
      const statuses = {
        pending: 'Ожидает',
        processing: 'В обработке',
        completed: 'Завершен',
        cancelled: 'Отменен'
      }
      return statuses[status] || status
    }

    const sendFeedback = async () => {
      if (!feedbackMessage.value) return
      
      const response = await fetch('/api/feedback', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${store.state.token}`
        },
        body: JSON.stringify({ message: feedbackMessage.value })
      })
      
      const data = await response.json()
      if (data.success) {
        alert('Сообщение отправлено!')
        feedbackMessage.value = ''
      } else {
        alert('Ошибка при отправке сообщения')
      }
    }

    onMounted(async () => {
      if (store.state.token) {
        const response = await fetch('/api/orders', {
          headers: {
            'Authorization': `Bearer ${store.state.token}`
          }
        })
        const data = await response.json()
        if (data.success) {
          orders.value = data.data
        }
      }
    })

    return {
      user,
      orders,
      feedbackMessage,
      formatPrice,
      formatDate,
      getStatusText,
      sendFeedback
    }
  }
}
</script>

<style scoped>
.profile {
  padding: 20px;
}

.profile h1 {
  color: #ff6600;
  margin-bottom: 20px;
}

.profile-content {
  display: grid;
  gap: 30px;
}

.profile-info {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
}

.profile-info h2 {
  color: #ff6600;
  margin-bottom: 15px;
}

.orders {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
}

.orders h2 {
  color: #ff6600;
  margin-bottom: 15px;
}

.order-card {
  background: #0a0a0a;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 15px;
}

.order-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.order-id {
  font-weight: bold;
  color: #ff6600;
}

.order-status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
}

.order-status.pending {
  background: #ffa500;
  color: #000;
}

.order-status.processing {
  background: #007bff;
  color: #fff;
}

.order-status.completed {
  background: #28a745;
  color: #fff;
}

.order-status.cancelled {
  background: #dc3545;
  color: #fff;
}

.order-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 5px;
  color: #888;
}

.order-total {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid #333;
  color: #ff6600;
}

.order-date {
  color: #888;
  font-size: 14px;
  margin-top: 10px;
}

.feedback-section {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
}

.feedback-section h2 {
  color: #ff6600;
  margin-bottom: 15px;
}

.feedback-form textarea {
  width: 100%;
  height: 100px;
  padding: 10px;
  border: 1px solid #333;
  border-radius: 4px;
  background: #0a0a0a;
  color: #fff;
  margin-bottom: 10px;
  resize: vertical;
}

.btn {
  background: #ff6600;
  color: #fff;
  border: none;
  padding: 12px 24px;
  border-radius: 4px;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
}

.no-orders {
  color: #888;
  text-align: center;
  padding: 20px;
}
</style>