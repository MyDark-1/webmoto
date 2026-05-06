<template>
  <div id="app">
    <div class="bg-animation" aria-hidden="true">
      <div class="bg-animation__orb bg-animation__orb--1"></div>
      <div class="bg-animation__orb bg-animation__orb--2"></div>
      <div class="bg-animation__orb bg-animation__orb--3"></div>
      <div class="bg-animation__orb bg-animation__orb--4"></div>
    </div>
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
  position: relative;
  z-index: 1;
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

/* ───── Анимированный фон ───── */
#app {
  position: relative;
}

.bg-animation {
  position: fixed;
  inset: 0;
  z-index: 0;
  overflow: hidden;
  pointer-events: none;
}

.bg-animation__orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.20;
  will-change: transform;
  animation: bgOrb 20s ease-in-out infinite alternate;
}

.bg-animation__orb--1 {
  width: 500px;
  height: 500px;
  background: var(--color-accent);
  top: -10%;
  left: -5%;
  animation-duration: 25s;
}

.bg-animation__orb--2 {
  width: 400px;
  height: 400px;
  background: #ffae00;
  bottom: -8%;
  right: -4%;
  animation-duration: 22s;
  animation-delay: -5s;
}

.bg-animation__orb--3 {
  width: 350px;
  height: 350px;
  background: #5fb1ff;
  top: 40%;
  left: 60%;
  animation-duration: 28s;
  animation-delay: -10s;
  opacity: 0.10;
}

.bg-animation__orb--4 {
  width: 300px;
  height: 300px;
  background: var(--color-accent);
  top: 60%;
  left: 10%;
  animation-duration: 20s;
  animation-delay: -15s;
  opacity: 0.12;
}

@keyframes bgOrb {
  0% {
    transform: translate(0, 0) scale(1);
  }
  25% {
    transform: translate(40px, -30px) scale(1.1);
  }
  50% {
    transform: translate(-20px, 40px) scale(0.95);
  }
  75% {
    transform: translate(30px, 20px) scale(1.05);
  }
  100% {
    transform: translate(-30px, -20px) scale(1);
  }
}
</style>