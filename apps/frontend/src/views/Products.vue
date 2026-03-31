<template>
  <div class="products">
    <h1>Каталог товаров</h1>
    
    <div class="filters">
      <select v-model="selectedCategory">
        <option value="">Все категории</option>
        <option v-for="category in categories" :key="category.id" :value="category.slug">
          {{ category.name }}
        </option>
      </select>
    </div>

    <div class="product-grid">
      <div v-for="product in filteredProducts" :key="product.id" class="product-card">
        <img :src="product.image" :alt="product.title">
        <h3>{{ product.title }}</h3>
        <p class="category">{{ product.category_name }}</p>
        <p class="price">{{ formatPrice(product.price) }} ₽</p>
        <div class="actions">
          <router-link :to="`/products/${product.id}`">Подробнее</router-link>
          <button @click="addToCart(product)">В корзину</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'

export default {
  name: 'Products',
  setup() {
    const store = useStore()
    const route = useRoute()
    const selectedCategory = ref(route.query.category || '')

    const categories = computed(() => store.state.categories)
    const products = computed(() => store.state.products)

    const filteredProducts = computed(() => {
      if (!selectedCategory.value) {
        return products.value
      }
      return products.value.filter(p => p.category_slug === selectedCategory.value)
    })

    const formatPrice = (price) => {
      return new Intl.NumberFormat('ru-RU').format(price)
    }

    const addToCart = (product) => {
      store.commit('addToCart', product)
      alert('Товар добавлен в корзину!')
    }

    onMounted(() => {
      store.dispatch('fetchCategories')
      store.dispatch('fetchProducts')
    })

    return {
      selectedCategory,
      categories,
      filteredProducts,
      formatPrice,
      addToCart
    }
  }
}
</script>

<style scoped>
.products {
  padding: 20px;
}

.products h1 {
  color: #ff6600;
  margin-bottom: 20px;
}

.filters {
  margin-bottom: 20px;
}

.filters select {
  padding: 10px;
  border-radius: 4px;
  border: 1px solid #333;
  background: #1a1a1a;
  color: #fff;
  min-width: 200px;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.product-card {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
}

.product-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 4px;
  margin-bottom: 10px;
}

.product-card h3 {
  margin-bottom: 5px;
  color: #fff;
}

.category {
  color: #888;
  font-size: 14px;
  margin-bottom: 10px;
}

.price {
  font-size: 18px;
  font-weight: bold;
  color: #ff6600;
  margin-bottom: 10px;
}

.actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.actions a {
  color: #ff6600;
  text-decoration: none;
}

.actions button {
  background: #ff6600;
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}
</style>