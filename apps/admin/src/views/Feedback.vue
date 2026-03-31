<template>
  <div class="feedback-admin">
    <h1>Обратная связь</h1>
    
    <div class="feedback-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Пользователь</th>
            <th>Сообщение</th>
            <th>Статус</th>
            <th>Дата</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in feedback" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.email }}</td>
            <td>{{ item.message }}</td>
            <td>
              <span class="status" :class="item.status">{{ getStatusText(item.status) }}</span>
            </td>
            <td>{{ formatDate(item.created_at) }}</td>
            <td>
              <select @change="updateStatus(item.id, $event.target.value)" :value="item.status">
                <option value="new">Новое</option>
                <option value="read">Прочитано</option>
                <option value="replied">Отвечено</option>
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
  name: 'FeedbackAdmin',
  setup() {
    const store = useStore()

    const feedback = computed(() => store.state.feedback)

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('ru-RU')
    }

    const getStatusText = (status) => {
      const statuses = {
        new: 'Новое',
        read: 'Прочитано',
        replied: 'Отвечено'
      }
      return statuses[status] || status
    }

    const updateStatus = async (id, status) => {
      const response = await fetch(`/api/feedback/${id}/status`, {
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
        store.dispatch('fetchFeedback')
      } else {
        alert('Ошибка: ' + data.error)
      }
    }

    onMounted(() => {
      store.dispatch('fetchFeedback')
    })

    return {
      feedback,
      formatDate,
      getStatusText,
      updateStatus
    }
  }
}
</script>

<style scoped>
.feedback-admin {
  padding: 20px;
}

.feedback-admin h1 {
  color: #ff6600;
  margin-bottom: 20px;
}

.feedback-table {
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

.status.new {
  background: #ffa500;
  color: #000;
}

.status.read {
  background: #007bff;
  color: #fff;
}

.status.replied {
  background: #28a745;
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