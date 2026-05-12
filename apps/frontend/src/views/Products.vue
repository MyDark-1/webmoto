<template>
  <div class="products">
    <div class="container">
      <div class="products__head">
        <h1>Каталог</h1>
        <select v-model="selectedCategory" class="input products__filter" @change="onCategoryChange">
          <option value="">Все категории</option>
          <option v-for="category in catalog.categories" :key="category.id" :value="category.slug">
            {{ category.name }}
          </option>
        </select>
      </div>

      <div class="products__layout">
        <!-- Боковая панель фильтров -->
        <aside v-if="catalog.charDefs.length > 0" class="filters">
          <h3 class="filters__title">Фильтры</h3>
          <div
            v-for="charDef in catalog.charDefs"
            :key="charDef.id"
            class="filters__group"
          >
            <label class="filters__label">{{ charDef.name }}</label>
            <select
              class="input filters__select"
              :value="catalog.charFilters[charDef.id] || ''"
              @change="onCharFilterChange(charDef.id, $event.target.value)"
            >
              <option value="">Все</option>
              <option
                v-for="val in charDef.values"
                :key="val"
                :value="val"
              >{{ val }}</option>
            </select>
          </div>
          <button
            v-if="Object.keys(catalog.charFilters).length > 0"
            class="btn btn--small filters__clear"
            @click="clearFilters"
          >
            Сбросить фильтры
          </button>
        </aside>

        <!-- Сетка товаров -->
        <div class="products__content">
          <div v-if="catalog.loading" class="products__loading">Загрузка...</div>

          <div v-else-if="catalog.products.length === 0" class="products__empty">
            Товары не найдены
          </div>

          <div v-else class="grid grid--cards">
            <ProductCard
              v-for="product in catalog.products"
              :key="product.id"
              :product="product"
              @buy="cart.add($event)"
            />
          </div>

          <div v-if="catalog.totalPages > 1" class="pagination">
            <button
              class="pagination__btn"
              :disabled="catalog.currentPage <= 1"
              @click="goToPage(catalog.currentPage - 1)"
            >
              ← Назад
            </button>
            <span class="pagination__info">
              {{ catalog.currentPage }} из {{ catalog.totalPages }}
            </span>
            <button
              class="pagination__btn"
              :disabled="catalog.currentPage >= catalog.totalPages"
              @click="goToPage(catalog.currentPage + 1)"
            >
              Вперед →
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ProductCard from '../components/ProductCard.vue'
import { useCatalogStore } from '../stores/catalog'
import { useCartStore } from '../stores/cart'

const route = useRoute()
const router = useRouter()
const catalog = useCatalogStore()
const cart = useCartStore()
const selectedCategory = ref(route.query.category || '')

function loadProducts() {
  catalog.fetchProducts({
    page: 1,
    limit: 8,
    category: selectedCategory.value || undefined,
  })
}

function onCategoryChange() {
  catalog.resetFilters()
  router.replace({ query: { category: selectedCategory.value || undefined } })
  catalog.fetchCharDefs(selectedCategory.value || undefined)
  loadProducts()
}

function onCharFilterChange(charId, value) {
  catalog.setCharFilter(charId, value)
  loadProducts()
}

function clearFilters() {
  catalog.resetFilters()
  loadProducts()
}

function goToPage(page) {
  catalog.fetchProducts({
    page,
    limit: 8,
    category: selectedCategory.value || undefined,
  })
}

onMounted(async () => {
  catalog.fetchCategories()
  await catalog.fetchCharDefs(selectedCategory.value || undefined)
  loadProducts()
})
</script>

<style scoped>
.products {
  padding: 40px 0;
}
.products__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
  gap: 20px;
}
.products__head h1 {
  font-size: 28px;
  font-weight: 700;
}
.products__filter {
  max-width: 240px;
}
.products__layout {
  display: flex;
  gap: 32px;
}
.products__content {
  flex: 1;
  min-width: 0;
}
.products__loading {
  text-align: center;
  padding: 60px 0;
  color: var(--color-muted);
  font-size: 16px;
}
.products__empty {
  text-align: center;
  padding: 60px 0;
  color: var(--color-muted);
  font-size: 16px;
}

/* Фильтры */
.filters {
  width: 240px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.filters__title {
  font-size: 16px;
  font-weight: 700;
  margin: 0;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--color-border);
}
.filters__group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.filters__label {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-muted);
}
.filters__select {
  padding: 8px 10px;
  font-size: 14px;
}
.filters__clear {
  align-self: flex-start;
  background: transparent;
  border: 1px solid var(--color-border);
  color: var(--color-muted);
  padding: 8px 16px;
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 13px;
  transition: border-color 0.2s, color 0.2s;
}
.filters__clear:hover {
  border-color: var(--color-accent);
  color: var(--color-text);
}

/* Пагинация */
.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  margin-top: 40px;
  padding: 20px 0;
}
.pagination__btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 20px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  color: var(--color-text);
  font-size: 14px;
  font-weight: 500;
  transition: border-color 0.2s, background 0.2s;
}
.pagination__btn:hover:not(:disabled) {
  border-color: var(--color-accent);
  background: var(--color-surface-2);
}
.pagination__btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.pagination__info {
  font-size: 14px;
  color: var(--color-muted);
}

@media (max-width: 768px) {
  .products__layout {
    flex-direction: column;
  }
  .filters {
    width: 100%;
  }
}
</style>