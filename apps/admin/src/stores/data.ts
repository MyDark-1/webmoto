import { defineStore } from 'pinia'
import { ref } from 'vue'
import { apiFetch } from '../utils/api'

export const useAdminDataStore = defineStore('admin-data', () => {
  const products = ref<any[]>([])
  const orders = ref<any[]>([])
  const news = ref<any[]>([])
  const promotions = ref<any[]>([])
  const feedback = ref<any[]>([])

  async function load<T>(url: string, target: { value: T[] }) {
    const res = await apiFetch<any>(url)
    if (res.success) {
      // поддержка пагинированного ответа { items, total, page, pages }
      if (res.data && res.data.items) {
        target.value = res.data.items
      } else if (Array.isArray(res.data)) {
        target.value = res.data
      }
    }
  }

  return {
    products,
    orders,
    news,
    promotions,
    feedback,
    fetchProducts: () => load('/api/products?all=1', products),
    fetchOrders: () => load('/api/orders/all', orders),
    fetchNews: () => load('/api/news', news),
    fetchPromotions: () => load('/api/promotions', promotions),
    fetchFeedback: () => load('/api/feedback', feedback)
  }
})
