import { defineStore } from 'pinia'
import { ref } from 'vue'
import { apiFetch } from '../utils/api'

export const useCatalogStore = defineStore('catalog', () => {
  const products = ref<any[]>([])
  const categories = ref<any[]>([])
  const loading = ref(false)

  async function fetchProducts() {
    loading.value = true
    const data = await apiFetch<any[]>('/api/products')
    if (data.success && data.data) products.value = data.data
    loading.value = false
  }

  async function fetchCategories() {
    const data = await apiFetch<any[]>('/api/categories')
    if (data.success && data.data) categories.value = data.data
  }

  return { products, categories, loading, fetchProducts, fetchCategories }
})
