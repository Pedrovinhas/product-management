import { createRouter, createWebHistory, type RouteLocationNormalized } from 'vue-router'
import { useAuthStore } from '@/modules/auth/store/auth.store'
import LoginView from '@/modules/auth/views/LoginView.vue'
import ProductsListView from '@/modules/products/views/ProductsListView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: {
        title: 'Acesso',
        layout: 'blank',
        guestOnly: true,
        hideHeader: true,
      },
    },
    {
      path: '/',
      redirect: { name: 'products' },
    },
    {
      path: '/products',
      name: 'products',
      component: ProductsListView,
      meta: {
        title: 'Produtos',
        layout: 'shell',
        requiresAuth: true,
      },
    }
  ],
})

router.beforeEach(async (to: RouteLocationNormalized) => {
  const authStore = useAuthStore()

  if (authStore.isAuthenticated && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch {
      authStore.clearSession()
    }
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.guestOnly && authStore.isAuthenticated) {
    return { name: 'products' }
  }

  return true
})

export default router
