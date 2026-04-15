<template>
  <section class="product container">
    <div v-if="loading" class="product__loading">Загрузка...</div>
    <div v-else-if="!product" class="product__empty">
      <p>Товар не найден.</p>
      <router-link to="/products" class="btn btn--primary">В каталог</router-link>
    </div>
    <div v-else class="product__grid">
      <div class="product__media">
        <img v-if="product.image" :src="product.image" :alt="product.title" />
        <div v-else class="product__placeholder">RADAR EXTREME</div>
      </div>
      <div class="product__info">
        <span v-if="product.category_name" class="tag">{{ product.category_name }}</span>
        <h1>{{ product.title }}</h1>
        <div class="product__price">{{ formatPrice(product.price) }} ₽</div>
        <p class="product__description">{{ product.description }}</p>
        <div class="product__actions">
          <button class="btn btn--primary" @click="addToCart">В корзину</button>
          <router-link to="/cart" class="btn btn--ghost">Перейти в корзину</router-link>
        </div>
        <ul class="product__features">
          <li><strong>Гарантия</strong>оригинальная, от производителя</li>
          <li><strong>Доставка</strong>по РФ от 1 до 7 дней</li>
          <li><strong>Оплата</strong>наличные, карта, рассрочка</li>
        </ul>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { apiFetch } from '../utils/api'
import { formatPrice } from '../utils/format'
import { notifySuccess } from '../utils/notify'

const cart = useCartStore()
const route = useRoute()
const product = ref<any>(null)
const loading = ref(true)

function addToCart() {
  if (!product.value) return
  cart.add(product.value)
  notifySuccess('Товар добавлен в корзину')
}

onMounted(async () => {
  const data = await apiFetch<any>(`/api/products/${route.params.id}`)
  if (data.success) product.value = data.data
  loading.value = false
})
</script>

<style scoped>
.product {
  padding: 48px 24px 80px;
}
.product__loading,
.product__empty {
  text-align: center;
  padding: 80px 20px;
  color: var(--color-muted);
}
.product__empty p {
  margin-bottom: 16px;
}
.product__grid {
  display: grid;
  grid-template-columns: 1.1fr 1fr;
  gap: 40px;
  align-items: start;
}
.product__media {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  aspect-ratio: 4 / 3;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.product__media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.product__placeholder {
  font-weight: 800;
  letter-spacing: 0.3em;
  color: var(--color-border);
}
.product__info h1 {
  font-size: 32px;
  font-weight: 700;
  letter-spacing: -0.02em;
  margin: 12px 0 16px;
}
.product__price {
  font-size: 28px;
  font-weight: 700;
  color: var(--color-accent);
  margin-bottom: 20px;
}
.product__description {
  color: var(--color-muted);
  line-height: 1.7;
  margin-bottom: 24px;
}
.product__actions {
  display: flex;
  gap: 12px;
  margin-bottom: 32px;
}
.product__features {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 8px;
  background: var(--color-surface);
  border-radius: var(--radius-md);
  padding: 16px 18px;
  border: 1px solid var(--color-border);
}
.product__features li {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  color: var(--color-muted);
}
.product__features strong {
  color: var(--color-text);
  font-weight: 600;
}

@media (max-width: 900px) {
  .product__grid {
    grid-template-columns: 1fr;
  }
}
</style>
