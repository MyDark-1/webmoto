<template>
  <div class="news">
    <h1>Новости</h1>
    
    <div class="news-grid">
      <div v-for="newsItem in news" :key="newsItem.id" class="news-card">
        <img :src="newsItem.image" :alt="newsItem.title">
        <div class="news-content">
          <h3>{{ newsItem.title }}</h3>
          <p>{{ newsItem.content.substring(0, 150) }}...</p>
          <span class="date">{{ formatDate(newsItem.created_at) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'News',
  setup() {
    const news = ref([])

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('ru-RU')
    }

    onMounted(async () => {
      const response = await fetch('/api/news')
      const data = await response.json()
      if (data.success) {
        news.value = data.data
      }
    })

    return {
      news,
      formatDate
    }
  }
}
</script>

<style scoped>
.news {
  padding: 20px;
}

.news h1 {
  color: #ff6600;
  margin-bottom: 20px;
}

.news-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.news-card {
  background: #1a1a1a;
  border-radius: 8px;
  overflow: hidden;
}

.news-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.news-content {
  padding: 20px;
}

.news-content h3 {
  margin-bottom: 10px;
  color: #fff;
}

.news-content p {
  color: #888;
  margin-bottom: 10px;
  line-height: 1.5;
}

.date {
  color: #ff6600;
  font-size: 14px;
}
</style>