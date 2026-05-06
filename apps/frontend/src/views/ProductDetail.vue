<template>
  <section class="product-detail">
    <div v-if="loading" class="product-detail__status">Загрузка...</div>
    <div v-else-if="!product" class="product-detail__status">
      <p>Товар не найден.</p>
      <router-link to="/products" class="btn btn--primary">В каталог</router-link>
    </div>
    <template v-else>
      <!-- Хлебные крошки -->
      <div class="product-detail__breadcrumbs">
        <div class="container">
          <router-link to="/">Главная</router-link>
          <span class="product-detail__sep">/</span>
          <router-link to="/products">Каталог</router-link>
          <span v-if="product.category_name" class="product-detail__sep">/</span>
          <span v-if="product.category_name" class="product-detail__current">{{ product.category_name }}</span>
        </div>
      </div>

      <div class="container product-detail__main">
        <!-- Левая колонка — фото -->
        <div class="product-detail__gallery">
          <div class="product-detail__image-main">
            <img
              v-if="product.image"
              :src="activeImage"
              :alt="product.title"
              class="product-detail__img"
            />
            <div v-else class="product-detail__placeholder">RADAR EXTREME</div>
          </div>
          <div v-if="images.length > 1" class="product-detail__thumbs">
            <img
              v-for="(img, i) in images"
              :key="i"
              :src="img"
              :class="{ 'product-detail__thumb--active': activeImage === img }"
              class="product-detail__thumb"
              @click="activeImage = img"
            />
          </div>
        </div>

        <!-- Правая колонка — информация -->
        <div class="product-detail__info">
          <h1 class="product-detail__title">{{ product.title }}</h1>
          <div class="product-detail__meta">
            <span v-if="product.article" class="product-detail__article">Арт. {{ product.article }}</span>
            <span v-if="product.category_name" class="product-detail__category">{{ product.category_name }}</span>
          </div>

          <!-- Цена -->
          <div class="product-detail__prices">
            <span v-if="product.old_price" class="product-detail__old-price">
              {{ formatPrice(product.old_price) }} ₽
            </span>
            <strong class="product-detail__price">{{ formatPrice(product.price) }} ₽</strong>
          </div>

          <!-- Наличие -->
          <div class="product-detail__stock" :class="`product-detail__stock--${product.stock_status || 'in_stock'}`">
            <svg class="product-detail__stock-icon" viewBox="0 0 16 16" fill="currentColor" width="16" height="16">
              <circle cx="8" cy="8" r="6" />
            </svg>
            <span>{{ stockLabel }}</span>
          </div>

          <!-- Кнопки -->
          <div class="product-detail__actions">
            <button class="product-detail__buy-btn" @click="addToCart">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
              </svg>
              В корзину
            </button>
            <router-link to="/cart" class="product-detail__cart-link">Перейти в корзину</router-link>
          </div>

          <!-- Краткие преимущества -->
          <ul class="product-detail__features">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              Гарантия от производителя
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              Доставка по РФ от 1 дня
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              Оплата при получении
            </li>
          </ul>
        </div>
      </div>

      <!-- Описание -->
      <div v-if="product.description" class="product-detail__section container">
        <h2 class="product-detail__section-title">Описание</h2>
        <p class="product-detail__desc-text">{{ product.description }}</p>
      </div>

      <!-- Характеристики -->
      <div v-if="charsLines.length > 0" class="product-detail__section container">
        <h2 class="product-detail__section-title">Характеристики</h2>
        <table class="product-detail__chars">
          <tr v-for="(line, i) in charsLines" :key="i">
            <td class="product-detail__chars-key">{{ line.key }}</td>
            <td class="product-detail__chars-val">{{ line.val }}</td>
          </tr>
        </table>
      </div>

      <!-- Спецификации (разделы) -->
      <div v-if="specs.length > 0" class="product-detail__section container">
        <h2 class="product-detail__section-title">Спецификации</h2>
        <div v-for="(section, i) in specs" :key="i" class="product-detail__spec">
          <h3 class="product-detail__spec-title">{{ section.title }}</h3>
          <table class="product-detail__chars">
            <tr v-for="(val, key) in section.items" :key="key">
              <td class="product-detail__chars-key">{{ key }}</td>
              <td class="product-detail__chars-val">{{ val }}</td>
            </tr>
          </table>
        </div>
      </div>
    </template>
  </section>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { apiFetch } from '../utils/api'
