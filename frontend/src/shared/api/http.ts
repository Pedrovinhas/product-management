import axios from 'axios'
import type { Pinia } from 'pinia'
import { toast } from 'vue-sonner'
import { useAuthStore } from '@/modules/auth/store/auth.store'

const baseURL = import.meta.env.VITE_API_BASE_URL ?? '/api'

export const http = axios.create({
  baseURL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

let interceptorsConfigured = false

export function setupApiInterceptors(pinia: Pinia) {
  if (interceptorsConfigured) {
    return
  }

  http.interceptors.request.use((config) => {
    const authStore = useAuthStore(pinia)

    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }

    return config
  })

  http.interceptors.response.use(
    (response) => response,
    async (error) => {
      const authStore = useAuthStore(pinia)
      const status = error.response?.status as number | undefined
      const message =
        (error.response?.data?.message as string | undefined) ??
        'Erro inesperado ao comunicar com a API.'

      if (status === 401) {
        authStore.clearSession()
        return Promise.reject(error)
      }

      toast.error(message)

      return Promise.reject(error)
    },
  )

  interceptorsConfigured = true
}
