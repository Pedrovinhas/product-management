<script setup lang="ts">
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/modules/auth/store/auth.store'
import { useToast } from '@/shared/composables/useToast'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { user } = storeToRefs(authStore)
const { success, error } = useToast()

const userInitials = computed(() => {
  const value = (user.value?.name ?? 'Operador').trim()

  if (!value) {
    return 'OP'
  }

  return value
    .split(/\s+/)
    .slice(0, 2)
    .map((chunk) => chunk[0]?.toUpperCase() ?? '')
    .join('')
})

const isProductsSection = computed(() => route.path.startsWith('/products'))

async function logout() {
  try {
    await authStore.logout()
    success('Sessão encerrada com sucesso.')
    await router.push({ name: 'login' })
  } catch {
    error('Não foi possível encerrar a sessão no momento.')
  }
}
</script>

<template>
  <div class="min-h-screen bg-[#f7f9fb] text-[#191c1e]">
    <aside class="fixed left-0 top-0 z-40 hidden h-full w-[260px] border-r border-[#cfc4c5] bg-white lg:flex lg:flex-col">
      <div class="px-6 pb-7 pt-6">
        <h1 class="text-[30px] font-bold leading-none text-black">Gestão ERP</h1>
        <p class="mt-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#4c4546]">Operações</p>
      </div>

      <nav class="flex flex-col gap-1">
        <RouterLink
          :to="{ name: 'products' }"
          class="flex items-center gap-3 px-6 py-3 text-sm font-semibold text-[#191c1e] transition"
          :class="isProductsSection ? 'border-r-4 border-black bg-[#eceef0]' : 'hover:bg-[#f2f4f6]'"
        >
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 5H20V19H4V5Z" stroke="currentColor" stroke-width="1.75" />
            <path d="M8 9H16" stroke="currentColor" stroke-width="1.75" />
          </svg>
          Produtos
        </RouterLink>
      </nav>
    </aside>

    <header class="fixed left-0 right-0 top-0 z-30 h-16 border-b border-[#cfc4c5] bg-white lg:left-[260px]">
      <div class="flex h-full items-center justify-between px-4 sm:px-6">
        <div class="flex items-center gap-3 lg:hidden">
          <span class="inline-flex h-8 w-8 items-center justify-center border border-black bg-black text-xs font-semibold text-white">ERP</span>
          <span class="text-sm font-semibold text-black">Gestão ERP</span>
        </div>

        <div class="hidden lg:block" />

        <div class="flex items-center gap-2 sm:gap-4">
          <button
            type="button"
            class="inline-flex h-9 w-9 items-center justify-center border border-transparent text-[#4c4546] transition hover:border-[#cfc4c5] hover:bg-[#f2f4f6]"
            aria-label="Notificações"
          >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6 10C6 6.68629 8.68629 4 12 4C15.3137 4 18 6.68629 18 10V14L20 16V17H4V16L6 14V10Z" stroke="currentColor" stroke-width="1.6" />
              <path d="M10 19C10.2 19.6 10.8 20 12 20C13.2 20 13.8 19.6 14 19" stroke="currentColor" stroke-width="1.6" />
            </svg>
          </button>

          <button
            type="button"
            class="inline-flex h-9 w-9 items-center justify-center border border-transparent text-[#4c4546] transition hover:border-[#cfc4c5] hover:bg-[#f2f4f6]"
            aria-label="Configurações"
          >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 9.5C10.6193 9.5 9.5 10.6193 9.5 12C9.5 13.3807 10.6193 14.5 12 14.5C13.3807 14.5 14.5 13.3807 14.5 12C14.5 10.6193 13.3807 9.5 12 9.5Z" stroke="currentColor" stroke-width="1.6" />
              <path d="M19.4 15.5L20.5 17.4L18.4 19.5L16.5 18.4C15.9 18.8 15.2 19.1 14.5 19.2L14 21.5H10L9.5 19.2C8.8 19.1 8.1 18.8 7.5 18.4L5.6 19.5L3.5 17.4L4.6 15.5C4.2 14.9 3.9 14.2 3.8 13.5L1.5 13V11L3.8 10.5C3.9 9.8 4.2 9.1 4.6 8.5L3.5 6.6L5.6 4.5L7.5 5.6C8.1 5.2 8.8 4.9 9.5 4.8L10 2.5H14L14.5 4.8C15.2 4.9 15.9 5.2 16.5 5.6L18.4 4.5L20.5 6.6L19.4 8.5C19.8 9.1 20.1 9.8 20.2 10.5L22.5 11V13L20.2 13.5C20.1 14.2 19.8 14.9 19.4 15.5Z" stroke="currentColor" stroke-width="1.2" />
            </svg>
          </button>

          <div class="hidden h-8 w-px bg-[#cfc4c5] sm:block" />

          <div class="hidden text-right sm:block">
            <p class="text-sm font-semibold text-black">{{ user?.name ?? 'Usuário' }}</p>
            <p class="text-xs text-[#4c4546]">Operador</p>
          </div>

          <span class="inline-flex h-10 w-10 items-center justify-center border border-[#cfc4c5] bg-[#eceef0] text-sm font-semibold text-[#191c1e]">
            {{ userInitials }}
          </span>

          <button
            type="button"
            class="inline-flex h-9 items-center border border-black bg-black px-3 text-xs font-semibold uppercase tracking-[0.08em] text-white transition hover:bg-[#1b1b1b]"
            @click="logout"
          >
            Sair
          </button>
        </div>
      </div>
    </header>

    <main class="min-h-screen pt-16 lg:pl-[260px]">
      <slot />
    </main>
  </div>
</template>