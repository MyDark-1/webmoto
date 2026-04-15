<template>
  <div class="promotions-admin">
    <h1>Управление акциями</h1>
    
    <div class="actions">
      <button @click="showAddForm = true" class="btn">Добавить акцию</button>
    </div>

    <div v-if="showAddForm" class="modal">
      <div class="modal-content">
        <h2>{{ editingPromotion ? 'Редактировать акцию' : 'Добавить акцию' }}</h2>
        <form @submit.prevent="savePromotion">
          <div class="form-group">
            <label>Название</label>
            <input type="text" v-model="form.title" required>
          </div>
          <div class="form-group">
            <label>Описание</label>
            <textarea v-model="form.content" required></textarea>
          </div>
          <div class="form-group">
            <label>Скидка (%)</label>
            <input type="number" v-model="form.discount" min="0" max="100" required>
          </div>
          <div class="form-group">
            <label>Изображение (URL)</label>
            <input type="text" v-model="form.image">
          </div>
          <div class="form-group">
            <label>Активна</label>
            <input type="checkbox" v-model="form.active">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn">Сохранить</button>
            <button type="button" @click="cancelEdit" class="btn btn-secondary">Отмена</button>
          </div>
        </form>
      </div>
    </div>

    <div class="promotions-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Изображение</th>
            <th>Название</th>
            <th>Скидка</th>
            <th>Статус</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="promotion in promotions" :key="promotion.id">
            <td>{{ promotion.id }}</td>
            <td>
              <img v-if="promotion.image" :src="promotion.image" :alt="promotion.title" class="promotion-image">
            </td>
            <td>{{ promotion.title }}</td>
            <td>{{ promotion.discount }}%</td>
            <td>
              <span class="status" :class="{ active: promotion.active }">
                {{ promotion.active ? 'Активна' : 'Неактивна' }}
              </span>
            </td>
            <td>
              <button @click="editPromotion(promotion)" class="btn-small">Редактировать</button>
              <button @click="deletePromotion(promotion.id)" class="btn-small btn-danger">Удалить</button>
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
const editingPromotion = ref(null)

const form = ref({ title: '', content: '', discount: 0, image: '', active: true })

const promotions = computed(() => dataStore.promotions)

const savePromotion = async () => {
  const url = editingPromotion.value
    ? `/api/promotions/${editingPromotion.value.id}`
    : '/api/promotions'
  const method = editingPromotion.value ? 'PUT' : 'POST'
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
    notifySuccess(editingPromotion.value ? 'Акция обновлена' : 'Акция добавлена')
    cancelEdit()
    dataStore.fetchPromotions()
  } else {
    notifyError(data.error || 'Ошибка')
  }
}

const editPromotion = (promotion) => {
  editingPromotion.value = promotion
  form.value = { ...promotion }
  showAddForm.value = true
}

const cancelEdit = () => {
  showAddForm.value = false
  editingPromotion.value = null
  form.value = { title: '', content: '', discount: 0, image: '', active: true }
}

const deletePromotion = async (id) => {
  if (!confirm('Удалить акцию?')) return
  const response = await fetch(`/api/promotions/${id}`, {
    method: 'DELETE',
    headers: { Authorization: `Bearer ${userStore.token}` }
  })
  const data = await response.json()
  if (data.success) {
    notifySuccess('Акция удалена')
    dataStore.fetchPromotions()
  } else {
    notifyError(data.error || 'Ошибка')
  }
}

onMounted(() => dataStore.fetchPromotions())
</script>

<style scoped>
.promotions-admin {
  padding: 20px;
}

.promotions-admin h1 {
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
  height: 100px;
  resize: vertical;
}

.form-group input[type="checkbox"] {
  width: auto;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.promotions-table {
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

.promotion-image {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}

.status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  background: #dc3545;
  color: #fff;
}

.status.active {
  background: #28a745;
}
</style>