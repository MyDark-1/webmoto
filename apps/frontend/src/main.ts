import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { createStore } from 'vuex'
import App from './App.vue'

// Импорт компонентов
import Home from './views/Home.vue'
import Products from './views/Products.vue'
import ProductDetail from './views/ProductDetail.vue'
import Cart from './views/Cart.vue'
import Checkout from './views/Checkout.vue'
import News from './views/News.vue'
import Promotions from './views/Promotions.vue'
import Login from './views/Login.vue'
import Register from './views/Register.vue'
import Profile from './views/Profile.vue'

// Маршруты
const routes = [
  { path: '/', component: Home },
  { path: '/products', component: Products },
  { path: '/products/:id', component: ProductDetail },
  { path: '/cart', component: Cart },
  { path: '/checkout', component: Checkout },
  { path: '/news', component: News },
  { path: '/promotions', component: Promotions },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/profile', component: Profile }
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
      token: localStorage.getItem('token'),
      cart: JSON.parse(localStorage.getItem('cart') || '[]'),
      products: [],
      categories: []
    }
  },
  mutations: {
    setUser(state, user) {
      state.user = user
    },
    setToken(state, token) {
      state.token = token
      localStorage.setItem('token', token)
    },
    logout(state) {
      state.user = null
      state.token = null
      localStorage.removeItem('token')
    },
    addToCart(state, product) {
      const existing = state.cart.find(item => item.product.id === product.id)
      if (existing) {
        existing.quantity++
      } else {
        state.cart.push({ product, quantity: 1 })
      }
      localStorage.setItem('cart', JSON.stringify(state.cart))
    },
    removeFromCart(state, productId) {
      state.cart = state.cart.filter(item => item.product.id !== productId)
      localStorage.setItem('cart', JSON.stringify(state.cart))
    },
    clearCart(state) {
      state.cart = []
      localStorage.removeItem('cart')
    },
    setProducts(state, products) {
      state.products = products
    },
    setCategories(state, categories) {
      state.categories = categories
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
    async register({ commit }, credentials) {
      const response = await fetch('/api/auth/register', {
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
      const response = await fetch('/api/products')
      const data = await response.json()
      if (data.success) {
        commit('setProducts', data.data)
      }
    },
    async fetchCategories({ commit }) {
      const response = await fetch('/api/categories')
      const data = await response.json()
      if (data.success) {
        commit('setCategories', data.data)
      }
    }
  }
})

const app = createApp(App)
app.use(router)
app.use(store)
app.mount('#app')