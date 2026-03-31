<template>
  <div class="home">
    <section class="hero">
      <h1>Добро пожаловать в Radar Extreme</h1>
      <p>Лучший мотосалон для экстремалов</p>
      <router-link to="/products" class="btn">Смотреть каталог</router-link>
    </section>

    <section class="categories">
      <h2>Категории</h2>
      <div class="category-grid">
        <div v-for="category in categories" :key="category.id" class="category-card">
          <h3>{{ category.name }}</h3>
          <router-link :to="`/products?category=${category.slug}`">Смотреть</router-link>
        </div>
      </div>
    </section>

    <section class="featured">
      <h2>Популярные товары</h2>
      <div class="product-grid">
        <div v-for="product in featuredProducts" :key="product.id" class="product-card">
          <img :src="product.image" :alt="product.title">
          <h3>{{ product.title }}</h3>
          <p class="price">{{ formatPrice(product.price) }} ₽</p>
          <router-link :to="`/products/${product.id}`">Подробнее</router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { computed, onMounted } from 'vue'
import { useStore } from 'vuex'

export default {
  name: 'Home',
  setup() {
    const store = useStore()

    const categories = computed(() => store.state.categories)
    const featuredProducts = computed(() => store.state.products.slice(0, 6))

    const formatPrice = (price) => {
      return new Intl.NumberFormat('ru-RU').format(price)
    }

    onMounted(() => {
      store.dispatch('fetchCategories')
      store.dispatch('fetchProducts')
    })

    return {
      categories,
      featuredProducts,
      formatPrice
    }
  }
}
</script>

<style scoped>
.home {
  padding: 20px;
}

.hero {
  text-align: center;
  padding: 60px 20px;
  background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
  border-radius: 8px;
  margin-bottom: 40px;
}

.hero h1 {
  font-size: 36px;
  color: #ff6600;
  margin-bottom: 10px;
}

.hero p {
  font-size: 18px;
  color: #888;
  margin-bottom: 20px;
}

.btn {
  display: inline-block;
  background: #ff6600;
  color: #fff;
  padding: 12px 24px;
  border-radius: 4px;
  text-decoration: none;
  font-weight: bold;
}

.categories, .featured {
  margin-bottom: 40px;
}

.categories h2, .featured h2 {
  font-size: 24px;
  color: #ff6600;
  margin-bottom: 20px;
}

.category-grid, .product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.category-card, .product-card {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
}

.category-card h3, .product-card h3 {
  margin-bottom: 10px;
  color: #fff;
}

.product-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 4px;
  margin-bottom: 10px;
}

.price {
  font-size: 18px;
  font-weight: bold;
  color: #ff6600;
  margin-bottom: 10px;
}

.category-card a, .product-card a {
  color: #ff6600;
  text-decoration: none;
}
</style>