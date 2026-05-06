<template>
  <header class="app-header" :class="{ 'app-header--open': open }">
    <div class="container app-header__inner">
      <Logo />

      <button
        class="app-header__burger"
        :aria-expanded="open"
        aria-label="Меню"
        @click="open = !open"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>

      <nav class="app-header__nav" :class="{ 'app-header__nav--open': open }">
        <router-link to="/" @click="open = false">Главная</router-link>
        <router-link to="/products" @click="open = false">Каталог</router-link>
        <router-link to="/news" @click="open = false">Новости</router-link>
        <router-link to="/promotions" @click="open = false">Акции</router-link>
        <router-link to="/salons" @click="open = false">Мотосалоны</router-link>
      </nav>

      <div class="app-header__actions" :class="{ 'app-header__actions--open': open }">
        <router-link to="/cart" class="app-header__cart" @click="open = false">
          <span aria-hidden="true">🛒</span>
          <span class="app-header__cart-label">Корзина</span>
          <span v-if="cart.count" class="app-header__badge">{{ cart.count }}</span>
        </router-link>
        <template v-if="user.isAuthenticated">
          <router-link to="/profile" class="btn btn--ghost" @click="open = false">
            Профиль
          </router-link>
          <button class="btn btn--white" @click="onLogout">Выйти</button>
          <span
            v-if="userInitials"
            class="app-header__initials"
            :title="user.user?.fullname"
          >{{ userInitials }}</span>
        </template>
        <template v-else>
          <router-link to="/login" class="btn btn--white" @click="open = false">
            Войти
          </router-link>
          <router-link to="/register" class="btn btn--white" @click="open = false">
            Регистрация
          </router-link>
        </template>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Logo from './Logo.vue'
import { useUserStore } from '../stores/user'
import { useCartStore } from '../stores/cart'
import { notifySuccess } from '../utils/notify'

const user = useUserStore()
const cart = useCartStore()
const route = useRoute()
const router = useRouter()
const open = ref(false)

const userInitials = computed(() => {
  const fullname = user.user?.fullname
  if (!fullname) return ''
  const parts = fullname.trim().split(/\s+/).filter(Boolean)
  if (parts.length === 0) return ''
  // берём первую букву имени и первую букву фамилии
  const first = parts[0][0]
  const last = parts.length > 1 ? parts[parts.length - 1][0] : ''
  return (first + last).toUpperCase()
})

watch(() => route.fullPath, () => (open.value = false))

function onLogout() {
  user.logout()
  notifySuccess('Вы вышли из аккаунта')
  router.push('/')
}
</script>

<style scoped>
.app-header {
  position: sticky;
  top: 0;
  z-index: 50;
  background: rgba(14, 14, 16, 0.85);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--color-border);
}
.app-header__inner {
  display: flex;
  align-items: center;
  gap: 32px;
  padding: 16px 24px;
}
.app-header__nav {
  display: flex;
  gap: 28px;
  flex: 1;
  justify-content: center;
}
.app-header__nav a {
  color: var(--color-muted);
  font-weight: 500;
  font-size: 14px;
  letter-spacing: 0.02em;
  padding: 6px 0;
  border-bottom: 2px solid transparent;
  transition: color 0.2s, border-color 0.2s;
}
.app-header__nav a:hover,
.app-header__nav a.router-link-active {
  color: var(--color-text);
  border-color: var(--color-accent);
}
.app-header__actions {
  display: flex;
  align-items: center;
  gap: 12px;
}
.app-header__cart {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--color-text);
  font-weight: 600;
  font-size: 14px;
  padding: 6px 12px;
  border-radius: 999px;
  border: 1px solid var(--color-border);
}
.app-header__cart:hover {
  border-color: var(--color-accent);
}
.app-header__badge {
  background: var(--color-accent);
  color: #fff;
  font-size: 11px;
  padding: 2px 8px;
  border-radius: 999px;
}
.app-header__initials {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: var(--color-accent);
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.02em;
  flex-shrink: 0;
  cursor: default;
  user-select: none;
}
.app-header__burger {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: transparent;
  border: none;
  padding: 6px;
  margin-left: auto;
}
.app-header__burger span {
  width: 22px;
  height: 2px;
  background: var(--color-text);
  transition: transform 0.2s;
}

@media (max-width: 900px) {
  .app-header__burger {
    display: flex;
  }
  .app-header__nav,
  .app-header__actions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--color-surface);
    flex-direction: column;
    gap: 14px;
    padding: 20px 24px;
    border-bottom: 1px solid var(--color-border);
    display: none;
  }
  .app-header__nav--open,
  .app-header__actions--open {
    display: flex;
  }
  .app-header__actions--open {
    top: calc(100% + 220px);
  }
  .app-header--open .app-header__inner {
    position: relative;
  }
}
</style>
