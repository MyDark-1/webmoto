<template>
  <div class="admin-logo">
    <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
      <circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="2" />
      <path d="M12 26l8-16 8 16-8-4-8 4z" fill="currentColor" />
    </svg>
    <span>
      <strong>RADAR</strong>
      <em>Админ</em>
    </span>
  </div>
</template>

<style scoped>
.admin-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  color: var(--color-accent);
}
.admin-logo span {
  display: flex;
  flex-direction: column;
  line-height: 1;
}
.admin-logo strong {
  font-size: 15px;
  letter-spacing: 0.15em;
  color: var(--color-text);
}
.admin-logo em {
  font-size: 11px;
  font-style: normal;
  color: var(--color-muted);
  margin-top: 3px;
}
</style>
