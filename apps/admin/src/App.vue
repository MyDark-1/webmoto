<template>
  <div id="admin-app">
    <div v-if="user" class="admin-layout">
      <aside class="sidebar">
        <div class="logo">
          <h2>Radar Extreme</h2>
          <span>Админ-панель</span>
        </div>
        <nav class="nav">
          <router-link to="/">Дашборд</router-link>
          <router-link to="/products">Товары</router-link>
          <router-link to="/orders">Заказы</router-link>
          <router-link to="/news">Новости</router-link>
          <router-link to="/promotions">Акции</router-link>
          <router-link to="/feedback">Обратная связь</router-link>
        </nav>
        <div class="sidebar-footer">
          <button @click="logout">Выйти</button>
        </div>
      </aside>
      <main class="main-content">
        <router-view />
      </main>
    </div>
    <div v-else>
      <router-view />
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useStore } from 'vuex'

export default {
  name: 'AdminApp',
  setup() {
    const store = useStore()

    const user = computed(() => store.state.user)

    const logout = () => {
      store.commit('logout')
    }

    return {
      user,
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

.admin-layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 250px;
  background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
  padding: 20px;
  display: flex;
  flex-direction: column;
  border-right: 2px solid #ff6600;
}

.logo h2 {
  color: #ff6600;
  margin-bottom: 5px;
}

.logo span {
  color: #888;
  font-size: 14px;
}

.nav {
  margin-top: 30px;
  flex: 1;
}

.nav a {
  display: block;
  color: #fff;
  text-decoration: none;
  padding: 12px 15px;
  margin-bottom: 5px;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.nav a:hover,
.nav a.router-link-active {
  background: #ff6600;
  color: #fff;
}

.sidebar-footer {
  margin-top: auto;
}

.sidebar-footer button {
  width: 100%;
  background: #ff4444;
  color: #fff;
  border: none;
  padding: 12px;
  border-radius: 4px;
  cursor: pointer;
}

.main-content {
  flex: 1;
  padding: 20px;
  background: #0a0a0a;
}
</style>