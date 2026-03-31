import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { createStore } from 'vuex'
import App from './App.vue'

// Импорт компонентов
import Dashboard from './views/Dashboard.vue'
import Products from './views/Products.vue'
import ProductEdit from './views/ProductEdit.vue'
import Orders from './views/Orders.vue'
import News from './views/News.vue'
import Promotions from './views/Promotions.vue'
import Feedback from './views/Feedback.vue'
import Login from './views/Login.vue'

// Маршруты
const routes = [
  { path: '/', component: Dashboard },
  { path: '/products', component: Products },
  { path: '/products/:id', component: ProductEdit },
  { path: '/orders', component: Orders },
  { path: '/news', component: News },
  { path: '/promotions', component: Promotions },
  { path: '/feedback', component: Feedback },
  { path: '/login', component: Login }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Vuex store
const store = createStore({
  state() {
    return {
      user: null,
      token: localStorage.getItem('admin_token'),
      products: [],
      orders: [],
      news: [],
      promotions: [],
      feedback: []
    }
  },
  mutations: {
    setUser(state, user) {
      state.user = user
    },
    setToken(state, token) {
      state.token = token
      localStorage.setItem('admin_token', token)
    },
    logout(state) {
      state.user = null
      state.token = null
      localStorage.removeItem('admin_token')
    },
    setProducts(state, products) {
      state.products = products
    },
    setOrders(state, orders) {
      state.orders = orders
    },
    setNews(state, news) {
      state.news = news
    },
    setPromotions(state, promotions) {
      state.promotions = promotions
    },
    setFeedback(state, feedback) {
      state.feedback = feedback
    }
  },
  actions: {
    async login({ commit }, credentials) {
      const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(credentials)
      })
      const data = await response.json()
      if (data.success) {
        commit('setToken', data.data.token)
        commit('setUser', data.data.user)
      }
      return data
    },
    async fetchProducts({ commit }) {
      const response = await fetch('/api/products', {
        headers: { 'Authorization': `Bearer ${this.state.token}` }
      })
      const data = await response.json()
      if (data.success) {
        commit('setProducts', data.data)
      }
    },
    async fetchOrders({ commit }) {
      const response = await fetch('/api/orders/all', {
        headers: { 'Authorization': `Bearer ${this.state.token}` }
      })
      const data = await response.json()
      if (data.success) {
        commit('setOrders', data.data)
      }
    },
    async fetchNews({ commit }) {
      const response = await fetch('/api/news', {
        headers: { 'Authorization': `Bearer ${this.state.token}` }
      })
      const data = await response.json()
      if (data.success) {
        commit('setNews', data.data)
      }
    },
    async fetchPromotions({ commit }) {
      const response = await fetch('/api/promotions', {
        headers: { 'Authorization': `Bearer ${this.state.token}` }
      })
      const data = await response.json()
      if (data.success) {
        commit('setPromotions', data.data)
      }
    },
    async fetchFeedback({ commit }) {
      const response = await fetch('/api/feedback', {
        headers: { 'Authorization': `Bearer ${this.state.token}` }
      })
      const data = await response.json()
      if (data.success) {
        commit('setFeedback', data.data)
      }
    }
  }
})

const app = createApp(App)
app.use(router)
app.use(store)
app.mount('#app')