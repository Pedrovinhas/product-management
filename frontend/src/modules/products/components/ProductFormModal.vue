<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useForm } from 'vee-validate'
import { productSchema } from '@/modules/products/schemas/product.schema'
import { useProductForm } from '@/modules/products/composables/useProductForm'
import { useFocusTrap } from '@/shared/composables/useFocusTrap'
import CurrencyInput from '@/shared/components/CurrencyInput.vue'
import type { Product } from '@/modules/products/types/product.types'

const props = defineProps<{
  open: boolean
  mode: 'create' | 'edit'
  product: Product | null
}>()

const emit = defineEmits<{
  close: []
  saved: []
}>()

const containerRef = ref<HTMLElement | null>(null)
const isOpen = computed(() => props.open)
useFocusTrap(containerRef, isOpen)

const { createProduct, updateProduct } = useProductForm()

const { defineField, errors, handleSubmit, resetForm, isSubmitting } = useForm({
  validationSchema: productSchema,
})

const [name] = defineField('name')
const [description] = defineField('description')
const [price] = defineField('price')
const [stock_quantity] = defineField('stock_quantity')

const modalTitle = computed(() =>
  props.mode === 'create' ? 'Cadastro de Produto' : 'Edição de Produto',
)
const actionLabel = computed(() =>
  props.mode === 'create' ? 'Salvar Produto' : 'Salvar Alterações',
)

watch(
  () => props.open,
  (isOpen) => {
    if (!isOpen) return

    if (props.mode === 'edit' && props.product) {
      resetForm({
        values: {
          name: props.product.name,
          description: props.product.description ?? '',
          price: props.product.price,
          stock_quantity: props.product.stock_quantity,
        },
      })
    } else {
      resetForm({ values: { name: '', description: '', price: undefined, stock_quantity: undefined } })
    }
  },
)

const onSubmit = handleSubmit(async (values) => {
  const payload = {
    name: values.name,
    description: values.description?.trim() ? values.description.trim() : null,
    price: values.price,
    stock_quantity: values.stock_quantity,
  }

  const success =
    props.mode === 'create'
      ? await createProduct(payload)
      : props.product
        ? await updateProduct(props.product.id, payload)
        : false

  if (success) {
    emit('saved')
  }
})

function handleClose() {
  if (isSubmitting.value) return
  emit('close')
}
</script>

<template>
  <div
    v-if="open"
    class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 px-4 backdrop-blur-[2px]"
    @click.self="handleClose"
  >
    <div
      ref="containerRef"
      role="dialog"
      aria-modal="true"
      :aria-labelledby="'modal-title-' + mode"
      class="w-full max-w-[560px] overflow-hidden border border-[#cfc4c5] bg-white shadow-xl"
    >
      <header class="flex items-center justify-between border-b border-[#e0e3e5] px-8 py-6">
        <h3
          :id="'modal-title-' + mode"
          class="text-[34px] font-semibold leading-none tracking-[-0.01em] text-black"
        >
          {{ modalTitle }}
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

      <form class="space-y-5 px-8 py-6" @submit.prevent="onSubmit">
        <div>
          <label class="mb-2 block text-sm font-semibold text-[#191c1e]" for="product-name">
            Nome do Produto
          </label>
          <input
            id="product-name"
            v-model="name"
            type="text"
            class="h-11 w-full border border-[#cfc4c5] bg-[#f7f9fb] px-3 text-base text-[#191c1e] outline-none transition focus:border-black focus-visible:ring-2 focus-visible:ring-black"
            placeholder="Ex: Placa-Mãe Z790-P"
          />
          <p class="mt-1 text-xs text-[#ba1a1a]" role="alert">{{ errors.name }}</p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-[#191c1e]" for="product-description">
            Descrição
          </label>
          <textarea
            id="product-description"
            v-model="description"
            class="w-full resize-none border border-[#cfc4c5] bg-[#f7f9fb] px-3 py-2.5 text-base text-[#191c1e] outline-none transition focus:border-black focus-visible:ring-2 focus-visible:ring-black"
            rows="4"
            placeholder="Descreva os detalhes técnicos e especificações..."
          />
          <p class="mt-1 text-xs text-[#ba1a1a]" role="alert">{{ errors.description }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-[#191c1e]" for="product-price">
              Preço (R$)
            </label>
            <CurrencyInput
              id="product-price"
              v-model="price"
              class="h-11 w-full border border-[#cfc4c5] bg-[#f7f9fb] px-3 text-base text-[#191c1e] outline-none transition focus:border-black focus-visible:ring-2 focus-visible:ring-black"
              placeholder="0,00"
              :aria-invalid="Boolean(errors.price)"
            />
            <p class="mt-1 text-xs text-[#ba1a1a]" role="alert">{{ errors.price }}</p>
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-[#191c1e]" for="product-stock">
              Estoque Inicial
            </label>
            <input
              id="product-stock"
              v-model="stock_quantity"
              type="number"
              min="0"
              step="1"
              class="h-11 w-full border border-[#cfc4c5] bg-[#f7f9fb] px-3 text-base text-[#191c1e] outline-none transition focus:border-black focus-visible:ring-2 focus-visible:ring-black"
              placeholder="0"
            />
            <p class="mt-1 text-xs text-[#ba1a1a]" role="alert">{{ errors.stock_quantity }}</p>
          </div>
        </div>

        <footer class="-mx-8 -mb-6 mt-8 flex justify-end gap-3 border-t border-[#e0e3e5] bg-[#f2f4f6] px-8 py-5">
          <button
            type="button"
            class="h-10 border border-[#cfc4c5] bg-white px-5 text-sm font-medium text-[#4c4546] transition hover:bg-[#eceef0] focus-visible:ring-2 focus-visible:ring-black disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="isSubmitting"
            @click="handleClose"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="h-10 border border-black bg-black px-5 text-sm font-semibold text-white transition hover:bg-[#1b1b1b] focus-visible:ring-2 focus-visible:ring-white disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="isSubmitting"
          >
            {{ isSubmitting ? 'Salvando...' : actionLabel }}
          </button>
        </footer>
      </form>
    </div>
  </div>
</template>
