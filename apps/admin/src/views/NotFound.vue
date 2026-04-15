<template>
  <section class="admin-not-found">
    <h1>404</h1>
    <p>Раздел не найден.</p>
    <router-link to="/" class="btn btn--primary">К дашборду</router-link>
  </section>
</template>

<style scoped>
.admin-not-found {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 80px 20px;
  text-align: center;
}
.admin-not-found h1 {
  font-size: 80px;
  color: var(--color-accent);
}
</style>
