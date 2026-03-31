<template>
  <div class="product-detail">
    <div v-if="product" class="product">
      <div class="product-image">
        <img :src="product.image" :alt="product.title">
      </div>
      <div class="product-info">
        <h1>{{ product.title }}</h1>
        <p class="category">{{ product.category_name }}</p>
        <p class="price">{{ formatPrice(product.price) }} ₽</p>
        <p class="description">{{ product.description }}</p>
        <button @click="addToCart" class="btn">Добавить в корзину</button>
      </div>
    </div>
    <div v-else>
      <p>Загрузка...</p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'

export default {
  name: 'ProductDetail',
  setup() {
    const store = useStore()
    const route = useRoute()
    const product = ref(null)

    const formatPrice = (price) => {
      return new Intl.NumberFormat('ru-RU').format(price)
    }

    const addToCart = () => {
      store.commit('addToCart', product.value)
      alert('Товар добавлен в корзину!')
    }

    onMounted(async () => {
      const response = await fetch(`/api/products/${route.params.id}`)
      const data = await response.json()
      if (data.success) {
        product.value = data.data
      }
    })

    return {
      product,
      formatPrice,
      addToCart
    }
  }
}
</script>

<style scoped>
.product-detail {
  padding: 20px;
}

.product {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
}

.product-image img {
  width: 100%;
  border-radius: 8px;
}

.product-info h1 {
  color: #ff6600;
  margin-bottom: 10px;
}

.category {
  color: #888;
  margin-bottom: 10px;
}

.price {
  font-size: 24px;
  font-weight: bold;
  color: #ff6600;
  margin-bottom: 20px;
}

.description {
  margin-bottom: 20px;
  line-height: 1.6;
}

.btn {
  background: #ff6600;
  color: #fff;
  border: none;
  padding: 12px 24px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}
</style>