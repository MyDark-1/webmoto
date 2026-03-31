<template>
  <div class="promotions">
    <h1>Акции</h1>
    
    <div class="promotions-grid">
      <div v-for="promotion in promotions" :key="promotion.id" class="promotion-card">
        <img :src="promotion.image" :alt="promotion.title">
        <div class="promotion-content">
          <h3>{{ promotion.title }}</h3>
          <p>{{ promotion.content }}</p>
          <div class="discount">Скидка {{ promotion.discount }}%</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'Promotions',
  setup() {
    const promotions = ref([])

    onMounted(async () => {
      const response = await fetch('/api/promotions')
      const data = await response.json()
      if (data.success) {
        promotions.value = data.data
      }
    })

    return {
      promotions
    }
  }
}
</script>

<style scoped>
.promotions {
  padding: 20px;
}

.promotions h1 {
  color: #ff6600;
  margin-bottom: 20px;
}

.promotions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.promotion-card {
  background: #1a1a1a;
  border-radius: 8px;
  overflow: hidden;
}

.promotion-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.promotion-content {
  padding: 20px;
}

.promotion-content h3 {
  margin-bottom: 10px;
  color: #fff;
}

.promotion-content p {
  color: #888;
  margin-bottom: 10px;
  line-height: 1.5;
}

.discount {
  background: #ff6600;
  color: #fff;
  padding: 5px 10px;
  border-radius: 4px;
  display: inline-block;
  font-weight: bold;
}
</style>