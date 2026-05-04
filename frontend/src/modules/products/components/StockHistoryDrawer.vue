<script setup lang="ts">
import { ref, computed } from 'vue'
import { useStockHistory } from '@/modules/products/composables/useStockHistory'
import { useFocusTrap } from '@/shared/composables/useFocusTrap'
import { formatDateTime } from '@/shared/utils/date'
import type { Product, StockHistoryEntry, StockType } from '@/modules/products/types/product.types'

const props = defineProps<{
  open: boolean
  product: Product | null
}>()

const emit = defineEmits<{
  close: []
}>()

const drawerRef = ref<HTMLElement | null>(null)
const isOpen = computed(() => props.open)
useFocusTrap(drawerRef, isOpen)

const productRef = computed(() => props.product)
const { isLoading, items, page, lastPage, nextPage, previousPage } = useStockHistory(
  productRef,
  isOpen,
)

const canGoPrev = computed(() => page.value > 1)
const canGoNext = computed(() => page.value < lastPage.value)

function handleClose() {
  if (isLoading.value) return
  emit('close')
}

function getTitle(type: StockType) {
  if (type === 'entrada') return 'Entrada de Estoque'
  if (type === 'saída') return 'Saída de Estoque'
  return 'Ajuste de Estoque'
}

function getNote(entry: StockHistoryEntry) {
  if (entry.type === 'entrada') return 'Entrada registrada no inventário.'
  if (entry.type === 'saída') return 'Saída registrada no inventário.'
  return 'Ajuste manual registrado no inventário.'
}

function getBadgeClass(type: StockType) {
  if (type === 'entrada') return 'bg-[#d0e1fb] text-[#38485d]'
  if (type === 'saída') return 'bg-[#ffdad6] text-[#93000a]'
  return 'bg-[#eceef0] text-[#4c4546]'
}

function goToPrev() {
  if (canGoPrev.value && !isLoading.value) previousPage()
}

function goToNext() {
  if (canGoNext.value && !isLoading.value) nextPage()
}

</script>

<template>
  <template v-if="open">
    <div
      class="fixed inset-0 z-[95] bg-black/20 backdrop-blur-sm w-screen h-screen"
      @click="handleClose"
    />

    <aside
      ref="drawerRef"
      role="complementary"
      :aria-label="`Histórico de estoque: ${product?.name ?? 'Produto'}`"
      class="fixed right-0 top-0 z-[100] flex h-full w-full max-w-[420px] flex-col border-l border-[#cfc4c5] bg-white shadow-2xl"
    >
      <header class="flex h-16 items-center justify-between border-b border-[#e0e3e5] px-6">
        <div class="flex items-center gap-3">
          <div class="inline-flex h-9 w-9 items-center justify-center bg-[#eceef0] text-[#4c4546]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 8V12L15 15" stroke="currentColor" stroke-width="1.6" />
              <path d="M3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21" stroke="currentColor" stroke-width="1.6" />
            </svg>
          </div>
          <div>
            <h3 class="text-xl font-semibold text-black">Histórico de Edição</h3>
            <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-[#4c4546]">
              {{ product?.name ?? 'Produto' }}
            </p>
          </div>
        </div>

        <button
          type="button"
          class="inline-flex h-8 w-8 items-center justify-center text-[#4c4546] transition hover:bg-[#eceef0] focus-visible:ring-2 focus-visible:ring-black"
          aria-label="Fechar histórico"
          @click="handleClose"
        >
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 6L18 18" stroke="currentColor" stroke-width="1.75" />
            <path d="M18 6L6 18" stroke="currentColor" stroke-width="1.75" />
          </svg>
        </button>
      </header>

      <div class="flex-1 overflow-y-auto p-6" role="status" aria-live="polite" aria-atomic="false">
        <div v-if="isLoading" class="text-sm text-[#4c4546]">Carregando histórico...</div>

        <div v-else-if="items.length === 0" class="text-sm text-[#4c4546]">
          Nenhuma movimentação encontrada para este produto.
        </div>

        <div
          v-else
          class="relative space-y-5 before:absolute before:bottom-0 before:left-[18px] before:top-0 before:w-px before:bg-[#e0e3e5]"
        >
          <article
            v-for="entry in items"
            :key="entry.id"
            class="relative pl-12"
          >
            <div class="absolute left-0 top-1 z-10 inline-flex h-9 w-9 items-center justify-center border border-[#cfc4c5] bg-white text-[#4c4546]">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 12H20" stroke="currentColor" stroke-width="1.5" />
                <path d="M12 4V20" stroke="currentColor" stroke-width="1.5" />
              </svg>
            </div>

            <div class="border border-[#e0e3e5] bg-[#f7f9fb] p-4">
              <div class="mb-2 flex items-start justify-between gap-3">
                <p class="text-sm font-semibold text-black">{{ getTitle(entry.type) }}</p>
                <span class="text-[10px] font-semibold uppercase tracking-[0.08em] text-[#4c4546]">
                  {{ formatDateTime(entry.created_at) }}
                </span>
              </div>

              <p class="mb-3 text-sm text-[#4c4546]">{{ getNote(entry) }}</p>

              <div class="mb-3 flex items-center gap-2 border border-[#e0e3e5] bg-white px-3 py-2 text-sm">
                <span class="text-[#4c4546]">{{ entry.previous_stock }} unidades</span>
                <svg class="h-4 w-4 text-[#4c4546]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M5 12H19" stroke="currentColor" stroke-width="1.5" />
                  <path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="1.5" />
                </svg>
                <span class="font-semibold text-black">{{ entry.current_stock }} unidades</span>
              </div>

              <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] text-[#4c4546]">
                  Alterado por <strong>{{ entry.user.name }}</strong>
                </span>
                <span
                  class="inline-flex border px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.08em]"
                  :class="getBadgeClass(entry.type)"
                >
                  {{ entry.type }}: {{ entry.quantity }}
                </span>
              </div>
            </div>
          </article>
        </div>
      </div>

      <footer class="space-y-3 border-t border-[#e0e3e5] bg-[#f2f4f6] p-5">
        <div class="flex items-center justify-between gap-2">
          <button
            type="button"
            class="h-9 border border-[#cfc4c5] bg-white px-4 text-sm font-semibold text-[#191c1e] transition hover:bg-[#eceef0] focus-visible:ring-2 focus-visible:ring-black disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="!canGoPrev || isLoading"
            @click="goToPrev"
          >
            Anterior
          </button>

          <span class="text-sm text-[#4c4546]">Página {{ page }} de {{ lastPage }}</span>

          <button
            type="button"
            class="h-9 border border-[#cfc4c5] bg-white px-4 text-sm font-semibold text-[#191c1e] transition hover:bg-[#eceef0] focus-visible:ring-2 focus-visible:ring-black disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="!canGoNext || isLoading"
            @click="goToNext"
          >
            Próximo
          </button>
        </div>
      </footer>
    </aside>
  </template>
</template>
