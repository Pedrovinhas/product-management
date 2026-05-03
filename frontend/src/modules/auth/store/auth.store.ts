import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import { authService } from '@/modules/auth/services/auth.service'
import type { LoginRequest, User } from '@/modules/auth/types/auth.types'

const TOKEN_KEY = 'product-manager.token'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem(TOKEN_KEY))
  const user = ref<User | null>(null)
  const isLoading = ref(false)

  const isAuthenticated = computed(() => Boolean(token.value))

  async function login(payload: LoginRequest) {
    isLoading.value = true

    try {
      const response = await authService.login(payload)

      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem(TOKEN_KEY, response.data.token)
    } finally {
      isLoading.value = false
    }
  }

  async function fetchUser() {
    if (!token.value) {
      return
    }

    user.value = await authService.me()
  }

  async function logout() {
    if (token.value) {
      await authService.logout()
    }

    clearSession()
  }

  function clearSession() {
    token.value = null
    user.value = null
    localStorage.removeItem(TOKEN_KEY)
  }

  return {
    token,
    user,
    isLoading,
    isAuthenticated,
    login,
    fetchUser,
    logout,
    clearSession,
  }
})
