<template>
  <div class="home">
    <!-- Акции — карусель -->
    <section class="promo">
      <div class="container promo__inner">
        <div class="promo__head">
          <span class="tag">Акции</span>
          <h1>Скорость, драйв и выгодные предложения</h1>
          <p>Актуальные акции салона и свежие новости о новинках экстремальной техники.</p>
        </div>

        <div class="carousel-wrapper">
          <div class="carousel-track" ref="promoTrack" @scroll="onPromoScroll">
            <article
              v-for="item in promos"
              :key="item.id"
              class="promo-card"
              :style="{ backgroundImage: `url(${item.image})` }"
            >
              <div class="promo-card__overlay">
                <h3 class="promo-card__title">{{ item.title }}</h3>
              </div>
              <span class="promo-card__badge promo-card__badge--discount">
                −{{ item.discount }}%
              </span>
            </article>
          </div>

          <button
            class="carousel-btn carousel-btn--prev"
            @click="scrollPromo(-1)"
            :disabled="promoIndex === 0"
          >‹</button>
          <button
            class="carousel-btn carousel-btn--next"
            @click="scrollPromo(1)"
            :disabled="promoIndex >= promos.length - 1"
          >›</button>

          <div class="carousel-dots">
            <span
              v-for="(_, i) in promos"
              :key="i"
              class="carousel-dot"
              :class="{ 'carousel-dot--active': i === promoIndex }"
              @click="goToPromo(i)"
            ></span>
          </div>
        </div>

        <router-link to="/promotions" class="promo__all-link">
          Все акции →
        </router-link>
      </div>
    </section>

    <!-- Новости — карусель -->
    <section class="section">
      <div class="container">
        <div class="section__head">
          <h2 class="section__title">Новости</h2>
          <router-link to="/news" class="section__link">Все новости →</router-link>
        </div>

        <div class="carousel-wrapper">
          <div class="carousel-track" ref="newsTrack" @scroll="onNewsScroll">
            <article
              v-for="item in news"
              :key="item.id"
              class="promo-card"
              :style="{ backgroundImage: `url(${item.image})` }"
            >
              <div class="promo-card__overlay">
                <span class="promo-card__badge promo-card__badge--news">
                  {{ formatDate(item.created_at) }}
                </span>
                <h3 class="promo-card__title">{{ item.title }}</h3>
              </div>
            </article>
          </div>

          <button
            class="carousel-btn carousel-btn--prev"
            @click="scrollNews(-1)"
            :disabled="newsIndex === 0"
          >‹</button>
          <button
            class="carousel-btn carousel-btn--next"
            @click="scrollNews(1)"
            :disabled="newsIndex >= news.length - 1"
          >›</button>

          <div class="carousel-dots">
            <span
              v-for="(_, i) in news"
              :key="i"
              class="carousel-dot"
              :class="{ 'carousel-dot--active': i === newsIndex }"
              @click="goToNews(i)"
            ></span>
          </div>
        </div>
      </div>
    </section>

    <section class="section benefits">
      <div class="container">
        <h2 class="section__title">Почему Radar Extreme</h2>
        <p class="section__subtitle">
          Мы отобрали только проверенную технику и предлагаем полный цикл сервиса.
        </p>
        <div class="benefits__grid">
          <div class="benefit" v-for="b in benefits" :key="b.title">
            <div class="benefit__icon">{{ b.icon }}</div>
            <h3>{{ b.title }}</h3>
            <p>{{ b.text }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="section__head">
          <h2 class="section__title">Категории</h2>
          <router-link to="/products" class="section__link">Смотреть всё →</router-link>
        </div>
        <div class="grid grid--cards">
          <router-link
            v-for="category in catalog.categories"
            :key="category.id"
            :to="`/products?category=${category.slug}`"
            class="category-card"
          >
            <h3>{{ category.name }}</h3>
            <span>Смотреть →</span>
          </router-link>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="section__head">
          <h2 class="section__title">Популярные модели</h2>
          <router-link to="/products" class="section__link">Весь каталог →</router-link>
        </div>
        <div class="grid grid--cards">
          <ProductCard
            v-for="product in featured"
            :key="product.id"
            :product="product"
            @buy="cart.add($event)"
          />
        </div>
      </div>
    </section>

    <section class="newsletter">
      <div class="container newsletter__inner">
        <div class="newsletter__text">
          <h2>Подпишитесь на рассылку</h2>
          <p>
            Первыми узнавайте об акциях, поступлениях и закрытых тест-драйвах Radar Extreme.
          </p>
        </div>
        <form class="newsletter__form" @submit.prevent="subscribe">
          <input
            v-model="email"
            type="email"
            required
            class="input newsletter__input"
            placeholder="Ваш e-mail"
          />
          <button class="btn btn--primary" type="submit">
            {{ subscribed ? 'Готово' : 'Подписаться' }}
          </button>
        </form>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import ProductCard from '../components/ProductCard.vue'
import { useCatalogStore } from '../stores/catalog'
import { useCartStore } from '../stores/cart'
import { apiFetch } from '../utils/api'

const catalog = useCatalogStore()
const cart = useCartStore()
const email = ref('')
const subscribed = ref(false)

const featured = computed(() => catalog.products.slice(0, 8))

const promos = ref<any[]>([])
const news = ref<any[]>([])

const promoTrack = ref<HTMLElement | null>(null)
const newsTrack = ref<HTMLElement | null>(null)
const promoIndex = ref(0)
const newsIndex = ref(0)

const benefits = [
  {
    icon: '⚡',
    title: 'Оригинальная техника',
    text: 'Прямые поставки от производителей, полный пакет документов и гарантия.'
  },
  {
    icon: '🛠️',
    title: 'Собственный сервис',
    text: 'Сертифицированные механики и оригинальные запчасти в наличии.'
  },
  {
    icon: '🚚',
    title: 'Доставка по РФ',
    text: 'Отправим технику в любой регион транспортной компанией с нашим контролем.'
  },
  {
    icon: '💳',
    title: 'Рассрочка и trade-in',
    text: 'Выгодные условия рассрочки, кредит от банков-партнёров, обмен старой техники.'
  }
]

function formatDate(d: string): string {
  return new Date(d).toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' })
}

function subscribe() {
  subscribed.value = true
  email.value = ''
  setTimeout(() => (subscribed.value = false), 3000)
}

function scrollPromo(dir: number) {
  const el = promoTrack.value
  if (!el) return
  const cardW = el.children[0]?.clientWidth ?? 360
  const gap = 20
  el.scrollBy({ left: (cardW + gap) * dir, behavior: 'smooth' })
}

function scrollNews(dir: number) {
  const el = newsTrack.value
  if (!el) return
  const cardW = el.children[0]?.clientWidth ?? 360
  const gap = 20
  el.scrollBy({ left: (cardW + gap) * dir, behavior: 'smooth' })
}

function goToPromo(i: number) {
  const el = promoTrack.value
  if (!el) return
  const cardW = el.children[0]?.clientWidth ?? 360
  const gap = 20
  el.scrollTo({ left: (cardW + gap) * i, behavior: 'smooth' })
}

function goToNews(i: number) {
  const el = newsTrack.value
  if (!el) return
  const cardW = el.children[0]?.clientWidth ?? 360
  const gap = 20
  el.scrollTo({ left: (cardW + gap) * i, behavior: 'smooth' })
}

function onPromoScroll() {
  const el = promoTrack.value
  if (!el) return
  const cardW = el.children[0]?.clientWidth ?? 360
  const gap = 20
  promoIndex.value = Math.round(el.scrollLeft / (cardW + gap))
}

function onNewsScroll() {
  const el = newsTrack.value
  if (!el) return
  const cardW = el.children[0]?.clientWidth ?? 360
  const gap = 20
  newsIndex.value = Math.round(el.scrollLeft / (cardW + gap))
}

onMounted(async () => {
  catalog.fetchCategories()
  catalog.fetchProducts({ page: 1, limit: 8 })

  const [promoRes, newsRes] = await Promise.all([
    apiFetch<any[]>('/api/promotions'),
    apiFetch<any[]>('/api/news')
  ])

  if (promoRes.success && promoRes.data) {
    const sorted = [...promoRes.data].sort(
      (a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
    )
    promos.value = sorted
  }

  if (newsRes.success && newsRes.data) {
    const sorted = [...newsRes.data].sort(
      (a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
    )
    news.value = sorted
  }
})
</script>

<style scoped>
.home {
  padding-bottom: 40px;
}

.promo {
  padding: 64px 0 48px;
  background: radial-gradient(
      120% 70% at 90% 0%,
      rgba(255, 90, 31, 0.18),
      transparent 55%
    ),
    linear-gradient(180deg, #0e0e10 0%, #131316 100%);
  border-bottom: 1px solid var(--color-border);
}

.promo__head {
  max-width: 720px;
  margin-bottom: 40px;
}

.promo__head h1 {
  font-size: clamp(32px, 4vw, 48px);
  font-weight: 800;
  letter-spacing: -0.02em;
  margin: 14px 0 12px;
}

.promo__head p {
  color: var(--color-muted);
  font-size: 17px;
}

.promo__all-link {
  display: inline-block;
  margin-top: 24px;
  color: var(--color-accent);
  font-weight: 600;
  font-size: 14px;
  text-decoration: none;
}

/* Карусель */
.carousel-wrapper {
  position: relative;
}

.carousel-track {
  display: flex;
  gap: 20px;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  padding-bottom: 16px;
}

.carousel-track::-webkit-scrollbar {
  display: none;
}

.carousel-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  border: 2px solid var(--color-border);
  background: var(--color-surface);
  color: var(--color-text);
  font-size: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease, border-color 0.2s ease;
  box-shadow: 0 2px 12px rgba(0,0,0,0.4);
}

.carousel-btn:hover {
  background: var(--color-accent);
  color: #fff;
  border-color: var(--color-accent);
}

.carousel-btn:disabled {
  opacity: 0.25;
  cursor: default;
  pointer-events: none;
}

.carousel-btn--prev {
  left: -22px;
}

.carousel-btn--next {
  right: -22px;
}

.carousel-dots {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-top: 12px;
}

.carousel-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--color-border);
  cursor: pointer;
  transition: background 0.2s, width 0.3s;
}

.carousel-dot--active {
  background: var(--color-accent);
  width: 24px;
  border-radius: 4px;
}

/* Карточка акции/новости */
.promo-card {
  flex: 0 0 360px;
  scroll-snap-align: start;
  position: relative;
  border-radius: var(--radius-lg);
  min-height: 300px;
  overflow: hidden;
  background-size: cover;
  background-position: center;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  transition: transform 0.2s ease;
}

.promo-card:hover {
  transform: translateY(-4px);
}

.promo-card__overlay {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  gap: 10px;
  padding: 28px;
  background: linear-gradient(0deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.1) 70%, transparent 100%);
  color: #fff;
}

