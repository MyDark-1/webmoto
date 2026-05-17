<template>
  <footer class="app-footer">
    <div class="container app-footer__inner">
      <div class="app-footer__brand">
        <Logo />
        <p>Мотосалон экстремальной техники. Доставка по всей России.</p>
      </div>
      <div class="app-footer__cols">
        <div>
          <h4>Магазин</h4>
          <router-link to="/products">Каталог</router-link>
          <router-link to="/promotions">Акции</router-link>
          <router-link to="/news">Новости</router-link>
          <router-link to="/about">О компании</router-link>
        </div>
        <div>
          <h4>Контакты</h4>
          <a href="tel:+78005553535">8 800 555-35-35</a>
          <a href="mailto:info@radarextreme.ru">info@radarextreme.ru</a>
          <span>Пн–Вс 10:00–20:00</span>
        </div>
        <div>
          <h4>Аккаунт</h4>
          <router-link to="/login">Войти</router-link>
          <router-link to="/register">Регистрация</router-link>
          <router-link to="/profile">Профиль</router-link>
        </div>
      </div>
    </div>
    <div class="app-footer__copy container">
      © {{ year }} Radar Extreme. Все права защищены.
    </div>
  </footer>
</template>

<script setup lang="ts">
import Logo from './Logo.vue'
const year = new Date().getFullYear()
</script>

<style scoped>
.app-footer {
  background: var(--color-surface);
  border-top: 1px solid var(--color-border);
  margin-top: 80px;
  padding: 56px 0 24px;
}
.app-footer__inner {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 40px;
}
.app-footer__brand p {
  margin-top: 16px;
  color: var(--color-muted);
  max-width: 320px;
  font-size: 14px;
}
.app-footer__cols {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}
.app-footer__cols h4 {
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--color-muted);
  margin-bottom: 14px;
}
.app-footer__cols a,
.app-footer__cols span {
  display: block;
  color: var(--color-text);
  font-size: 14px;
  margin-bottom: 8px;
}
.app-footer__cols a:hover {
  color: var(--color-accent);
}
.app-footer__copy {
  margin-top: 40px;
  padding-top: 20px;
  border-top: 1px solid var(--color-border);
  color: var(--color-muted);
  font-size: 13px;
}
@media (max-width: 768px) {
  .app-footer__inner,
  .app-footer__cols {
    grid-template-columns: 1fr;
  }
}
</style>
