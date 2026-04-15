<template>
  <section class="checkout container">
    <h1>Оформление заказа</h1>

    <div v-if="cart.items.length === 0" class="checkout__empty">
      <p>Корзина пуста — добавьте товары перед оформлением.</p>
      <router-link to="/products" class="btn btn--primary">В каталог</router-link>
    </div>

    <div v-else class="checkout__grid">
      <form class="checkout__form" @submit.prevent="submitOrder">
        <fieldset>
          <legend>Контактные данные</legend>
          <label>
            Имя
            <input v-model="form.name" type="text" required class="input" />
          </label>
          <label>
            Email
            <input v-model="form.email" type="email" required class="input" />
          </label>
          <label>
            Телефон
            <input v-model="form.phone" type="tel" required class="input" />
          </label>
        </fieldset>

        <fieldset>
          <legend>Доставка</legend>
          <label>
            Адрес
            <textarea v-model="form.address" required class="input" rows="3"></textarea>
          </label>
        </fieldset>

        <fieldset>
          <legend>Промокод</legend>
          <div class="checkout__promo">
            <input v-model="promoCode" type="text" class="input" placeholder="Введите код" />
            <button type="button" class="btn btn--ghost" @click="applyPromoCode">
              Применить
            </button>
          </div>
          <p v-if="discount > 0" class="checkout__promo-applied">
            Промокод применён: −{{ discount }}%
          </p>
        </fieldset>

        <button type="submit" class="btn btn--primary checkout__submit">
          Подтвердить заказ
        </button>
      </form>

      <aside class="checkout__summary">
        <h2>Ваш заказ</h2>
        <div class="checkout__items">
          <div v-for="item in cart.items" :key="item.product.id" class="checkout__item">
            <span>{{ item.product.title }} × {{ item.quantity }}</span>
            <span>{{ formatPrice(item.product.price * item.quantity) }} ₽</span>
          </div>
        </div>
        <div class="checkout__total">
          <span>К оплате</span>
          <strong>{{ formatPrice(total) }} ₽</strong>
        </div>
      </aside>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { apiFetch } from '../utils/api'
import { formatPrice } from '../utils/format'
import { notifyError, notifySuccess } from '../utils/notify'

const cart = useCartStore()
const router = useRouter()

const form = ref({ name: '', email: '', phone: '', address: '' })
const promoCode = ref('')
const discount = ref(0)

const total = computed(() => cart.total * (1 - discount.value / 100))

async function applyPromoCode() {
  if (!promoCode.value) return
  const data = await apiFetch<{ discount: number }>('/api/promo-codes/validate', {
    method: 'POST',
    json: { code: promoCode.value }
  })
  if (data.success && data.data) {
    discount.value = data.data.discount
    notifySuccess(`Промокод применён: скидка ${data.data.discount}%`)
  } else {
    notifyError('Недействительный промокод')
  }
}

async function submitOrder() {
  const data = await apiFetch('/api/orders', {
    method: 'POST',
    json: {
      items: cart.items.map((i) => ({
        product_id: i.product.id,
        quantity: i.quantity
      }))
    }
  })
  if (data.success) {
    cart.clear()
    notifySuccess('Заказ успешно оформлен!')
    router.push('/profile')
  } else {
    notifyError(data.error || 'Ошибка при оформлении заказа')
  }
}
</script>

<style scoped>
.checkout {
  padding: 48px 24px 80px;
}
.checkout h1 {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 28px;
  letter-spacing: -0.02em;
}
.checkout__empty {
  text-align: center;
  padding: 60px 20px;
  background: var(--color-surface);
  border-radius: var(--radius-lg);
  border: 1px solid var(--color-border);
}
.checkout__empty p {
  color: var(--color-muted);
  margin-bottom: 16px;
}
.checkout__grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 32px;
  align-items: start;
}
.checkout__form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
fieldset {
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: 20px;
  background: var(--color-surface);
}
legend {
  padding: 0 8px;
  color: var(--color-muted);
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}
fieldset label {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 13px;
  color: var(--color-muted);
  margin-bottom: 12px;
}
fieldset label:last-child {
  margin-bottom: 0;
}
.checkout__promo {
  display: flex;
  gap: 10px;
}
.checkout__promo .input {
  flex: 1;
}
.checkout__promo-applied {
  margin-top: 10px;
  color: #2ecc71;
  font-size: 13px;
}
.checkout__submit {
  align-self: stretch;
  padding: 16px;
  font-size: 15px;
}
.checkout__summary {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: 24px;
  position: sticky;
  top: 100px;
}
.checkout__summary h2 {
  font-size: 18px;
  margin-bottom: 16px;
}
.checkout__items {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--color-border);
}
.checkout__item {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  color: var(--color-muted);
}
.checkout__total {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-top: 16px;
}
.checkout__total strong {
  font-size: 22px;
  color: var(--color-accent);
}

@media (max-width: 900px) {
  .checkout__grid {
    grid-template-columns: 1fr;
  }
}
</style>
