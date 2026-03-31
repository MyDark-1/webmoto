<template>
  <div class="register">
    <h1>Регистрация</h1>
    
    <form @submit.prevent="register" class="register-form">
      <div class="form-group">
        <label>Email</label>
        <input type="email" v-model="email" required>
      </div>
      <div class="form-group">
        <label>Пароль</label>
        <input type="password" v-model="password" required>
      </div>
      <div class="form-group">
        <label>Подтвердите пароль</label>
        <input type="password" v-model="confirmPassword" required>
      </div>
      <button type="submit" class="btn">Зарегистрироваться</button>
    </form>
    
    <p class="login-link">
      Есть аккаунт? <router-link to="/login">Войти</router-link>
    </p>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

export default {
  name: 'Register',
  setup() {
    const store = useStore()
    const router = useRouter()
    const email = ref('')
    const password = ref('')
    const confirmPassword = ref('')

    const register = async () => {
      if (password.value !== confirmPassword.value) {
        alert('Пароли не совпадают')
        return
      }

      const result = await store.dispatch('register', {
        email: email.value,
        password: password.value
      })
      
      if (result.success) {
        alert('Регистрация прошла успешно!')
        router.push('/')
      } else {
        alert('Ошибка регистрации: ' + result.error)
      }
    }

    return {
      email,
      password,
      confirmPassword,
      register
    }
  }
}
</script>

<style scoped>
.register {
  padding: 20px;
  max-width: 400px;
  margin: 0 auto;
}

.register h1 {
  color: #ff6600;
  margin-bottom: 20px;
  text-align: center;
}

.register-form {
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

.login-link {
  text-align: center;
  margin-top: 20px;
  color: #888;
}

.login-link a {
  color: #ff6600;
  text-decoration: none;
}
</style>