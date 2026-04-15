<template>
  <header class="admin-header">
    <Logo />
    <nav>
      <router-link to="/">Дашборд</router-link>
      <router-link to="/products">Товары</router-link>
      <router-link to="/orders">Заказы</router-link>
      <router-link to="/news">Новости</router-link>
      <router-link to="/promotions">Акции</router-link>
      <router-link to="/feedback">Обратная связь</router-link>
    </nav>
    <div class="admin-header__right">
      <span v-if="user.user" class="admin-header__email">{{ user.user.email }}</span>
      <button class="btn" @click="user.logout()">Выйти</button>
    </div>
  </header>
</template>

<script setup lang="ts">
import Logo from './Logo.vue'
import { useAdminUserStore } from '../stores/user'
const user = useAdminUserStore()
</script>

<style scoped>
.admin-header {
  display: flex;
  align-items: center;
  gap: 32px;
  padding: 14px 24px;
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border);
}
nav {
  display: flex;
  gap: 22px;
  flex: 1;
}
nav a {
  color: var(--color-muted);
  font-size: 14px;
  font-weight: 500;
}
nav a:hover,
nav a.router-link-active {
  color: var(--color-accent);
}
.admin-header__right {
  display: flex;
  align-items: center;
  gap: 12px;
}
.admin-header__email {
  color: var(--color-muted);
  font-size: 13px;
}
</style>
