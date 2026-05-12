import { defineStore } from 'pinia'
import { ref } from 'vue'
import { apiFetch } from '../utils/api'

export const useCatalogStore = defineStore('catalog', () => {
  const products = ref<any[]>([])
  const categories = ref<any[]>([])
  const loading = ref(false)
  const total = ref(0)
  const currentPage = ref(1)
  const totalPages = ref(0)
  const charFilters = ref<Record<number, string>>({})       // Выбранные фильтры
  const charDefs = ref<any[]>([])                           // Характеристики с values для текущей категории

  async function fetchProducts(params?: { page?: number; limit?: number; category?: string; all?: boolean }) {
    loading.value = true
    const query = new URLSearchParams()
    if (params?.page) query.set('page', String(params.page))
    if (params?.limit) query.set('limit', String(params.limit))
    if (params?.category) query.set('category', params.category)
    if (params?.all) query.set('all', '1')

    // Добавляем фильтры по характеристикам
    for (const [charId, value] of Object.entries(charFilters.value)) {
      if (value) {
        query.set(`chars[${charId}]`, value)
      }
    }

    const url = '/api/products' + (query.toString() ? '?' + query.toString() : '')
    const data = await apiFetch<any>(url)

    if (data.success && data.data) {
      if (data.data.items) {
        products.value = data.data.items
        total.value = data.data.total
        currentPage.value = data.data.page
        totalPages.value = data.data.pages
      } else if (Array.isArray(data.data)) {
        products.value = data.data
        total.value = data.data.length
        currentPage.value = 1
        totalPages.value = 1
      }
    }
    loading.value = false
  }

  async function fetchCategories() {
    const data = await apiFetch<any[]>('/api/categories')
    if (data.success && data.data) categories.value = data.data
  }

  // Загрузить характеристики с доступными значениями для фильтрации
  async function fetchCharDefs(categorySlug?: string) {
    if (!categorySlug) {
      charDefs.value = []
      return
    }
    // Находим id категории по slug
    const cat = categories.value.find(c => c.slug === categorySlug)
    if (!cat) {
      charDefs.value = []
      return
    }
    const data = await apiFetch<any>(`/api/characteristics?category_id=${cat.id}&with_values=1`)
    if (data.success && data.data) {
      charDefs.value = data.data
    }
  }

  // Сбросить фильтры
  function resetFilters() {
    charFilters.value = {}
  }

  // Установить фильтр по характеристике
  function setCharFilter(charId: number, value: string) {
    if (value) {
      charFilters.value[charId] = value
    } else {
      delete charFilters.value[charId]
    }
  }

  return { products, categories, loading, total, currentPage, totalPages, charFilters, charDefs, fetchProducts, fetchCategories, fetchCharDefs, resetFilters, setCharFilter }
})