<template>
  <section class="not-found container">
    <div class="not-found__code">404</div>
    <h1>Страница не найдена</h1>
    <p>Похоже, такого маршрута нет. Возвращайтесь в каталог.</p>
    <router-link to="/" class="btn btn--primary">На главную</router-link>
  </section>
</template>

<style scoped>
.not-found {
  min-height: 60vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 80px 20px;
  gap: 12px;
}
.not-found__code {
  font-size: clamp(80px, 14vw, 160px);
  font-weight: 800;
  letter-spacing: -0.04em;
  background: linear-gradient(135deg, var(--color-accent), var(--color-accent-2));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  line-height: 1;
}
.not-found h1 {
  font-size: 28px;
  font-weight: 700;
}
.not-found p {
  color: var(--color-muted);
  margin-bottom: 20px;
}
</style>
