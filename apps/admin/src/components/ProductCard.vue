<template>
  <div class="admin-product-card card">
    <div class="admin-product-card__title">{{ product.title }}</div>
    <div class="admin-product-card__meta">
      <span>{{ product.category || '—' }}</span>
      <span>{{ formatPrice(product.price) }} ₽</span>
    </div>
    <div class="admin-product-card__actions">
      <router-link class="btn" :to="`/products/${product.id}`">Редактировать</router-link>
      <button class="btn" @click="$emit('remove', product)">Удалить</button>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{ product: any }>()
defineEmits<{ (e: 'remove', product: any): void }>()
const formatPrice = (price: number) => new Intl.NumberFormat('ru-RU').format(price)
</script>

<style scoped>
.admin-product-card {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.admin-product-card__title {
  font-weight: 600;
  font-size: 15px;
}
.admin-product-card__meta {
  display: flex;
  justify-content: space-between;
  color: var(--color-muted);
  font-size: 13px;
}
.admin-product-card__actions {
  display: flex;
  gap: 8px;
}
</style>
