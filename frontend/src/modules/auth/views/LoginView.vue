<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useForm } from 'vee-validate'
import { loginSchema } from '@/modules/auth/schemas/login.schema'
import { useAuth } from '@/modules/auth/composables/useAuth'
import { useToast } from '@/shared/composables/useToast'

const router = useRouter()
const { login, isLoading } = useAuth()
const { success } = useToast()

const { defineField, errors, handleSubmit } = useForm({
  validationSchema: loginSchema,
  initialValues: {
    email: '',
    password: '',
  },
})

const [email] = defineField('email')
const [password] = defineField('password')

const onSubmit = handleSubmit(async (values) => {
  await login(values)
  success('Login realizado com sucesso.')
  await router.push({ name: 'products' })
})
</script>

<template>
  <section class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[#f7f9fb] px-6 py-10 text-[#191c1e]">
    <div class="mx-auto w-full max-w-6xl">
      <div class="mx-auto w-full max-w-[420px]">
        <div class="border border-[#cfc4c5] bg-white p-8 sm:p-10">
          <div>
            <h2 class="text-[32px] font-semibold leading-[1.2] tracking-[-0.01em] text-black">Bem-vindo de volta</h2>
            <p class="mt-2 text-base text-[#4c4546]">Acesse sua conta para continuar</p>
          </div>

          <form class="mt-9 space-y-6" @submit.prevent="onSubmit">
            <div>
              <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.05em] text-[#191c1e]" for="email">
                Email
              </label>
              <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[#7e7576]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M4 6H20V18H4V6Z" stroke="currentColor" stroke-width="1.75"/>
                  <path d="M4 7L12 13L20 7" stroke="currentColor" stroke-width="1.75"/>
                </svg>
                <input
                  id="email"
                  v-model="email"
                  type="email"
                  autocomplete="email"
                  class="h-12 w-full border border-[#cfc4c5] bg-[#f2f4f6] pl-11 pr-3 text-base text-[#191c1e] outline-none transition focus:border-black focus-visible:ring-2 focus-visible:ring-black"
                  placeholder="exemplo@empresa.com"
                />
              </div>
              <p class="mt-1 text-xs text-[#ba1a1a]">{{ errors.email }}</p>
            </div>

            <div>
              <div class="mb-2 flex items-center justify-between">
                <label class="block text-[12px] font-semibold uppercase tracking-[0.05em] text-[#191c1e]" for="password">
                  Senha
                </label>
                <span class="text-xs text-[#4c4546] opacity-40" aria-disabled="true">Esqueceu a senha?</span>
              </div>
              <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[#7e7576]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M7 10V7C7 4.79086 8.79086 3 11 3H13C15.2091 3 17 4.79086 17 7V10" stroke="currentColor" stroke-width="1.75"/>
                  <rect x="4" y="10" width="16" height="11" stroke="currentColor" stroke-width="1.75"/>
                </svg>
                <input
                  id="password"
                  v-model="password"
                  type="password"
                  autocomplete="current-password"
                  class="h-12 w-full border border-[#cfc4c5] bg-[#f2f4f6] pl-11 pr-3 text-base text-[#191c1e] outline-none transition focus:border-black focus-visible:ring-2 focus-visible:ring-black"
                  placeholder="••••••••"
                />
              </div>
              <p class="mt-1 text-xs text-[#ba1a1a]">{{ errors.password }}</p>
            </div>

            <button
              type="submit"
              :disabled="isLoading"
              class="mt-1 flex h-12 w-full items-center justify-center gap-2 border border-black bg-black px-4 text-base font-semibold text-white transition hover:bg-[#1b1b1b] disabled:cursor-not-allowed disabled:opacity-50"
            >
              {{ isLoading ? 'Entrando...' : 'Entrar' }}
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 12H20" stroke="currentColor" stroke-width="1.75"/>
                <path d="M14 6L20 12L14 18" stroke="currentColor" stroke-width="1.75"/>
              </svg>
            </button>
          </form>


        </div>
      </div>
    </div>

    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-64 bg-gradient-to-t from-[#d8dadc] to-transparent opacity-40" />
  </section>
</template>
