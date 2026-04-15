import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { apiFetch } from '../utils/api'

export const useUserStore = defineStore('user', () => {
  const user = ref<any>(null)
  const token = ref<string | null>(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials: { email: string; password: string }) {
    const data = await apiFetch<any>('/api/auth/login', {
      method: 'POST',
      json: credentials,
      auth: false
    })
    if (data.success && data.data) {
      token.value = data.data.token
      user.value = data.data.user
      localStorage.setItem('token', data.data.token)
    }
    return data
  }

  async function register(credentials: { email: string; password: string }) {
    const data = await apiFetch<any>('/api/auth/register', {
      method: 'POST',
      json: credentials,
      auth: false
    })
    if (data.success && data.data) {
      token.value = data.data.token
      user.value = data.data.user
      localStorage.setItem('token', data.data.token)
    }
    return data
  }

  function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
  }

  return { user, token, isAuthenticated, login, register, logout }
})
