<template>
  <section class="profile container">
    <header class="profile__header">
      <div>
        <h1>Личный кабинет</h1>
        <p v-if="user.user">{{ user.user.email }}</p>
      </div>
      <button class="btn btn--ghost" @click="onLogout">Выйти</button>
    </header>

    <div class="profile__grid">
      <section class="profile__card">
        <h2>Мои заказы</h2>
        <div v-if="loading" class="profile__loading">Загрузка...</div>
        <div v-else-if="orders.length === 0" class="profile__empty">
          <p>У вас пока нет заказов.</p>
          <router-link to="/products" class="btn btn--primary">В каталог</router-link>
        </div>
        <ul v-else class="orders">
          <li v-for="order in orders" :key="order.id" class="order">
            <div class="order__head">
              <span class="order__id">Заказ #{{ order.id }}</span>
              <span class="order__status" :class="`order__status--${order.status}`">
                {{ statusText(order.status) }}
              </span>
            </div>
            <ul class="order__items">
              <li v-for="item in order.items" :key="item.id">
                {{ item.product_title }} × {{ item.quantity }}
                <span>{{ formatPrice(item.price * item.quantity) }} ₽</span>
              </li>
            </ul>
            <div class="order__foot">
              <span class="order__date">{{ formatDate(order.created_at) }}</span>
              <strong>{{ formatPrice(order.total) }} ₽</strong>
            </div>
          </li>
        </ul>
      </section>

      <section class="profile__card">
        <h2>Мои данные</h2>
        <form class="profile-form" @submit.prevent="saveProfile">
          <label>
            ФИО
            <input v-model="profileForm.fullname" type="text" class="input" placeholder="Ваше полное имя" />
          </label>
          <label>
            Email
            <input v-model="profileForm.email" type="email" class="input" />
          </label>
          <label>
            Телефон
            <input v-model="profileForm.phone" type="tel" class="input" placeholder="+7 (XXX) XXX-XX-XX" />
          </label>
          <button type="submit" class="btn btn--primary">Сохранить изменения</button>
        </form>
      </section>

      <section class="profile__card">
        <h2>Связаться с нами</h2>
        <form class="feedback" @submit.prevent="sendFeedback">
          <textarea
            v-model="feedbackMessage"
            class="input"
            rows="5"
            placeholder="Ваш вопрос или пожелание..."
            required
          ></textarea>
          <button type="submit" class="btn btn--primary">Отправить</button>
        </form>
      </section>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../stores/user'
import { apiFetch } from '../utils/api'
import { formatDate, formatPrice } from '../utils/format'
import { notifyError, notifySuccess } from '../utils/notify'

const user = useUserStore()
const router = useRouter()
const orders = ref<any[]>([])
const loading = ref(true)
const feedbackMessage = ref('')

const profileForm = ref({
  fullname: user.user?.fullname || '',
  email: user.user?.email || '',
  phone: user.user?.phone || ''
})

const statusText = (s: string) =>
  ({
    pending: 'Ожидает',
    processing: 'В обработке',
    completed: 'Завершён',
    cancelled: 'Отменён'
  } as Record<string, string>)[s] || s

async function sendFeedback() {
  if (!feedbackMessage.value) return
  const data = await apiFetch('/api/feedback', {
    method: 'POST',
    json: { message: feedbackMessage.value }
  })
  if (data.success) {
    notifySuccess('Сообщение отправлено')
    feedbackMessage.value = ''
  } else {
    notifyError(data.error || 'Не удалось отправить')
  }
}

function onLogout() {
  user.logout()
  notifySuccess('Вы вышли из аккаунта')
  router.push('/')
}

async function saveProfile() {
  const data = await apiFetch('/api/user/profile', {
    method: 'PUT',
    json: {
      fullname: profileForm.value.fullname,
      email: profileForm.value.email,
      phone: profileForm.value.phone
    }
  })
  
  if (data.success) {
    notifySuccess('Данные профиля обновлены')
    user.user = { ...user.user, ...profileForm.value }
  } else {
    notifyError(data.error || 'Ошибка при сохранении')
  }
}

onMounted(async () => {
  const data = await apiFetch<any[]>('/api/orders')
  if (data.success && data.data) orders.value = data.data
  loading.value = false
})
</script>

<style scoped>
.profile {
  padding: 48px 24px 80px;
}
.profile__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 32px;
}
.profile__header h1 {
  font-size: 32px;
  font-weight: 700;
  letter-spacing: -0.02em;
}
.profile__header p {
  color: var(--color-muted);
  margin-top: 4px;
}
.profile__grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 24px;
  align-items: start;
}
.profile__card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: 24px;
}
.profile__card h2 {
  font-size: 18px;
  margin-bottom: 16px;
}
.profile__loading,
.profile__empty {
  color: var(--color-muted);
  text-align: center;
  padding: 32px 16px;
}
.profile__empty p {
  margin-bottom: 16px;
}
.orders {
  display: flex;
  flex-direction: column;
  gap: 14px;
  list-style: none;
}
.order {
  background: var(--color-surface-2);
  border-radius: var(--radius-md);
  padding: 16px;
}
.order__head {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
}
.order__id {
  font-weight: 600;
}
.order__status {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}
.order__status--pending {
  background: rgba(255, 174, 0, 0.15);
  color: var(--color-accent-2);
}
.order__status--processing {
  background: rgba(31, 142, 255, 0.15);
  color: #5fb1ff;
}
.order__status--completed {
  background: rgba(46, 204, 113, 0.15);
  color: #2ecc71;
}
.order__status--cancelled {
  background: rgba(231, 76, 60, 0.15);
  color: #e74c3c;
}
.order__items {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 14px;
  color: var(--color-muted);
}
.order__items li {
  display: flex;
  justify-content: space-between;
}
.order__foot {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid var(--color-border);
}
.order__date {
  color: var(--color-muted);
  font-size: 13px;
}
.order__foot strong {
  color: var(--color-accent);
}
.feedback {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.feedback textarea {
  resize: vertical;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.profile-form label {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 13px;
  color: var(--color-muted);
}

@media (max-width: 900px) {
  .profile__grid {
    grid-template-columns: 1fr;
  }
}
</style>
