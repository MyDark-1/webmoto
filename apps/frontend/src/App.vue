<template>
  <div id="app">
    <AppHeader />
    <main class="main">
      <router-view v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>
    <AppFooter />
    <Toaster />
  </div>
</template>

<script setup lang="ts">
import AppHeader from './components/AppHeader.vue'
import AppFooter from './components/AppFooter.vue'
import Toaster from './components/Toaster.vue'
</script>

<style>
.main {
  min-height: calc(100vh - 360px);
}
.page-enter-from,
.page-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
.page-enter-active,
.page-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
</style>
