import { defineStore } from 'pinia'
import { computed, ref, watch } from 'vue'

interface CartItem {
  product: any
  quantity: number
}

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>(JSON.parse(localStorage.getItem('cart') || '[]'))

  const count = computed(() => items.value.reduce((sum, i) => sum + i.quantity, 0))
  const total = computed(() =>
    items.value.reduce((sum, i) => sum + i.product.price * i.quantity, 0)
  )

  watch(
    items,
    (value) => localStorage.setItem('cart', JSON.stringify(value)),
    { deep: true }
  )

  function add(product: any) {
    const existing = items.value.find((i) => i.product.id === product.id)
    if (existing) existing.quantity++
    else items.value.push({ product, quantity: 1 })
  }

  function remove(productId: number | string) {
    items.value = items.value.filter((i) => i.product.id !== productId)
  }

  function clear() {
    items.value = []
  }

  return { items, count, total, add, remove, clear }
})
