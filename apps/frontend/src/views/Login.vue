<template>
  <div class="login">
    <h1>Вход</h1>
    
    <form @submit.prevent="login" class="login-form">
      <div class="form-group">
        <label>Email</label>
        <input type="email" v-model="email" required>
      </div>
      <div class="form-group">
        <label>Пароль</label>
        <input type="password" v-model="password" required>
      </div>
      <button type="submit" class="btn">Войти</button>
    </form>
    
    <p class="register-link">
      Нет аккаунта? <router-link to="/register">Зарегистрироваться</router-link>
    </p>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

export default {
  name: 'Login',
  setup() {
    const store = useStore()
    const router = useRouter()
    const email = ref('')
    const password = ref('')

    const login = async () => {
      const result = await store.dispatch('login', {
        email: email.value,
        password: password.value
      })
      
      if (result.success) {
        alert('Вход выполнен успешно!')
        router.push('/')
      } else {
        alert('Ошибка входа: ' + result.error)
      }
    }

    return {
      email,
      password,
      login
    }
  }
}
</script>

<style scoped>
.login {
  padding: 20px;
  max-width: 400px;
  margin: 0 auto;
}

.login h1 {
  color: #ff6600;
  margin-bottom: 20px;
  text-align: center;
}

.login-form {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  color: #888;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #333;
  border-radius: 4px;
  background: #0a0a0a;
  color: #fff;
}

.btn {
  width: 100%;
  background: #ff6600;
  color: #fff;
  border: none;
  padding: 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.register-link {
  text-align: center;
  margin-top: 20px;
  color: #888;
}

.register-link a {
  color: #ff6600;
  text-decoration: none;
}
</style>