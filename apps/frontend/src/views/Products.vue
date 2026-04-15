<template>
  <div class="products container">
    <div class="products__head">
      <h1>Каталог</h1>
      <select v-model="selectedCategory" class="input products__filter">
        <option value="">Все категории</option>
        <option v-for="category in catalog.categories" :key="category.id" :value="category.slug">
          {{ category.name }}
        </option>
      </select>
    </div>

    <div class="grid grid--cards">
      <ProductCard
        v-for="product in filteredProducts"
        :key="product.id"
        :product="product"
        @buy="cart.add($event)"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import ProductCard from '../components/ProductCard.vue'
import { useCatalogStore } from '../stores/catalog'
import { useCartStore } from '../stores/cart'

const route = useRoute()
const catalog = useCatalogStore()
const cart = useCartStore()
const selectedCategory = ref(route.query.category || '')

const filteredProducts = computed(() => {
  if (!selectedCategory.value) return catalog.products
  return catalog.products.filter((p) => p.category_slug === selectedCategory.value)
})

onMounted(() => {
  catalog.fetchCategories()
  catalog.fetchProducts()
})
</script>

<style scoped>
.products {
  padding: 40px 20px;
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
</style>
