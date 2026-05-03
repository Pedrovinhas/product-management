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
  <section class="relative min-h-screen overflow-hidden bg-[#f7f9fb] px-6 py-10 text-[#191c1e]">
    <div class="mx-auto w-full max-w-6xl">
      <div class="mx-auto mb-10 w-full max-w-md text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center border-2 border-black bg-black text-white">
          <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M7 7.5V6C7 3.791 8.791 2 11 2H13C15.209 2 17 3.791 17 6V7.5" stroke="currentColor" stroke-width="1.75"/>
            <path d="M5 7.5H19C20.1046 7.5 21 8.39543 21 9.5V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V9.5C3 8.39543 3.89543 7.5 5 7.5Z" stroke="currentColor" stroke-width="1.75"/>
            <path d="M9 12H15" stroke="currentColor" stroke-width="1.75"/>
            <path d="M9 15H13" stroke="currentColor" stroke-width="1.75"/>
          </svg>
        </div>

        <h1 class="mt-5 text-[40px] font-bold leading-[1.1] tracking-[-0.02em] text-black">ERP Core</h1>
        <p class="mt-2 text-sm text-[#4c4546]">Gestao Inteligente de Produtos</p>
      </div>

      <div class="mx-auto grid w-full max-w-[980px] grid-cols-1 items-start gap-0 xl:grid-cols-[1fr_340px]">
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

        <aside class="hidden border border-l-0 border-[#cfc4c5] bg-white p-5 xl:block">
          <div class="h-[280px] border border-[#cfc4c5] bg-[#eceef0] overflow-hidden">
            <svg
              class="h-full w-full"
              viewBox="0 0 340 280"
              xmlns="http://www.w3.org/2000/svg"
              aria-hidden="true"
            >
              <rect width="340" height="280" fill="#eceef0" />
              <!-- Grid lines -->
              <g stroke="#cfc4c5" stroke-width="0.5">
                <line x1="0" y1="56" x2="340" y2="56" />
                <line x1="0" y1="112" x2="340" y2="112" />
                <line x1="0" y1="168" x2="340" y2="168" />
                <line x1="0" y1="224" x2="340" y2="224" />
                <line x1="68" y1="0" x2="68" y2="280" />
                <line x1="136" y1="0" x2="136" y2="280" />
                <line x1="204" y1="0" x2="204" y2="280" />
                <line x1="272" y1="0" x2="272" y2="280" />
              </g>
              <!-- Bar chart -->
              <rect x="30" y="160" width="28" height="90" fill="#191c1e" opacity="0.12" />
              <rect x="80" y="120" width="28" height="130" fill="#191c1e" opacity="0.18" />
              <rect x="130" y="80" width="28" height="170" fill="#191c1e" opacity="0.24" />
              <rect x="180" y="100" width="28" height="150" fill="#191c1e" opacity="0.18" />
              <rect x="230" y="60" width="28" height="190" fill="#191c1e" opacity="0.30" />
              <rect x="280" y="40" width="28" height="210" fill="#191c1e" opacity="0.36" />
              <!-- Trend line -->
              <polyline
                points="44,200 94,160 144,110 194,130 244,90 294,68"
                fill="none"
                stroke="#191c1e"
                stroke-width="1.5"
                stroke-opacity="0.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <!-- Dots on trend line -->
              <circle cx="44" cy="200" r="3" fill="#191c1e" opacity="0.5" />
              <circle cx="94" cy="160" r="3" fill="#191c1e" opacity="0.5" />
              <circle cx="144" cy="110" r="3" fill="#191c1e" opacity="0.5" />
              <circle cx="194" cy="130" r="3" fill="#191c1e" opacity="0.5" />
              <circle cx="244" cy="90" r="3" fill="#191c1e" opacity="0.5" />
              <circle cx="294" cy="68" r="3" fill="#191c1e" opacity="0.7" />
            </svg>
          </div>

          <div class="mt-5 flex items-start gap-3 border border-[#cfc4c5] bg-[#f7f9fb] p-4">
            <div class="mt-0.5 flex h-8 w-8 items-center justify-center border border-black bg-white text-black">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3L14.78 8.63L21 9.53L16.5 13.91L17.56 20.09L12 17.17L6.44 20.09L7.5 13.91L3 9.53L9.22 8.63L12 3Z" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-black">Infraestrutura segura</h3>
              <p class="mt-1 text-sm leading-relaxed text-[#4c4546]">
                Seus dados protegidos com criptografia de ponta.
              </p>
            </div>
          </div>
        </aside>
      </div>


    </div>

    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-64 bg-gradient-to-t from-[#d8dadc] to-transparent opacity-40" />
  </section>
</template>
