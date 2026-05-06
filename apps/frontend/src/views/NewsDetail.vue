<template>
  <section class="news-detail container">
    <div v-if="loading" class="news-detail__loading">Загрузка...</div>
    <div v-else-if="!item" class="news-detail__empty">
      <p>Новость не найдена.</p>
      <router-link to="/news" class="btn btn--primary">Все новости</router-link>
    </div>
    <article v-else class="news-detail__article">
      <router-link to="/news" class="news-detail__back">← Назад к новостям</router-link>

      <header class="news-detail__header">
        <span class="tag">Новости</span>
        <h1>{{ item.title }}</h1>
        <div class="news-detail__meta">
          <time>{{ formatDate(item.created_at) }}</time>
        </div>
      </header>

      <div v-if="item.image" class="news-detail__image">
        <img :src="item.image" :alt="item.title" />
      </div>

      <div class="news-detail__content" v-html="item.content"></div>
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
  const data = await apiFetch<any>(`/api/news/${route.params.id}`)
  if (data.success && data.data) item.value = data.data
  loading.value = false
})
</script>

<style scoped>
.news-detail {
  padding: 48px 24px 80px;
  max-width: 800px;
}
.news-detail__loading,
.news-detail__empty {
  text-align: center;
  padding: 80px 0;
  color: var(--color-muted);
}
.news-detail__empty p {
  margin-bottom: 16px;
}
.news-detail__back {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--color-accent);
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 20px;
  transition: opacity 0.2s;
}
.news-detail__back:hover {
  opacity: 0.75;
}
.news-detail__header {
  margin-bottom: 24px;
}
.news-detail__header h1 {
  font-size: clamp(24px, 3vw, 34px);
  font-weight: 700;
  margin: 12px 0 8px;
  line-height: 1.3;
  letter-spacing: -0.02em;
}
.news-detail__meta {
  display: flex;
  gap: 16px;
  color: var(--color-muted);
  font-size: 14px;
}
.news-detail__image {
  border-radius: var(--radius-md);
  overflow: hidden;
  margin-bottom: 28px;
}
.news-detail__image img {
  width: 100%;
  height: auto;
  display: block;
}
.news-detail__content {
  font-size: 16px;
  line-height: 1.8;
  color: var(--color-text);
}
.news-detail__content p {
  margin-bottom: 16px;
}
</style>