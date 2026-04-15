<template>
  <div id="admin-app">
    <template v-if="user.isAuthenticated">
      <AppHeader />
      <main class="admin-main">
        <router-view />
      </main>
      <AppFooter />
    </template>
    <template v-else>
      <router-view />
    </template>
    <Toaster />
  </div>
</template>

<script setup lang="ts">
import AppHeader from './components/AppHeader.vue'
import AppFooter from './components/AppFooter.vue'
import Toaster from './components/Toaster.vue'
import { useAdminUserStore } from './stores/user'
const user = useAdminUserStore()
</script>

<style>
.admin-main {
  min-height: calc(100vh - 120px);
  padding: 24px;
  max-width: 1280px;
  margin: 0 auto;
  width: 100%;
}
</style>
