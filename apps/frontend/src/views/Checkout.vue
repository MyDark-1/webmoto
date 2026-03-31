<template>
  <div class="checkout">
    <h1>Оформление заказа</h1>
    
    <div v-if="cart.length === 0" class="empty-cart">
      <p>Корзина пуста</p>
      <router-link to="/products" class="btn">Перейти в каталог</router-link>
    </div>

    <div v-else class="checkout-content">
      <div class="checkout-form">
        <h2>Данные для заказа</h2>
        <form @submit.prevent="submitOrder">
          <div class="form-group">
            <label>Имя</label>
            <input type="text" v-model="form.name" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" v-model="form.email" required>
          </div>
          <div class="form-group">
            <label>Телефон</label>
            <input type="tel" v-model="form.phone" required>
          </div>
          <div class="form-group">
            <label>Адрес доставки</label>
            <textarea v-model="form.address" required></textarea>
          </div>
          <div class="form-group">
            <label>Промокод</label>
            <input type="text" v-model="promoCode">
            <button type="button" @click="applyPromoCode">Применить</button>
          </div>
          <button type="submit" class="btn">Оформить заказ</button>
        </form>
      </div>

      <div class="order-summary">
        <h2>Ваш заказ</h2>
        <div class="order-items">
          <div v-for="item in cart" :key="item.product.id" class="order-item">
            <span>{{ item.product.title }} x {{ item.quantity }}</span>
            <span>{{ formatPrice(item.product.price * item.quantity) }} ₽</span>
          </div>
        </div>
        <div class="order-total">
          <strong>Итого: {{ formatPrice(total) }} ₽</strong>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

export default {
  name: 'Checkout',
  setup() {
    const store = useStore()
    const router = useRouter()
    const promoCode = ref('')
    const discount = ref(0)

    const form = ref({
      name: '',
      email: '',
      phone: '',
      address: ''
    })

    const cart = computed(() => store.state.cart)
    const total = computed(() => {
      const subtotal = cart.value.reduce((sum, item) => sum + item.product.price * item.quantity, 0)
      return subtotal * (1 - discount.value / 100)
    })

    const formatPrice = (price) => {
      return new Intl.NumberFormat('ru-RU').format(price)
    }

    const applyPromoCode = async () => {
      if (!promoCode.value) return
      
      const response = await fetch('/api/promo-codes/validate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code: promoCode.value })
      })
      
      const data = await response.json()
      if (data.success) {
        discount.value = data.data.discount
        alert(`Промокод применен! Скидка ${data.data.discount}%`)
      } else {
        alert('Недействительный промокод')
      }
    }

    const submitOrder = async () => {
      const orderData = {
        items: cart.value.map(item => ({
          product_id: item.product.id,
          quantity: item.quantity
        }))
      }

      const response = await fetch('/api/orders', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${store.state.token}`
        },
        body: JSON.stringify(orderData)
      })

      const data = await response.json()
      if (data.success) {
        store.commit('clearCart')
        alert('Заказ успешно оформлен!')
        router.push('/profile')
      } else {
        alert('Ошибка при оформлении заказа')
      }
    }

    return {
      form,
      cart,
      total,
      promoCode,
      discount,
      formatPrice,
      applyPromoCode,
      submitOrder
    }
  }
}
</script>

<style scoped>
.checkout {
  padding: 20px;
}

.checkout h1 {
  color: #ff6600;
  margin-bottom: 20px;
}

.empty-cart {
  text-align: center;
  padding: 40px;
}

.btn {
  display: inline-block;
  background: #ff6600;
  color: #fff;
  padding: 12px 24px;
  border-radius: 4px;
  text-decoration: none;
  border: none;
  cursor: pointer;
}

.checkout-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
}

.checkout-form {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
}

.checkout-form h2 {
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

.order-summary {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
  height: fit-content;
}

.order-summary h2 {
  color: #ff6600;
  margin-bottom: 20px;
}

.order-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid #333;
}

.order-total {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 2px solid #ff6600;
  color: #ff6600;
  font-size: 18px;
}
</style>