import { formatPrice } from '../utils/format'
import { notifySuccess } from '../utils/notify'

interface SpecSection {
  title: string
  items: Record<string, string>
}

const cart = useCartStore()
const route = useRoute()
const product = ref<any>(null)
const loading = ref(true)
const activeImage = ref('')

const stockLabel = computed(() => {
  const map: Record<string, string> = {
    in_stock: 'В наличии',
    out_of_stock: 'Нет в наличии',
    on_order: 'Под заказ',
  }
  return map[product.value?.stock_status || 'in_stock'] || 'В наличии'
})

const images = computed(() => {
  const main = product.value?.image
  if (!main) return []
  // если в image передано несколько ссылок через запятую или массив
  if (Array.isArray(product.value.images)) return product.value.images
  const alt = product.value?.images_extra
  if (alt) {
    if (Array.isArray(alt)) return [main, ...alt]
    if (typeof alt === 'string') return [main, ...alt.split(',').map((s: string) => s.trim()).filter(Boolean)]
  }
  return [main]
})

const charsLines = computed(() => {
  const text = product.value?.characteristics
  if (!text) return []
  return text
    .split('\n')
    .map((line: string) => line.trim())
    .filter(Boolean)
    .map((line: string) => {
      const sep = line.indexOf(':')
      if (sep === -1) return { key: line, val: '' }
      return { key: line.slice(0, sep).trim(), val: line.slice(sep + 1).trim() }
    })
})

const specs = computed(() => {
  const raw = product.value?.specifications
  if (!raw) return []
  try {
    const parsed = JSON.parse(raw)
    if (Array.isArray(parsed)) return parsed as SpecSection[]
    return []
  } catch {
    return []
  }
})

function addToCart() {
  if (!product.value) return
  cart.add(product.value)
  notifySuccess('Товар добавлен в корзину')
}

onMounted(async () => {
  const data = await apiFetch<any>(`/api/products/${route.params.id}`)
  if (data.success) {
    product.value = data.data
    activeImage.value = data.data.image || ''
  }
  loading.value = false
})
</script>

<style scoped>
/* ───── статусы ───── */
.product-detail__status {
  text-align: center;
  padding: 80px 20px;
  color: var(--color-muted);
}
.product-detail__status p {
  margin-bottom: 16px;
}

/* ───── хлебные крошки ───── */
.product-detail__breadcrumbs {
  border-bottom: 1px solid var(--color-border);
  padding: 12px 0;
  margin-bottom: 32px;
}
.product-detail__breadcrumbs .container {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--color-muted);
}
.product-detail__breadcrumbs a {
  color: var(--color-accent);
  font-weight: 500;
  transition: opacity 0.2s;
}
.product-detail__breadcrumbs a:hover {
  opacity: 0.75;
}
.product-detail__sep {
  color: var(--color-border);
}
.product-detail__current {
  color: var(--color-text);
}

/* ───── основная сетка ───── */
.product-detail__main {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 48px;
  margin-bottom: 48px;
}

