<template>
  <div class="product-edit">
    <h1>{{ productId ? 'Редактирование товара' : 'Новый товар' }}</h1>

    <form @submit.prevent="saveProduct" class="product-form">
      <div class="form-grid">
        <div class="form-group">
          <label>Название товара</label>
          <input v-model="form.title" type="text" class="input" required />
        </div>

        <div class="form-group">
          <label>Категория</label>
          <select v-model="form.category_id" class="input" required @change="onCategoryChange">
            <option value="">Выберите категорию</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>

        <div class="form-group">
          <label>Цена (₽)</label>
          <input v-model.number="form.price" type="number" min="0" step="0.01" class="input" required />
        </div>

        <div class="form-group">
          <label>URL изображения</label>
          <input v-model="form.image" type="text" class="input" placeholder="https://..." />
        </div>

        <div class="form-group">
          <label>Статус</label>
          <select v-model="form.status" class="input">
            <option value="active">Активный</option>
            <option value="inactive">Неактивный</option>
          </select>
        </div>

        <div class="form-group">
          <label>Наличие (общее)</label>
          <select v-model="form.stock_status" class="input">
            <option value="in_stock">В наличии</option>
            <option value="out_of_stock">Нет в наличии</option>
            <option value="on_order">Под заказ</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Описание товара</label>
        <textarea v-model="form.description" class="input" rows="5" placeholder="Полное описание товара..."></textarea>
      </div>

      <!-- Характеристики (выпадающие списки) -->
      <div class="form-section">
        <div class="form-section__head">
          <h3>Характеристики</h3>
          <button type="button" @click="addCharRow" class="btn btn--small">+ Добавить</button>
        </div>
        <div v-if="chars.length === 0" class="form-empty">Выберите категорию, чтобы добавить характеристики</div>
        <div v-for="(row, i) in chars" :key="row.key" class="char-row">
          <select v-model="row.characteristic_id" class="input char-select" @change="onCharSelect(i)">
            <option value="">Выберите...</option>
            <option
              v-for="ch in availableCharsForRow(i)"
              :key="ch.id"
              :value="ch.id"
            >{{ ch.name }}</option>
          </select>
          <input
            v-model="row.value"
            type="text"
            class="input char-value"
            placeholder="Значение"
          />
          <button type="button" @click="removeCharRow(i)" class="btn btn--danger btn--small">✕</button>
        </div>
      </div>

      <!-- Салоны (чекбоксы) -->
      <div class="form-section">
        <div class="form-section__head">
          <h3>Наличие в салонах</h3>
        </div>
        <div v-if="salons.length === 0" class="form-empty">Загрузка салонов...</div>
        <div v-for="salon in salons" :key="salon.id" class="salon-check">
          <label class="salon-check__label">
            <input
              type="checkbox"
              :value="salon.id"
              v-model="selectedSalonIds"
            />
            <span>
              <strong>{{ salon.name }}</strong>
              <span class="salon-check__city">{{ salon.city }}, {{ salon.address }}</span>
            </span>
          </label>
        </div>
      </div>

      <div class="form-actions">
        <router-link :to="{ name: 'products' }" class="btn btn--ghost">Отмена</router-link>
        <button type="submit" class="btn btn--primary" :disabled="loading">
          {{ loading ? 'Сохранение...' : 'Сохранить товар' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { apiFetch } from '../utils/api'
import { notifyError, notifySuccess } from '../utils/notify'

const route = useRoute()
const router = useRouter()
const productId = route.params.id

const loading = ref(false)
const categories = ref([])
const charDefs = ref([])     // Все определения характеристик
const salons = ref([])        // Все салоны
const selectedSalonIds = ref([])

const form = ref({
  title: '',
  category_id: '',
  price: 0,
  image: '',
  description: '',
  status: 'active',
  stock_status: 'in_stock'
})

// Характеристики товара: [{ characteristic_id, value }]
const chars = ref([])

// Генерируем уникальный ключ для каждой строки
let charKeyCounter = 0
function newCharKey() {
  return 'char_' + (++charKeyCounter)
}

function addCharRow(charId = '', val = '') {
  chars.value.push({ key: newCharKey(), characteristic_id: charId, value: val })
}

function removeCharRow(i) {
  chars.value.splice(i, 1)
}

function availableCharsForRow(currentIndex) {
  const selectedIds = chars.value
    .filter((_, i) => i !== currentIndex && _.characteristic_id)
    .map(_ => parseInt(_.characteristic_id))

  return charDefs.value.filter(ch => !selectedIds.includes(ch.id))
}

function onCharSelect(i) {
  // placeholder
}

async function loadCategories() {
  const data = await apiFetch('/api/categories')
  if (data.success) categories.value = data.data
}

async function loadChars() {
  const data = await apiFetch('/api/characteristics')
  if (data.success) charDefs.value = data.data || []
}

async function loadSalons() {
  const data = await apiFetch('/api/salons')
  if (data.success) salons.value = data.data || []
}

function onCategoryChange() {
  // Сбрасываем характеристики, если поменялась категория
  chars.value = []
}

async function loadProduct() {
  if (!productId) return

  const data = await apiFetch(`/api/products/${productId}`)
  if (data.success && data.data) {
    form.value = {
      title: data.data.title,
      category_id: data.data.category_id,
      price: data.data.price,
      image: data.data.image || '',
      description: data.data.description || '',
      status: data.data.status,
      stock_status: data.data.stock_status || 'in_stock'
    }

    // Загружаем характеристики
    chars.value = []
    if (data.data.characteristics_list && data.data.characteristics_list.length > 0) {
      data.data.characteristics_list.forEach(ch => {
        addCharRow(String(ch.characteristic_id), ch.value)
      })
    }

    // Загружаем салоны
    selectedSalonIds.value = []
    if (data.data.salons_list && data.data.salons_list.length > 0) {
      selectedSalonIds.value = data.data.salons_list.map(s => s.salon_id)
    }
  }
}

async function saveProduct() {
  loading.value = true

  try {
    const method = productId ? 'PUT' : 'POST'
    const url = productId ? `/api/products/${productId}` : '/api/products'

    // Формируем характеристики для отправки
    const characteristics_values = chars.value
      .filter(ch => ch.characteristic_id && ch.value)
      .map(ch => ({
        characteristic_id: parseInt(ch.characteristic_id),
        value: ch.value
      }))

    const body = {
      ...form.value,
      characteristics_values,
      salons: selectedSalonIds.value,
    }

    const data = await apiFetch(url, {
      method,
      json: body
    })

    if (data.success) {
      notifySuccess(productId ? 'Товар обновлен' : 'Товар создан')
      router.push({ name: 'products' })
    } else {
      notifyError(data.error || 'Ошибка при сохранении')
    }
  } catch (e) {
    notifyError('Ошибка соединения')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCategories()
  loadChars()
  loadSalons()
  loadProduct()
})
</script>

<style scoped>
.product-edit {
  padding: 20px;
  max-width: 900px;
}

.product-edit h1 {
  color: #ff6600;
  margin-bottom: 24px;
}

.product-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  color: #888;
  font-size: 14px;
}

.input {
  padding: 12px;
  border: 1px solid #333;
  border-radius: 6px;
  background: #0a0a0a;
  color: #fff;
  font-size: 15px;
}

.input:focus {
  outline: none;
  border-color: #ff6600;
}

textarea.input {
  resize: vertical;
  min-height: 100px;
  font-family: inherit;
  line-height: 1.5;
}

/* Секции */
.form-section {
  border: 1px solid #333;
  border-radius: 8px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.form-section__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.form-section__head h3 {
  color: #ff6600;
  font-size: 16px;
  margin: 0;
}

.form-empty {
  color: #666;
  font-size: 14px;
  text-align: center;
  padding: 16px;
}

/* Характеристики */
.char-row {
  display: flex;
  gap: 10px;
  align-items: center;
}

.char-select {
  flex: 1;
  min-width: 200px;
}

.char-value {
  flex: 1;
}

/* Салоны */
.salon-check {
  border-bottom: 1px solid #222;
  padding: 6px 0;
}
.salon-check:last-child {
  border-bottom: none;
}

.salon-check__label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  padding: 4px 0;
}

.salon-check__label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: #ff6600;
}

.salon-check__label span {
  display: flex;
  flex-direction: column;
  gap: 2px;
  font-size: 14px;
}

.salon-check__city {
  color: #888;
  font-size: 12px;
}

/* Кнопки */
.btn {
  padding: 12px 24px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-size: 15px;
  transition: opacity 0.2s;
}

.btn:hover {
  opacity: 0.8;
}

.btn--primary {
  background: #ff6600;
  color: white;
}

.btn--ghost {
  background: transparent;
  color: #fff;
  border: 1px solid #333;
  text-decoration: none;
}

.btn--small {
  padding: 6px 14px;
  font-size: 13px;
}

.btn--danger {
  background: #dc3545;
  color: #fff;
  padding: 8px 12px;
  font-size: 14px;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 10px;
}
</style>