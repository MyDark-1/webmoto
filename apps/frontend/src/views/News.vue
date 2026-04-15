<template>
  <section class="news container">
    <header class="news__header">
      <span class="tag">Новости</span>
      <h1>Свежие новости салона</h1>
      <p>Поступления, события, обновления модельного ряда.</p>
    </header>

    <div v-if="loading" class="news__loading">Загрузка...</div>
    <div v-else-if="news.length === 0" class="news__empty">
      Пока нет новостей.
    </div>
    <div v-else class="news__grid">
      <article v-for="item in news" :key="item.id" class="news-card">
        <div class="news-card__media">
          <img v-if="item.image" :src="item.image" :alt="item.title" />
          <div v-else class="news-card__placeholder">RX</div>
        </div>
        <div class="news-card__body">
          <span class="news-card__date">{{ formatDate(item.created_at) }}</span>
          <h3>{{ item.title }}</h3>
          <p>{{ excerpt(item.content) }}</p>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { apiFetch } from '../utils/api'
import { formatDate } from '../utils/format'

const news = ref<any[]>([])
const loading = ref(true)

const excerpt = (text: string) =>
  text.length > 160 ? text.slice(0, 157) + '…' : text

onMounted(async () => {
  const data = await apiFetch<any[]>('/api/news')
  if (data.success && data.data) news.value = data.data
  loading.value = false
})
</script>

<style scoped>
.news {
  padding: 48px 24px 80px;
}
.news__header {
  margin-bottom: 32px;
  max-width: 720px;
}
.news__header h1 {
  font-size: clamp(28px, 3vw, 36px);
  font-weight: 700;
  margin: 12px 0 8px;
  letter-spacing: -0.02em;
}
.news__header p {
  color: var(--color-muted);
}
.news__loading,
.news__empty {
  padding: 60px 0;
  text-align: center;
  color: var(--color-muted);
}
.news__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}
.news-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: transform 0.2s, border-color 0.2s;
}
.news-card:hover {
  transform: translateY(-4px);
  border-color: var(--color-accent);
}
.news-card__media {
  aspect-ratio: 16 / 10;
  background: var(--color-surface-2);
  display: flex;
  align-items: center;
  justify-content: center;
}
.news-card__media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.news-card__placeholder {
  font-weight: 800;
  letter-spacing: 0.3em;
  color: var(--color-border);
}
.news-card__body {
  padding: 18px 20px 22px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.news-card__date {
  color: var(--color-accent);
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-weight: 600;
}
.news-card h3 {
  font-size: 17px;
  font-weight: 600;
}
.news-card p {
  color: var(--color-muted);
  font-size: 14px;
}
</style>
