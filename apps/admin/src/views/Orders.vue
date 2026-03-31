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
  </div>
</template>

<script>
import { computed, onMounted } from 'vue'
import { useStore } from 'vuex'

export default {
  name: 'OrdersAdmin',
  setup() {
    const store = useStore()

    const orders = computed(() => store.state.orders)

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

    const updateStatus = async (orderId, status) => {
      const response = await fetch(`/api/orders/${orderId}/status`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${store.state.token}`
        },
        body: JSON.stringify({ status })
      })
      
      const data = await response.json()
      if (data.success) {
        alert('Статус обновлен!')
        store.dispatch('fetchOrders')
      } else {
        alert('Ошибка: ' + data.error)
      }
    }

    onMounted(() => {
      store.dispatch('fetchOrders')
    })

    return {
      orders,
      formatPrice,
      formatDate,
      getStatusText,
      updateStatus
    }
  }
}
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
}
</style>