import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { apiFetch } from '../utils/api'

export const useUserStore = defineStore('user', () => {
  const user = ref<any>(null)
  const token = ref<string | null>(localStorage.getItem('token'))
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  // Загрузить профиль по токену (при перезагрузке страницы)
  async function fetchUser() {
    if (!token.value) return
    loading.value = true
    const data = await apiFetch<any>('/api/auth/me')
    if (data.success && data.data) {
      user.value = data.data
    } else {
      // Токен невалидный — очищаем
      logout()
    }
    loading.value = false
  }

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
      // Если админ — сохраняем токен и для админ-панели
      if (data.data.user?.role === 'admin') {
        localStorage.setItem('admin_token', data.data.token)
      }
    }
    return data
  }

  async function register(credentials: { email: string; password: string; fullname?: string; phone?: string }) {
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
    localStorage.removeItem('admin_token')
  }

  return { user, token, loading, isAuthenticated, isAdmin, fetchUser, login, register, logout }
})
