<template>
  <div class="home">
    <section class="promo">
      <div class="container promo__inner">
        <div class="promo__head">
          <span class="tag">Акции и новости</span>
          <h1>Скорость, драйв и выгодные предложения</h1>
          <p>Актуальные акции салона и свежие новости о новинках экстремальной техники.</p>
        </div>

        <div class="promo__grid">
          <article
            v-for="(item, index) in promos"
            :key="item.id"
            class="promo-card"
            :class="{ 'promo-card--featured': index === 0 }"
          >
            <span class="promo-card__badge">
              {{ item.discount ? '−' + item.discount + '%' : 'Акция' }}
            </span>
            <h3>{{ item.title }}</h3>
            <p>{{ item.content }}</p>
            <router-link to="/promotions" class="promo-card__link">
              Подробнее →
            </router-link>
          </article>
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

function subscribe() {
  subscribed.value = true
  email.value = ''
  setTimeout(() => (subscribed.value = false), 3000)
}

onMounted(async () => {
  catalog.fetchCategories()
  catalog.fetchProducts({ page: 1, limit: 8 })

  const data = await apiFetch<any[]>('/api/promotions')
  if (data.success && data.data) {
    // сортируем по created_at — новые первыми, берём не больше 3
    const sorted = [...data.data].sort(
      (a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
    )
    promos.value = sorted.slice(0, 3)
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

.promo__grid {
  display: grid;
  grid-template-columns: 1.4fr 1fr 1fr;
  gap: 20px;
}

.promo-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: 28px;
  display: flex;
  flex-direction: column;
  gap: 14px;
  min-height: 240px;
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.promo-card:hover {
  transform: translateY(-4px);
  border-color: var(--color-accent);
}

.promo-card--featured {
  background: linear-gradient(135deg, #ff5a1f 0%, #ffae00 100%);
  border-color: transparent;
  color: #0e0e10;
}

.promo-card--featured .promo-card__badge {
  background: rgba(14, 14, 16, 0.15);
  color: #0e0e10;
}

.promo-card--featured .promo-card__link {
  color: #0e0e10;
}

.promo-card__badge {
  align-self: flex-start;
  background: rgba(255, 90, 31, 0.15);
  color: var(--color-accent);
  padding: 4px 10px;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-weight: 700;
  border-radius: 999px;
}

.promo-card h3 {
  font-size: 22px;
  font-weight: 700;
  line-height: 1.2;
}

.promo-card p {
  opacity: 0.85;
  font-size: 14px;
  flex: 1;
}

.promo-card__link {
  color: var(--color-accent);
  font-weight: 600;
  font-size: 14px;
}

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
  .promo__grid {
    grid-template-columns: 1fr;
  }
  .newsletter__inner {
    grid-template-columns: 1fr;
  }
}
</style>