.promo-card__badge--discount {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 3;
  background: var(--color-accent);
  color: #fff;
  padding: 5px 12px;
  font-size: 13px;
  font-weight: 700;
  border-radius: 999px;
}

.promo-card__badge--news {
  align-self: flex-start;
  background: rgba(255,255,255,0.15);
  font-size: 11px;
  font-weight: 500;
  padding: 4px 10px;
  border-radius: 999px;
}

.promo-card__title {
  font-size: 20px;
  font-weight: 700;
  line-height: 1.2;
  margin: 0;
}

/* Секции */
.benefits__grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 20px;
}

.benefit {
  padding: 24px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
}

.benefit__icon {
  font-size: 26px;
  margin-bottom: 14px;
}

.benefit h3 {
  font-size: 16px;
  margin-bottom: 8px;
}

.benefit p {
  color: var(--color-muted);
  font-size: 14px;
}

.section__head {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  margin-bottom: 24px;
}

.section__head .section__title {
  margin-bottom: 0;
}

.section__link {
  color: var(--color-accent);
  font-weight: 600;
  font-size: 14px;
}

.category-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: 24px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 140px;
  transition: border-color 0.2s ease, transform 0.2s ease;
}

.category-card:hover {
  border-color: var(--color-accent);
  transform: translateY(-3px);
}

.category-card span {
  color: var(--color-accent);
  font-weight: 600;
  font-size: 14px;
  margin-top: 12px;
}

.newsletter {
  margin: 64px 0 0;
  padding: 48px 0;
  background: linear-gradient(135deg, #161618 0%, #1f1f23 100%);
  border-top: 1px solid var(--color-border);
  border-bottom: 1px solid var(--color-border);
}

.newsletter__inner {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  align-items: center;
}

.newsletter__text h2 {
  font-size: 28px;
  margin-bottom: 8px;
}

.newsletter__text p {
  color: var(--color-muted);
}

.newsletter__form {
  display: flex;
  gap: 10px;
}

.newsletter__input {
  flex: 1;
}

@media (max-width: 900px) {
  .promo-card {
    flex: 0 0 280px;
    min-height: 240px;
  }
  .carousel-btn {
    display: none;
  }
  .newsletter__inner {
    grid-template-columns: 1fr;
  }
}
</style>