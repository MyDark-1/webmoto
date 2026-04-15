<template>
  <router-link to="/" class="logo" aria-label="Radar Extreme">
    <svg
      class="logo__mark"
      width="40"
      height="40"
      viewBox="0 0 40 40"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="2" />
      <path d="M20 6v28M6 20h28" stroke="currentColor" stroke-width="1" opacity=".4" />
      <path d="M12 26l8-16 8 16-8-4-8 4z" fill="currentColor" />
    </svg>
    <span class="logo__text">
      <strong>RADAR</strong>
      <em>EXTREME</em>
    </span>
  </router-link>
</template>

<script setup lang="ts">
</script>

<style scoped>
.logo {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  color: var(--color-accent);
}
.logo__text {
  display: flex;
  flex-direction: column;
  line-height: 1;
}
.logo__text strong {
  font-size: 18px;
  font-weight: 800;
  letter-spacing: 0.12em;
  color: var(--color-text);
}
.logo__text em {
  font-size: 11px;
  font-style: normal;
  letter-spacing: 0.25em;
  color: var(--color-accent);
  margin-top: 4px;
}
</style>
