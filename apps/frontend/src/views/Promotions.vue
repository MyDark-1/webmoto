<template>
  <section class="promotions container">
    <header class="promotions__header">
      <span class="tag">Акции</span>
      <h1>Действующие акции и скидки</h1>
      <p>Только живые предложения. Условия — в карточке акции.</p>
    </header>

    <div v-if="loading" class="promotions__loading">Загрузка...</div>
    <div v-else-if="promotions.length === 0" class="promotions__empty">
      Сейчас нет активных акций.
    </div>
    <div v-else class="promotions__grid">
      <router-link
        v-for="promo in promotions"
        :key="promo.id"
        :to="`/promotions/${promo.id}`"
        class="promo-card"
      >
        <div class="promo-card__media">
          <img v-if="promo.image" :src="promo.image" :alt="promo.title" />
          <div v-else class="promo-card__placeholder">{{ promo.discount }}%</div>
          <span v-if="promo.discount" class="promo-card__discount">
            −{{ promo.discount }}%
          </span>
        </div>
        <div class="promo-card__body">
          <span class="promo-card__date">{{ formatDate(promo.created_at) }}</span>
          <h3>{{ promo.title }}</h3>
          <p>{{ excerpt(promo.content) }}</p>
        </div>
      </router-link>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { apiFetch } from '../utils/api'
import { formatDate } from '../utils/format'

const promotions = ref<any[]>([])
const loading = ref(true)

const excerpt = (text: string) =>
  text.length > 160 ? text.slice(0, 157) + '…' : text

onMounted(async () => {
  const data = await apiFetch<any[]>('/api/promotions')
  if (data.success && data.data) promotions.value = data.data
  loading.value = false
})
</script>

<style scoped>
.promotions {
  padding: 48px 24px 80px;
}
.promotions__header {
  margin-bottom: 32px;
  max-width: 720px;
}
.promotions__header h1 {
  font-size: clamp(28px, 3vw, 36px);
  font-weight: 700;
  margin: 12px 0 8px;
  letter-spacing: -0.02em;
}
.promotions__header p {
  color: var(--color-muted);
}
.promotions__loading,
.promotions__empty {
  padding: 60px 0;
  text-align: center;
  color: var(--color-muted);
}
.promotions__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}
.promo-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: transform 0.2s, border-color 0.2s;
  text-decoration: none;
  color: inherit;
}
.promo-card:hover {
  transform: translateY(-4px);
  border-color: var(--color-accent);
}
.promo-card__media {
  position: relative;
  aspect-ratio: 16 / 10;
  background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-2) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.promo-card__media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.promo-card__placeholder {
  font-weight: 800;
  font-size: 24px;
  letter-spacing: 0.1em;
  color: rgba(255,255,255,0.3);
}
.promo-card__discount {
  position: absolute;
  top: 14px;
  right: 14px;
  background: #0e0e10;
  color: var(--color-accent);
  font-weight: 800;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 13px;
  letter-spacing: 0.04em;
}
.promo-card__body {
  padding: 18px 20px 22px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.promo-card__date {
  color: var(--color-accent);
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-weight: 600;
}
.promo-card__body h3 {
  font-size: 17px;
  font-weight: 600;
  margin: 0;
}
.promo-card__body p {
  color: var(--color-muted);
  font-size: 14px;
  line-height: 1.5;
  margin: 0;
}
</style>