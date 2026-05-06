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
          <select v-model="form.category_id" class="input" required>
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
      </div>

      <div class="form-group">
        <label>Описание товара</label>
        <textarea v-model="form.description" class="input" rows="5" placeholder="Полное описание товара..."></textarea>
      </div>

      <div class="form-group">
        <label>Основные характеристики</label>
        <textarea v-model="form.characteristics" class="input" rows="6" placeholder="Характеристики товара, каждая с новой строки:
Материал: Сталь
Вес: 2 кг
Размер: 20x30 см"></textarea>
      </div>

      <div class="form-group">
        <label>Спецификации (разделы с характеристиками)</label>
        <textarea v-model="form.specifications" class="input" rows="8" placeholder='Формат JSON:
[
  {
    "title": "Двигатель",
    "items": {
      "Тип": "Бензиновый",
      "Объём": "200 см³"
    }
  },
  {
    "title": "Габариты",
    "items": {
      "Длина": "2100 мм",
      "Вес": "120 кг"
    }
  }
]'></textarea>
      </div>

      <div class="form-group">
        <label>Наличие</label>
        <select v-model="form.stock_status" class="input">
          <option value="in_stock">В наличии</option>
          <option value="out_of_stock">Нет в наличии</option>
          <option value="on_order">Под заказ</option>
        </select>
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
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { apiFetch } from '../utils/api'
import { notifyError, notifySuccess } from '../utils/notify'

const route = useRoute()
const router = useRouter()
const productId = route.params.id

const loading = ref(false)
const categories = ref([])

const form = ref({
  title: '',
  category_id: '',
  price: 0,
  image: '',
  description: '',
  characteristics: '',
  specifications: '',
  status: 'active',
  stock_status: 'in_stock'
})

async function loadCategories() {
  const data = await apiFetch('/api/categories')
  if (data.success) {
    categories.value = data.data
  }
}

async function loadProduct() {
  if (!productId) return

  const data = await apiFetch(`/api/products/${productId}`)
  if (data.success && data.data) {
    form.value = {
      title: data.data.title,
      category_id: data.data.category_id,
      price: data.data.price,
      image: data.data.image,
      description: data.data.description || '',
      characteristics: data.data.characteristics || '',
      specifications: data.data.specifications || '',
      status: data.data.status,
      stock_status: data.data.stock_status || 'in_stock'
    }
  }
}

async function saveProduct() {
  loading.value = true

  try {
    const method = productId ? 'PUT' : 'POST'
    const url = productId ? `/api/products/${productId}` : '/api/products'

    const data = await apiFetch(url, {
      method,
      json: form.value
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

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 10px;
}

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

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>