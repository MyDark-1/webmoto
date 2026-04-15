<template>
  <section class="cart container">
    <header class="cart__header">
      <h1>Корзина</h1>
      <p v-if="cart.count">{{ cart.count }} {{ pluralize(cart.count) }}</p>
    </header>

    <div v-if="cart.items.length === 0" class="cart__empty">
      <div class="cart__empty-icon">🛒</div>
      <h2>Корзина пуста</h2>
      <p>Добавьте товары из каталога — они появятся здесь.</p>
      <router-link to="/products" class="btn btn--primary">Перейти в каталог</router-link>
    </div>

    <div v-else class="cart__grid">
      <div class="cart__items">
        <article
          v-for="item in cart.items"
          :key="item.product.id"
          class="cart-item"
        >
          <div class="cart-item__media">
            <img v-if="item.product.image" :src="item.product.image" :alt="item.product.title" />
            <div v-else class="cart-item__placeholder">RX</div>
          </div>
          <div class="cart-item__info">
            <router-link :to="`/products/${item.product.id}`" class="cart-item__title">
              {{ item.product.title }}
            </router-link>
            <span class="cart-item__price">{{ formatPrice(item.product.price) }} ₽</span>
          </div>
          <div class="cart-item__qty">
            <button @click="decrease(item.product.id)" aria-label="Меньше">−</button>
            <span>{{ item.quantity }}</span>
            <button @click="cart.add(item.product)" aria-label="Больше">+</button>
          </div>
          <button class="cart-item__remove" @click="cart.remove(item.product.id)">
            Удалить
          </button>
        </article>
      </div>

      <aside class="cart__summary">
        <h2>Итого</h2>
        <div class="cart__row">
          <span>Товары ({{ cart.count }})</span>
          <span>{{ formatPrice(cart.total) }} ₽</span>
        </div>
        <div class="cart__row">
          <span>Доставка</span>
          <span>Рассчитывается на оформлении</span>
        </div>
        <div class="cart__total">
          <span>К оплате</span>
          <strong>{{ formatPrice(cart.total) }} ₽</strong>
        </div>
        <router-link to="/checkout" class="btn btn--primary cart__checkout">
          Оформить заказ
        </router-link>
      </aside>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useCartStore } from '../stores/cart'
import { formatPrice } from '../utils/format'

const cart = useCartStore()

function decrease(id: number | string) {
  const item = cart.items.find((i) => i.product.id === id)
  if (!item) return
  if (item.quantity > 1) item.quantity--
  else cart.remove(id)
}

function pluralize(n: number) {
  const mod10 = n % 10
  const mod100 = n % 100
  if (mod10 === 1 && mod100 !== 11) return 'товар'
  if ([2, 3, 4].includes(mod10) && ![12, 13, 14].includes(mod100)) return 'товара'
  return 'товаров'
}
</script>

<style scoped>
.cart {
  padding: 48px 24px 80px;
}
.cart__header {
  margin-bottom: 32px;
}
.cart__header h1 {
  font-size: 32px;
  font-weight: 700;
  letter-spacing: -0.02em;
}
.cart__header p {
  color: var(--color-muted);
  margin-top: 4px;
}

.cart__empty {
  text-align: center;
  padding: 80px 20px;
  background: var(--color-surface);
  border-radius: var(--radius-lg);
  border: 1px solid var(--color-border);
}
.cart__empty-icon {
  font-size: 56px;
  margin-bottom: 16px;
}
.cart__empty h2 {
  font-size: 22px;
  margin-bottom: 8px;
}
.cart__empty p {
  color: var(--color-muted);
  margin-bottom: 24px;
}

.cart__grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 32px;
  align-items: start;
}

.cart__items {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.cart-item {
  display: grid;
  grid-template-columns: 96px 1fr auto auto;
  align-items: center;
  gap: 18px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: 16px;
}
.cart-item__media {
  width: 96px;
  height: 96px;
  border-radius: 10px;
  overflow: hidden;
  background: var(--color-surface-2);
  display: flex;
  align-items: center;
  justify-content: center;
}
.cart-item__media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.cart-item__placeholder {
  font-weight: 800;
  letter-spacing: 0.2em;
  color: var(--color-border);
}
.cart-item__info {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 0;
}
.cart-item__title {
  font-weight: 600;
  color: var(--color-text);
}
.cart-item__title:hover {
  color: var(--color-accent);
}
.cart-item__price {
  color: var(--color-muted);
  font-size: 14px;
}
.cart-item__qty {
  display: inline-flex;
  align-items: center;
  border: 1px solid var(--color-border);
  border-radius: 10px;
  overflow: hidden;
}
.cart-item__qty button {
  width: 32px;
  height: 32px;
  background: transparent;
  border: none;
  color: var(--color-text);
  font-size: 16px;
}
.cart-item__qty button:hover {
  background: var(--color-surface-2);
}
.cart-item__qty span {
  min-width: 32px;
  text-align: center;
  font-weight: 600;
}
.cart-item__remove {
  background: transparent;
  color: var(--color-muted);
  border: none;
  font-size: 13px;
}
.cart-item__remove:hover {
  color: #e74c3c;
}

.cart__summary {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: 24px;
  position: sticky;
  top: 100px;
}
.cart__summary h2 {
  font-size: 18px;
  margin-bottom: 16px;
}
.cart__row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  color: var(--color-muted);
  padding: 10px 0;
}
.cart__total {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  padding: 16px 0;
  border-top: 1px solid var(--color-border);
  margin-top: 8px;
}
.cart__total strong {
  font-size: 22px;
  color: var(--color-accent);
}
.cart__checkout {
  width: 100%;
  margin-top: 12px;
}

@media (max-width: 900px) {
  .cart__grid {
    grid-template-columns: 1fr;
  }
  .cart-item {
    grid-template-columns: 80px 1fr;
    grid-template-areas:
      'media info'
      'qty remove';
    gap: 12px;
  }
  .cart-item__media {
    grid-area: media;
  }
  .cart-item__info {
    grid-area: info;
  }
  .cart-item__qty {
    grid-area: qty;
  }
  .cart-item__remove {
    grid-area: remove;
    text-align: right;
  }
}
</style>
