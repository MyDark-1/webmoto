import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { apiFetch } from '../utils/api'

export const useAdminUserStore = defineStore('admin-user', () => {
  const user = ref<any>(null)
  const token = ref<string | null>(localStorage.getItem('admin_token'))

  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials: { email: string; password: string }) {
    const data = await apiFetch<any>('/api/auth/login', {
      method: 'POST',
      json: credentials
    })
    if (data.success && data.data) {
      token.value = data.data.token
      user.value = data.data.user
      localStorage.setItem('admin_token', data.data.token)
    }
    return data
  }

  function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('admin_token')
  }

  return { user, token, isAuthenticated, login, logout }
})
