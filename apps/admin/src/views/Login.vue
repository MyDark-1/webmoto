<template>
  <div class="admin-login">
    <form class="admin-login__form" @submit.prevent="submit">
      <h1>Вход в админку</h1>
      <label>
        Email
        <input v-model="email" type="email" required />
      </label>
      <label>
        Пароль
        <input v-model="password" type="password" required />
      </label>
      <button type="submit">Войти</button>
      <p v-if="error" class="admin-login__error">{{ error }}</p>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAdminUserStore } from '../stores/user'

const email = ref('')
const password = ref('')
const error = ref('')
const router = useRouter()
const store = useAdminUserStore()

async function submit() {
  const result = await store.login({ email: email.value, password: password.value })
  if (result.success) router.push('/')
  else error.value = result.error || 'Ошибка входа'
}
</script>

<style scoped>
.admin-login {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #ffffff;
}
.admin-login__form {
  background: #ffffff;
  color: #111;
  padding: 32px;
  border-radius: 12px;
  width: 360px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
  border: 1px solid #e5e5e5;
}
.admin-login__form h1 {
  font-size: 22px;
  margin-bottom: 8px;
}
.admin-login__form label {
  display: flex;
  flex-direction: column;
  font-size: 14px;
  gap: 6px;
  color: #555;
}
.admin-login__form input {
  padding: 10px 12px;
  border: 1px solid #dcdcdc;
  border-radius: 8px;
  background: #fafafa;
  color: #111;
  font-size: 14px;
}
.admin-login__form button {
  margin-top: 6px;
  padding: 11px;
  border: none;
  border-radius: 8px;
  background: #111;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
}
.admin-login__error {
  color: #c0392b;
  font-size: 13px;
}
</style>
