<script setup lang="ts">
import { computed, ref } from 'vue'
import { useFocusTrap } from '@/shared/composables/useFocusTrap'
import type { Product } from '@/modules/products/types/product.types'

const props = defineProps<{
  open: boolean
  product: Product | null
  isSubmitting: boolean
}>()

const emit = defineEmits<{
  close: []
  confirm: []
}>()

const containerRef = ref<HTMLElement | null>(null)
const isOpen = computed(() => props.open)
useFocusTrap(containerRef, isOpen)

const productName = computed(() => props.product?.name ?? 'este produto')

function handleClose() {
  if (props.isSubmitting) return
  emit('close')
}

function handleConfirm() {
  if (props.isSubmitting) return
  emit('confirm')
}
</script>

<template>
  <div
    v-if="open"
    class="fixed inset-0 z-[120] flex items-center justify-center bg-black/40 px-4 backdrop-blur-[2px]"
    @click.self="handleClose"
  >
    <div
      ref="containerRef"
      role="dialog"
      aria-modal="true"
      aria-labelledby="delete-modal-title"
      class="w-full max-w-[520px] overflow-hidden border border-[#cfc4c5] bg-white shadow-xl"
    >
      <header class="flex items-center justify-between border-b border-[#e0e3e5] px-8 py-6">
        <h3 id="delete-modal-title" class="text-[30px] font-semibold leading-none tracking-[-0.01em] text-black">
          Confirmar Exclusão
        </h3>
        <button
          type="button"
          class="inline-flex h-8 w-8 items-center justify-center text-[#4c4546] transition hover:bg-[#eceef0] focus-visible:ring-2 focus-visible:ring-black"
          aria-label="Fechar modal"
          @click="handleClose"
        >
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 6L18 18" stroke="currentColor" stroke-width="1.75" />
            <path d="M18 6L6 18" stroke="currentColor" stroke-width="1.75" />
          </svg>
        </button>
      </header>

      <div class="space-y-4 px-8 py-6">
        <p class="text-base text-[#191c1e]">
          Tem certeza que deseja excluir <strong>{{ productName }}</strong>?
        </p>
        <p class="text-sm text-[#4c4546]">
          Esta ação não pode ser desfeita.
        </p>
      </div>

      <footer class="-mx-0 mt-2 flex justify-end gap-3 border-t border-[#e0e3e5] bg-[#f2f4f6] px-8 py-5">
        <button
          type="button"
          class="h-10 border border-[#cfc4c5] bg-white px-5 text-sm font-medium text-[#4c4546] transition hover:bg-[#eceef0] focus-visible:ring-2 focus-visible:ring-black disabled:cursor-not-allowed disabled:opacity-60"
          :disabled="isSubmitting"
          @click="handleClose"
        >
          Cancelar
        </button>
        <button
          type="button"
          class="h-10 border border-[#ba1a1a] bg-[#ba1a1a] px-5 text-sm font-semibold text-white transition hover:bg-[#93000a] focus-visible:ring-2 focus-visible:ring-white disabled:cursor-not-allowed disabled:opacity-60"
          :disabled="isSubmitting"
          @click="handleConfirm"
        >
          {{ isSubmitting ? 'Excluindo...' : 'Excluir Produto' }}
        </button>
      </footer>
    </div>
  </div>
</template>
