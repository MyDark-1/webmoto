<template>
  <footer class="admin-footer">
    © {{ year }} Radar Extreme — панель администратора
  </footer>
</template>

<script setup lang="ts">
const year = new Date().getFullYear()
</script>

<style scoped>
.admin-footer {
  padding: 16px 24px;
  color: var(--color-muted);
  font-size: 12px;
  text-align: center;
  border-top: 1px solid var(--color-border);
  background: var(--color-surface);
}
</style>
