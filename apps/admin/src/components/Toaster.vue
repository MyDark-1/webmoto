<template>
  <Teleport to="body">
    <div class="toaster">
      <transition-group name="toast">
        <div
          v-for="t in toasts"
          :key="t.id"
          class="toast"
          :class="`toast--${t.type}`"
        >
          {{ t.message }}
        </div>
      </transition-group>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { toasts } from '../utils/notify'
</script>

<style scoped>
.toaster {
  position: fixed;
  top: 24px;
  right: 24px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  z-index: 9999;
}
.toast {
  padding: 12px 16px;
  border-radius: 10px;
  background: #fff;
  border: 1px solid var(--color-border);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  font-size: 14px;
  border-left: 3px solid var(--color-accent);
}
.toast--success { border-left-color: #2ecc71; }
.toast--error { border-left-color: #e74c3c; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateX(20px); }
.toast-enter-active, .toast-leave-active { transition: all 0.25s ease; }
</style>
