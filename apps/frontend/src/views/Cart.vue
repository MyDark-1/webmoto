<template>
  <div class="cart">
    <h1>Корзина</h1>
    
    <div v-if="cart.length === 0" class="empty-cart">
      <p>Корзина пуста</p>
      <router-link to="/products" class="btn">Перейти в каталог</router-link>
    </div>

    <div v-else class="cart-content">
      <div class="cart-items">
        <div v-for="item in cart" :key="item.product.id" class="cart-item">
          <img :src="item.product.image" :alt="item.product.title">
          <div class="item-info">
            <h3>{{ item.product.title }}</h3>
            <p class="price">{{ formatPrice(item.product.price) }} ₽</p>
          </div>
          <div class="quantity">
            <button @click="decreaseQuantity(item.product.id)">-</button>
            <span>{{ item.quantity }}</span>
            <button @click="increaseQuantity(item.product.id)">+</button>
          </div>
          <button @click="removeItem(item.product.id)" class="remove-btn">Удалить</button>
        </div>
      </div>

      <div class="cart-summary">
        <h2>Итого: {{ formatPrice(total) }} ₽</h2>
        <router-link to="/checkout" class="btn">Оформить заказ</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useStore } from 'vuex'

export default {
  name: 'Cart',
  setup() {
    const store = useStore()

    const cart = computed(() => store.state.cart)
    const total = computed(() => {
      return cart.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0)
    })

    const formatPrice = (price) => {
      return new Intl.NumberFormat('ru-RU').format(price)
    }

    const increaseQuantity = (productId) => {
      const item = cart.value.find(i => i.product.id === productId)
      if (item) {
        store.commit('addToCart', item.product)
      }
    }

    const decreaseQuantity = (productId) => {
      const item = cart.value.find(i => i.product.id === productId)
      if (item && item.quantity > 1) {
        store.commit('removeFromCart', productId)
        store.commit('addToCart', item.product)
      }
    }

    const removeItem = (productId) => {
      store.commit('removeFromCart', productId)
    }

    return {
      cart,
      total,
      formatPrice,
      increaseQuantity,
      decreaseQuantity,
      removeItem
    }
  }
}
</script>

<style scoped>
.cart {
  padding: 20px;
}

.cart h1 {
  color: #ff6600;
  margin-bottom: 20px;
}

.empty-cart {
  text-align: center;
  padding: 40px;
}

.empty-cart p {
  margin-bottom: 20px;
  color: #888;
}

.btn {
  display: inline-block;
  background: #ff6600;
  color: #fff;
  padding: 12px 24px;
  border-radius: 4px;
  text-decoration: none;
}

.cart-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 40px;
}

.cart-item {
  display: flex;
  align-items: center;
  gap: 20px;
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.cart-item img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 4px;
}

.item-info {
  flex: 1;
}

.item-info h3 {
  margin-bottom: 5px;
}

.price {
  color: #ff6600;
  font-weight: bold;
}

.quantity {
  display: flex;
  align-items: center;
  gap: 10px;
}

.quantity button {
  background: #333;
  color: #fff;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 4px;
  cursor: pointer;
}

.remove-btn {
  background: #ff4444;
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.cart-summary {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
  height: fit-content;
}

.cart-summary h2 {
  color: #ff6600;
  margin-bottom: 20px;
}
</style>