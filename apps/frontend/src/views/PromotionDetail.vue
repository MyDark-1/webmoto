<template>
  <section class="promo-detail">
    <div v-if="loading" class="promo-detail__status">Загрузка...</div>
    <div v-else-if="!item" class="promo-detail__status">
      <p>Акция не найдена.</p>
      <router-link to="/promotions" class="btn btn--primary">Все акции</router-link>
    </div>
    <article v-else class="promo-detail__article">
      <div class="container">
        <router-link to="/promotions" class="promo-detail__back">← Назад к акциям</router-link>

        <header class="promo-detail__header">
          <span class="tag">Акция</span>
          <span v-if="item.discount" class="promo-detail__discount-badge">−{{ item.discount }}%</span>
          <h1>{{ item.title }}</h1>
          <div class="promo-detail__meta">
            <time>{{ formatDate(item.created_at) }}</time>
          </div>
        </header>

        <div v-if="item.image" class="promo-detail__image">
          <img :src="item.image" :alt="item.title" />
        </div>

        <div class="promo-detail__content" v-html="item.content"></div>
      </div>
    </article>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { apiFetch } from '../utils/api'
import { formatDate } from '../utils/format'

const route = useRoute()
const item = ref<any>(null)
const loading = ref(true)

onMounted(async () => {
  const data = await apiFetch<any>(`/api/promotions/${route.params.id}`)
  if (data.success && data.data) item.value = data.data
  loading.value = false
})
</script>

<style scoped>
.promo-detail {
  padding: 48px 0 80px;
}
.promo-detail__status {
  text-align: center;
  padding: 80px 0;
  color: var(--color-muted);
}
.promo-detail__status p {
  margin-bottom: 16px;
}
.promo-detail__back {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--color-accent);
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 20px;
  transition: opacity 0.2s;
}
.promo-detail__back:hover {
  opacity: 0.75;
}
.promo-detail__header {
  margin-bottom: 24px;
  max-width: 800px;
}
.promo-detail__header h1 {
  font-size: clamp(24px, 3vw, 34px);
  font-weight: 700;
  margin: 12px 0 8px;
  line-height: 1.3;
  letter-spacing: -0.02em;
}
.promo-detail__meta {
  display: flex;
  gap: 16px;
  color: var(--color-muted);
  font-size: 14px;
}
.promo-detail__discount-badge {
  display: inline-block;
  background: var(--color-accent);
  color: #fff;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 14px;
}
.promo-detail__image {
  border-radius: var(--radius-md);
  overflow: hidden;
  margin-bottom: 28px;
  max-width: 800px;
}
.promo-detail__image img {
  width: 100%;
  height: auto;
  display: block;
}
.promo-detail__content {
  font-size: 16px;
  line-height: 1.8;
  color: var(--color-text);
  max-width: 800px;
}
.promo-detail__content p {
  margin-bottom: 16px;
}
</style>