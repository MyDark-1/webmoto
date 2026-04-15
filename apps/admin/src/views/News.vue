<template>
  <div class="news-admin">
    <h1>Управление новостями</h1>
    
    <div class="actions">
      <button @click="showAddForm = true" class="btn">Добавить новость</button>
    </div>

    <div v-if="showAddForm" class="modal">
      <div class="modal-content">
        <h2>{{ editingNews ? 'Редактировать новость' : 'Добавить новость' }}</h2>
        <form @submit.prevent="saveNews">
          <div class="form-group">
            <label>Заголовок</label>
            <input type="text" v-model="form.title" required>
          </div>
          <div class="form-group">
            <label>Содержание</label>
            <textarea v-model="form.content" required></textarea>
          </div>
          <div class="form-group">
            <label>Изображение (URL)</label>
            <input type="text" v-model="form.image">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn">Сохранить</button>
            <button type="button" @click="cancelEdit" class="btn btn-secondary">Отмена</button>
          </div>
        </form>
      </div>
    </div>

    <div class="news-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Изображение</th>
            <th>Заголовок</th>
            <th>Дата</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="newsItem in news" :key="newsItem.id">
            <td>{{ newsItem.id }}</td>
            <td>
              <img v-if="newsItem.image" :src="newsItem.image" :alt="newsItem.title" class="news-image">
            </td>
            <td>{{ newsItem.title }}</td>
            <td>{{ formatDate(newsItem.created_at) }}</td>
            <td>
              <button @click="editNews(newsItem)" class="btn-small">Редактировать</button>
              <button @click="deleteNews(newsItem.id)" class="btn-small btn-danger">Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAdminUserStore } from '../stores/user'
import { useAdminDataStore } from '../stores/data'
import { notifyError, notifySuccess } from '../utils/notify'

const userStore = useAdminUserStore()
const dataStore = useAdminDataStore()
const showAddForm = ref(false)
const editingNews = ref(null)

const form = ref({ title: '', content: '', image: '' })

const news = computed(() => dataStore.news)

const formatDate = (d) => new Date(d).toLocaleDateString('ru-RU')

const saveNews = async () => {
  const url = editingNews.value
    ? `/api/news/${editingNews.value.id}`
    : '/api/news'
  const method = editingNews.value ? 'PUT' : 'POST'
  const response = await fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${userStore.token}`
    },
    body: JSON.stringify(form.value)
  })
  const data = await response.json()
  if (data.success) {
    notifySuccess(editingNews.value ? 'Новость обновлена' : 'Новость добавлена')
    cancelEdit()
    dataStore.fetchNews()
  } else {
    notifyError(data.error || 'Ошибка')
  }
}

const editNews = (item) => {
  editingNews.value = item
  form.value = { title: item.title, content: item.content, image: item.image }
  showAddForm.value = true
}

const cancelEdit = () => {
  showAddForm.value = false
  editingNews.value = null
  form.value = { title: '', content: '', image: '' }
}

const deleteNews = async (id) => {
  if (!confirm('Удалить новость?')) return
  const response = await fetch(`/api/news/${id}`, {
    method: 'DELETE',
    headers: { Authorization: `Bearer ${userStore.token}` }
  })
  const data = await response.json()
  if (data.success) {
    notifySuccess('Новость удалена')
    dataStore.fetchNews()
  } else {
    notifyError(data.error || 'Ошибка')
  }
}

onMounted(() => dataStore.fetchNews())
</script>

<style scoped>
.news-admin {
  padding: 20px;
}

.news-admin h1 {
  color: #ff6600;
  margin-bottom: 20px;
}

.actions {
  margin-bottom: 20px;
}

.btn {
  background: #ff6600;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-secondary {
  background: #333;
}

.btn-small {
  padding: 5px 10px;
  font-size: 12px;
  margin-right: 5px;
}

.btn-danger {
  background: #dc3545;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: #1a1a1a;
  padding: 30px;
  border-radius: 8px;
  width: 500px;
  max-width: 90%;
}

.modal-content h2 {
  color: #ff6600;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  color: #888;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #333;
  border-radius: 4px;
  background: #0a0a0a;
  color: #fff;
}

.form-group textarea {
  height: 150px;
  resize: vertical;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.news-table {
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

.news-image {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}
</style>