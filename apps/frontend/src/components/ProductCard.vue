<template>
  <router-link :to="`/products/${product.id}`" class="product-card">
    <div class="product-card__media">
      <img :src="product.image" :alt="product.title" v-if="product.image" />
      <div v-else class="product-card__placeholder">RADAR</div>
      <span v-if="product.badge" class="product-card__badge">{{ product.badge }}</span>
    </div>
    <div class="product-card__body">
      <div class="product-card__category" v-if="product.category">
        {{ product.category }}
      </div>
      <h3 class="product-card__title">{{ product.title }}</h3>
      <div class="product-card__footer">
        <span class="product-card__price">{{ formatPrice(product.price) }} ₽</span>
        <button class="product-card__buy" @click.prevent="$emit('buy', product)">
          В корзину
        </button>
      </div>
    </div>
  </router-link>
</template>

<script setup lang="ts">
defineProps<{ product: any }>()
defineEmits<{ (e: 'buy', product: any): void }>()

const formatPrice = (price: number) => new Intl.NumberFormat('ru-RU').format(price)
</script>

<style scoped>
.product-card {
  display: flex;
  flex-direction: column;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  overflow: hidden;
  transition: transform 0.2s ease, border-color 0.2s ease;
  color: inherit;
}
.product-card:hover {
  transform: translateY(-4px);
  border-color: var(--color-accent);
}
.product-card__media {
  position: relative;
  aspect-ratio: 4 / 3;
  background: var(--color-surface-2);
  display: flex;
  align-items: center;
  justify-content: center;
}
.product-card__media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.product-card__placeholder {
  font-weight: 800;
  letter-spacing: 0.3em;
  color: var(--color-border);
  font-size: 18px;
}
.product-card__badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: var(--color-accent);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.08em;
  padding: 4px 10px;
  border-radius: 999px;
  text-transform: uppercase;
}
.product-card__body {
  padding: 16px 18px 18px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex: 1;
}
.product-card__category {
  color: var(--color-muted);
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}
.product-card__title {
  font-size: 16px;
  font-weight: 600;
  color: var(--color-text);
  min-height: 44px;
}
.product-card__footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}
.product-card__price {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text);
}
.product-card__buy {
  background: transparent;
  color: var(--color-accent);
  border: 1px solid var(--color-accent);
  padding: 7px 12px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 13px;
}
.product-card__buy:hover {
  background: var(--color-accent);
  color: #fff;
}
</style>