/* ───── галерея ───── */
.product-detail__gallery {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.product-detail__image-main {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  aspect-ratio: 4 / 3;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.product-detail__img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}
.product-detail__placeholder {
  font-weight: 800;
  letter-spacing: 0.3em;
  color: var(--color-border);
  font-size: 18px;
}
.product-detail__thumbs {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding-bottom: 4px;
}
.product-detail__thumb {
  width: 72px;
  height: 56px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid transparent;
  cursor: pointer;
  background: var(--color-surface);
  flex-shrink: 0;
  transition: border-color 0.2s;
}
.product-detail__thumb:hover,
.product-detail__thumb--active {
  border-color: var(--color-accent);
}

/* ───── информация ───── */
.product-detail__title {
  font-size: 28px;
  font-weight: 700;
  line-height: 1.25;
  margin-bottom: 8px;
  letter-spacing: -0.02em;
}
.product-detail__meta {
  display: flex;
  align-items: center;
  gap: 14px;
  font-size: 13px;
  color: var(--color-muted);
  margin-bottom: 20px;
}
.product-detail__article {
  background: var(--color-surface-2);
  padding: 2px 10px;
  border-radius: 4px;
}
.product-detail__category {
  color: var(--color-accent);
  font-weight: 500;
}

/* ───── цена ───── */
.product-detail__prices {
  display: flex;
  align-items: baseline;
  gap: 12px;
  margin-bottom: 16px;
}
.product-detail__price {
  font-size: 32px;
  font-weight: 800;
  color: var(--color-accent);
}
.product-detail__old-price {
  font-size: 20px;
  color: var(--color-muted);
  text-decoration: line-through;
}

/* ───── наличие ───── */
.product-detail__stock {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 24px;
}
.product-detail__stock-icon {
  flex-shrink: 0;
}
.product-detail__stock--in_stock {
  background: rgba(46, 204, 113, 0.12);
  color: #2ecc71;
}
.product-detail__stock--out_of_stock {
  background: rgba(231, 76, 60, 0.12);
  color: #e74c3c;
}
.product-detail__stock--on_order {
  background: rgba(255, 174, 0, 0.12);
  color: #ffae00;
}

/* ───── кнопки ───── */
.product-detail__actions {
  display: flex;
  gap: 14px;
  align-items: center;
  margin-bottom: 24px;
}
.product-detail__buy-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 28px;
  border: none;
  border-radius: 12px;
  background: var(--color-accent);
  color: #fff;
  font-weight: 700;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s;
}
.product-detail__buy-btn:hover {
  background: #ff7a3d;
  transform: translateY(-1px);
}
.product-detail__cart-link {
  color: var(--color-accent);
  font-weight: 600;
  font-size: 14px;
  transition: opacity 0.2s;
}
.product-detail__cart-link:hover {
  opacity: 0.75;
}

/* ───── преимущества ───── */
.product-detail__features {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.product-detail__features li {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: var(--color-muted);
}
.product-detail__features li svg {
  color: #2ecc71;
  flex-shrink: 0;
}

/* ───── секции ───── */
.product-detail__section {
  border-top: 1px solid var(--color-border);
  padding: 32px 24px 24px;
  margin-bottom: 0;
}
.product-detail__section-title {
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 18px;
}
.product-detail__desc-text {
  color: var(--color-muted);
  font-size: 15px;
  line-height: 1.8;
}

/* ───── таблица характеристик ───── */
.product-detail__chars {
  width: 100%;
  max-width: 700px;
  border-collapse: collapse;
}
.product-detail__chars tr {
  border-bottom: 1px solid var(--color-border);
}
.product-detail__chars tr:last-child {
  border-bottom: none;
}
.product-detail__chars td {
  padding: 10px 14px;
  font-size: 14px;
  line-height: 1.5;
}
.product-detail__chars-key {
  color: var(--color-muted);
  width: 45%;
  font-weight: 500;
}
.product-detail__chars-val {
  color: var(--color-text);
}

/* ───── спецификации ───── */
.product-detail__spec {
  margin-bottom: 24px;
}
.product-detail__spec-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-accent);
  margin-bottom: 12px;
}

/* ───── медиа ───── */
@media (max-width: 900px) {
  .product-detail__main {
    grid-template-columns: 1fr;
    gap: 28px;
  }
}
</style>