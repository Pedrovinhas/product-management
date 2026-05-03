import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/modules/auth/store/auth.store'

export function useAuth() {
  const authStore = useAuthStore()
  const { user, isAuthenticated, isLoading } = storeToRefs(authStore)

  return {
    user,
    isAuthenticated,
    isLoading,
    login: authStore.login,
    logout: authStore.logout,
    fetchUser: authStore.fetchUser,
  }
}
