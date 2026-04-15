<template>
  <div class="auth">
    <div class="auth__card">
      <h1>Вход</h1>
      <p class="auth__subtitle">Войдите, чтобы оформить заказ и следить за доставкой.</p>

      <form class="auth__form" @submit.prevent="login">
        <label>
          Email
          <input class="auth__input" v-model="email" type="email" required />
        </label>
        <label>
          Пароль
          <input class="auth__input" v-model="password" type="password" required />
        </label>
        <button type="submit" class="auth__submit">Войти</button>
        <p v-if="error" class="auth__error">{{ error }}</p>
      </form>

      <p class="auth__alt">
        Нет аккаунта?
        <router-link to="/register">Зарегистрироваться</router-link>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from '../stores/user'
import { notifySuccess } from '../utils/notify'

const router = useRouter()
const route = useRoute()
const user = useUserStore()
const email = ref('')
const password = ref('')
const error = ref('')

async function login() {
  const result = await user.login({ email: email.value, password: password.value })
  if (result.success) {
    notifySuccess('Добро пожаловать!')
    router.push((route.query.redirect as string) || '/')
  } else {
    error.value = result.error || 'Не удалось войти'
  }
}
</script>

<style scoped>
.auth {
  min-height: calc(100vh - 200px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
}

.auth__card {
  width: 100%;
  max-width: 420px;
  background: #ffffff;
  color: #111111;
  border-radius: 16px;
  padding: 36px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
}

.auth__card h1 {
  font-size: 26px;
  font-weight: 700;
  margin-bottom: 6px;
  color: #111;
}

.auth__subtitle {
  color: #6b6b72;
  font-size: 14px;
  margin-bottom: 24px;
}

.auth__form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.auth__form label {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 13px;
  color: #555;
}

.auth__input {
  padding: 12px 14px;
  border-radius: 10px;
  border: 1px solid #dcdcdc;
  background: #fafafa;
  color: #111;
  font-size: 14px;
}

.auth__input:focus {
  outline: none;
  border-color: var(--color-accent);
  background: #fff;
}

.auth__submit {
  margin-top: 8px;
  padding: 12px;
  border-radius: 10px;
  border: none;
  background: #111;
  color: #fff;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
}

.auth__submit:hover {
  background: var(--color-accent);
}

.auth__alt {
  text-align: center;
  margin-top: 22px;
  font-size: 14px;
  color: #6b6b72;
}

.auth__alt a {
  color: var(--color-accent);
  font-weight: 600;
}

.auth__error {
  margin-top: 4px;
  color: #c0392b;
  font-size: 13px;
}
</style>
