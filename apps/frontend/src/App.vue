<template>
  <div id="app">
    <header class="header">
      <div class="container">
        <div class="logo">
          <h1>Radar Extreme</h1>
          <span>Мотосалон</span>
        </div>
        <nav class="nav">
          <router-link to="/">Главная</router-link>
          <router-link to="/products">Каталог</router-link>
          <router-link to="/news">Новости</router-link>
          <router-link to="/promotions">Акции</router-link>
        </nav>
        <div class="header-actions">
          <router-link to="/cart" class="cart-link">
            🛒 Корзина ({{ cartCount }})
          </router-link>
          <template v-if="user">
            <router-link to="/profile">{{ user.email }}</router-link>
            <button @click="logout">Выйти</button>
          </template>
          <template v-else>
            <router-link to="/login">Войти</router-link>
            <router-link to="/register">Регистрация</router-link>
          </template>
        </div>
      </div>
    </header>

    <main class="main">
      <router-view />
    </main>

    <footer class="footer">
      <div class="container">
        <p>&copy; 2026 Radar Extreme. Все права защищены.</p>
      </div>
    </footer>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useStore } from 'vuex'

export default {
  name: 'App',
  setup() {
    const store = useStore()

    const user = computed(() => store.state.user)
    const cartCount = computed(() => store.state.cart.reduce((sum, item) => sum + item.quantity, 0))

    const logout = () => {
      store.commit('logout')
    }

    return {
      user,
      cartCount,
      logout
    }
  }
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  background-color: #0a0a0a;
  color: #fff;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.header {
  background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
  padding: 20px 0;
  border-bottom: 2px solid #ff6600;
}

.header .container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h1 {
  color: #ff6600;
  font-size: 28px;
  margin-bottom: 5px;
}

.logo span {
  color: #888;
  font-size: 14px;
}

.nav {
  display: flex;
  gap: 30px;
}

.nav a {
  color: #fff;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.nav a:hover,
.nav a.router-link-active {
  color: #ff6600;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 20px;
}

.cart-link {
  color: #ff6600;
  text-decoration: none;
  font-weight: bold;
}

.header-actions button {
  background: #ff6600;
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.main {
  min-height: calc(100vh - 200px);
  padding: 40px 0;
}

.footer {
  background: #1a1a1a;
  padding: 20px 0;
  text-align: center;
  color: #888;
  border-top: 1px solid #333;
}
</style>