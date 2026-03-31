<template>
  <div class="products-admin">
    <h1>Управление товарами</h1>
    
    <div class="actions">
      <button @click="showAddForm = true" class="btn">Добавить товар</button>
    </div>

    <div v-if="showAddForm" class="modal">
      <div class="modal-content">
        <h2>{{ editingProduct ? 'Редактировать товар' : 'Добавить товар' }}</h2>
        <form @submit.prevent="saveProduct">
          <div class="form-group">
            <label>Название</label>
            <input type="text" v-model="form.title" required>
          </div>
          <div class="form-group">
            <label>Категория</label>
            <select v-model="form.category_id" required>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Описание</label>
            <textarea v-model="form.description" required></textarea>
          </div>
          <div class="form-group">
            <label>Цена</label>
            <input type="number" v-model="form.price" required>
          </div>
          <div class="form-group">
            <label>Изображение (URL)</label>
            <input type="text" v-model="form.image">
          </div>
          <div class="form-group">
            <label>Статус</label>
            <select v-model="form.status">
              <option value="active">Активен</option>
              <option value="inactive">Неактивен</option>
            </select>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn">Сохранить</button>
            <button type="button" @click="cancelEdit" class="btn btn-secondary">Отмена</button>
          </div>
        </form>
      </div>
    </div>

    <div class="products-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Изображение</th>
            <th>Название</th>
            <th>Категория</th>
            <th>Цена</th>
            <th>Статус</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.id }}</td>
            <td>
              <img v-if="product.image" :src="product.image" :alt="product.title" class="product-image">
            </td>
            <td>{{ product.title }}</td>
            <td>{{ product.category_name }}</td>
            <td>{{ formatPrice(product.price) }} ₽</td>
            <td>
              <span class="status" :class="product.status">{{ product.status === 'active' ? 'Активен' : 'Неактивен' }}</span>
            </td>
            <td>
              <button @click="editProduct(product)" class="btn-small">Редактировать</button>
              <button @click="deleteProduct(product.id)" class="btn-small btn-danger">Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'

export default {
  name: 'ProductsAdmin',
  setup() {
    const store = useStore()
    const showAddForm = ref(false)
    const editingProduct = ref(null)
    const categories = ref([])

    const form = ref({
      title: '',
      category_id: '',
      description: '',
      price: '',
      image: '',
      status: 'active'
    })

    const products = computed(() => store.state.products)

    const formatPrice = (price) => {
      return new Intl.NumberFormat('ru-RU').format(price)
    }

    const loadCategories = async () => {
      const response = await fetch('/api/categories')
      const data = await response.json()
      if (data.success) {
        categories.value = data.data
      }
    }

    const saveProduct = async () => {
      const url = editingProduct.value 
        ? `/api/products/${editingProduct.value.id}`
        : '/api/products'
      
      const method = editingProduct.value ? 'PUT' : 'POST'
      
      const response = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${store.state.token}`
        },
        body: JSON.stringify(form.value)
      })
      
      const data = await response.json()
      if (data.success) {
        alert(editingProduct.value ? 'Товар обновлен!' : 'Товар добавлен!')
        cancelEdit()
        store.dispatch('fetchProducts')
      } else {
        alert('Ошибка: ' + data.error)
      }
    }

    const editProduct = (product) => {
      editingProduct.value = product
      form.value = {
        title: product.title,
        category_id: product.category_id,
        description: product.description,
        price: product.price,
        image: product.image,
        status: product.status
      }
      showAddForm.value = true
    }

    const cancelEdit = () => {
      showAddForm.value = false
      editingProduct.value = null
      form.value = {
        title: '',
        category_id: '',
        description: '',
        price: '',
        image: '',
        status: 'active'
      }
    }

    const deleteProduct = async (id) => {
      if (!confirm('Удалить товар?')) return
      
      const response = await fetch(`/api/products/${id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${store.state.token}`
        }
      })
      
      const data = await response.json()
      if (data.success) {
        alert('Товар удален!')
        store.dispatch('fetchProducts')
      } else {
        alert('Ошибка: ' + data.error)
      }
    }

    onMounted(() => {
      store.dispatch('fetchProducts')
      loadCategories()
    })

    return {
      products,
      categories,
      showAddForm,
      editingProduct,
      form,
      formatPrice,
      saveProduct,
      editProduct,
      cancelEdit,
      deleteProduct
    }
  }
}
</script>

<style scoped>
.products-admin {
  padding: 20px;
}

.products-admin h1 {
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
.form-group textarea,
.form-group select {
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

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.products-table {
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

.product-image {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}

.status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
}

.status.active {
  background: #28a745;
  color: #fff;
}

.status.inactive {
  background: #dc3545;
  color: #fff;
}
</style>