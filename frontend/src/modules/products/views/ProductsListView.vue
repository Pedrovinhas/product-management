<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useProductList } from '@/modules/products/composables/useProductList'
import { useProductDelete } from '@/modules/products/composables/useProductDelete'
import { formatCurrency } from '@/shared/utils/currency'
import type { Product } from '@/modules/products/types/product.types'
import ProductFormModal from '@/modules/products/components/ProductFormModal.vue'
import ProductDeleteModal from '@/modules/products/components/ProductDeleteModal.vue'
import StockHistoryDrawer from '@/modules/products/components/StockHistoryDrawer.vue'

const { items, isLoading, currentPage, lastPage, total, from, to, fetchList } = useProductList()
const { isDeleting, deleteProduct } = useProductDelete()

const searchInput = ref('')
const priceFilter = ref('all')
const stockFilter = ref('all')

// Modal state
const isModalOpen = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const editingProduct = ref<Product | null>(null)

// Drawer state
const isHistoryDrawerOpen = ref(false)
const historyProduct = ref<Product | null>(null)

// Delete modal state
const isDeleteModalOpen = ref(false)
const deletingProduct = ref<Product | null>(null)

const lowStockCount = computed(() => items.value.filter((item) => item.stock_quantity <= 10).length)
const totalStockValue = computed(() => items.value.reduce((sum, item) => sum + item.price * item.stock_quantity, 0))
const visibleFrom = computed(() => from.value ?? (items.value.length > 0 ? 1 : 0))
const visibleTo = computed(() => to.value ?? items.value.length)
const canGoPrev = computed(() => currentPage.value > 1)
const canGoNext = computed(() => currentPage.value < lastPage.value)

onMounted(async () => {
  await fetchList({ page: 1 })
})

async function onSearchSubmit() {
  await fetchList({
    search: searchInput.value,
    min_price: priceFilter.value === 'under-100' ? 0 : priceFilter.value === '100-500' ? 100 : priceFilter.value === 'over-500' ? 500 : undefined,
    max_price: priceFilter.value === 'under-100' ? 100 : priceFilter.value === '100-500' ? 500 : undefined,
    min_stock: stockFilter.value === 'in-stock' ? 11 : stockFilter.value === 'low-stock' ? 1 : stockFilter.value === 'out-of-stock' ? 0 : undefined,
    max_stock: stockFilter.value === 'low-stock' ? 10 : stockFilter.value === 'out-of-stock' ? 0 : undefined,
    page: 1,
  })
}

async function goToPreviousPage() {
  if (canGoPrev.value) await fetchList({ page: currentPage.value - 1 })
}

async function goToNextPage() {
  if (canGoNext.value) await fetchList({ page: currentPage.value + 1 })
}

function openCreateModal() {
  isHistoryDrawerOpen.value = false
  modalMode.value = 'create'
  editingProduct.value = null
  isModalOpen.value = true
}

function openEditModal(product: Product) {
  isHistoryDrawerOpen.value = false
  modalMode.value = 'edit'
  editingProduct.value = product
  isModalOpen.value = true
}

async function onModalSaved() {
  await fetchList({ page: currentPage.value })
  isModalOpen.value = false
}

function openHistoryDrawer(product: Product) {
  isModalOpen.value = false
  historyProduct.value = product
  isHistoryDrawerOpen.value = true
}

function openDeleteModal(product: Product) {
  isModalOpen.value = false
  isHistoryDrawerOpen.value = false
  deletingProduct.value = product
  isDeleteModalOpen.value = true
}

async function onDeleteConfirmed() {
  if (!deletingProduct.value) return

  const success = await deleteProduct(deletingProduct.value.id)

  if (success) {
    isDeleteModalOpen.value = false
    deletingProduct.value = null
  }
}

function getStatusLabel(stockQuantity: number) {
  if (stockQuantity <= 0) return 'Esgotado'
  if (stockQuantity <= 10) return 'Estoque Baixo'
  return 'Em Estoque'
}

function getStatusClass(stockQuantity: number) {
  if (stockQuantity <= 0) return 'bg-[#ffdad6] text-[#93000a]'
  if (stockQuantity <= 10) return 'bg-[#d0e1fb] text-[#38485d]'
  return 'bg-[#eceef0] text-[#191c1e]'
}

function getSku(id: number) {
  return `SKU-${String(id).padStart(6, '0')}`
}
</script>

<template>
  <section class="bg-[#f7f9fb] px-6 py-8 text-[#191c1e]">
    <div class="mx-auto max-w-7xl space-y-6">
      <div class="flex flex-col justify-between gap-4 py-2 sm:flex-row sm:items-center">
        <div>
          <h2 class="text-[32px] font-semibold leading-[1.2] tracking-[-0.01em] text-black">Gestão de Produtos</h2>
          <p class="mt-1 text-base text-[#4c4546]">Visualize e gerencie seu catálogo completo de mercadorias.</p>
        </div>

        <button
          type="button"
          class="inline-flex h-11 items-center justify-center gap-2 border border-black bg-black px-6 text-sm font-semibold text-white transition hover:bg-[#1b1b1b]"
          @click="openCreateModal"
        >
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 5V19" stroke="currentColor" stroke-width="1.75" />
            <path d="M5 12H19" stroke="currentColor" stroke-width="1.75" />
          </svg>
          Novo Produto
        </button>
      </div>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <article class="flex items-center gap-4 border border-[#cfc4c5] bg-white p-5">
          <div class="flex h-11 w-11 items-center justify-center border border-[#cfc4c5] bg-[#eceef0] text-[#191c1e]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M4 5H20V19H4V5Z" stroke="currentColor" stroke-width="1.75" />
              <path d="M8 9H16" stroke="currentColor" stroke-width="1.75" />
            </svg>
          </div>
          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[#4c4546]">Total de Produtos</p>
            <p class="text-[34px] font-bold leading-none text-black">{{ total }}</p>
          </div>
        </article>

        <article class="flex items-center gap-4 border border-[#cfc4c5] bg-white p-5">
          <div class="flex h-11 w-11 items-center justify-center border border-[#cfc4c5] bg-[#eceef0] text-[#191c1e]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M4 18L10 12L14 16L20 10" stroke="currentColor" stroke-width="1.75" />
            </svg>
          </div>
          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[#4c4546]">Valor em Estoque <span class="normal-case font-normal opacity-60">(nesta página)</span></p>
            <p class="text-[34px] font-bold leading-none text-black">{{ formatCurrency(totalStockValue) }}</p>
          </div>
        </article>

        <article class="flex items-center gap-4 border border-[#cfc4c5] bg-white p-5">
          <div class="flex h-11 w-11 items-center justify-center border border-[#ffdad6] bg-[#ffdad6] text-[#ba1a1a]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 4L21 20H3L12 4Z" stroke="currentColor" stroke-width="1.75" />
              <path d="M12 10V14" stroke="currentColor" stroke-width="1.75" />
              <path d="M12 17H12.01" stroke="currentColor" stroke-width="1.75" />
            </svg>
          </div>
          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[#4c4546]">Estoque Baixo <span class="normal-case font-normal opacity-60">(nesta página)</span></p>
            <p class="text-[34px] font-bold leading-none text-black">{{ lowStockCount }} Itens</p>
          </div>
        </article>
      </div>

      <div class="overflow-hidden border border-[#cfc4c5] bg-white">
        <form class="flex flex-col gap-4 border-b border-[#cfc4c5] bg-[#f7f9fb] p-5 lg:flex-row lg:items-center lg:justify-between" @submit.prevent="onSearchSubmit">
          <div class="flex flex-col gap-3 sm:flex-row">
            <input
              id="search"
              v-model="searchInput"
              type="text"
              class="h-10 min-w-[220px] border border-[#cfc4c5] bg-white px-3 text-sm outline-none transition focus:border-black focus-visible:ring-2 focus-visible:ring-black"
              placeholder="Buscar produto..."
            />
            <select
              v-model="priceFilter"
              class="h-10 min-w-[180px] border border-[#cfc4c5] bg-white px-3 text-sm outline-none transition focus:border-black"
            >
              <option value="all">Todos os Preços</option>
              <option value="under-100">Abaixo de R$ 100</option>
              <option value="100-500">R$ 100 - R$ 500</option>
              <option value="over-500">Acima de R$ 500</option>
            </select>
            <select
              v-model="stockFilter"
              class="h-10 min-w-[180px] border border-[#cfc4c5] bg-white px-3 text-sm outline-none transition focus:border-black"
            >
              <option value="all">Status de Estoque</option>
              <option value="in-stock">Em estoque</option>
              <option value="low-stock">Estoque baixo</option>
              <option value="out-of-stock">Esgotado</option>
            </select>
            <button
              type="submit"
              class="h-10 border border-black bg-black px-4 text-sm font-semibold text-white transition hover:bg-[#1b1b1b]"
            >
              Filtrar
            </button>
          </div>

          <p class="text-sm text-[#4c4546]">
            Exibindo <span class="font-semibold text-black">{{ visibleFrom }}-{{ visibleTo }}</span> de
            <span class="font-semibold text-black">{{ total }}</span>
          </p>
        </form>

        <div class="overflow-x-auto">
          <table class="w-full min-w-[860px] border-collapse text-left">
            <thead>
              <tr class="border-b border-[#cfc4c5] bg-[#eceef0] text-[10px] font-semibold uppercase tracking-[0.12em] text-[#4c4546]">
                <th class="px-6 py-4">Produto</th>
                <th class="px-6 py-4">Preço</th>
                <th class="px-6 py-4">Estoque</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Ações</th>
              </tr>
            </thead>

            <tbody>
              <tr v-if="isLoading" class="border-b border-[#cfc4c5]">
                <td class="px-6 py-6 text-sm text-[#4c4546]" colspan="5" role="status">Carregando produtos...</td>
              </tr>
              <tr v-else-if="items.length === 0" class="border-b border-[#cfc4c5]">
                <td class="px-6 py-6 text-sm text-[#4c4546]" colspan="5">Nenhum produto encontrado para os filtros selecionados.</td>
              </tr>

              <template v-else>
                <tr
                  v-for="item in items"
                  :key="item.id"
                  class="group border-b border-[#cfc4c5] text-sm hover:bg-[#f2f4f6]"
                >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center border border-[#cfc4c5] bg-[#eceef0] text-xs font-semibold text-[#4c4546]">
                      {{ item.name.slice(0, 2).toUpperCase() }}
                    </div>
                    <div>
                      <p class="text-sm font-semibold text-black">{{ item.name }}</p>
                      <p class="text-[11px] text-[#4c4546]">{{ getSku(item.id) }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 font-semibold text-black">{{ formatCurrency(item.price) }}</td>
                <td class="px-6 py-4 text-[#4c4546]">{{ item.stock_quantity }} unidades</td>
                <td class="px-6 py-4">
                  <span
                    class="inline-flex border border-[#cfc4c5] px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.08em]"
                    :class="getStatusClass(item.stock_quantity)"
                  >
                    {{ getStatusLabel(item.stock_quantity) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-end gap-1 opacity-60 transition group-hover:opacity-100">
                    <button
                      type="button"
                      class="inline-flex h-8 w-8 items-center justify-center border border-transparent text-[#4c4546] transition hover:border-[#cfc4c5] hover:bg-white"
                      aria-label="Editar produto"
                      @click="openEditModal(item)"
                    >
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 20H8L18.5 9.5L14.5 5.5L4 16V20Z" stroke="currentColor" stroke-width="1.5" />
                      </svg>
                    </button>

                    <button
                      type="button"
                      class="inline-flex h-8 w-8 items-center justify-center border border-transparent text-[#4c4546] transition hover:border-[#cfc4c5] hover:bg-white"
                      aria-label="Ver histórico do produto"
                      @click="openHistoryDrawer(item)"
                    >
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 8V12L15 15" stroke="currentColor" stroke-width="1.5" />
                        <path d="M3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12C21 16.9706 16.9706 21 12 21C8.2458 21 5.02829 18.7095 3.66943 15.4444" stroke="currentColor" stroke-width="1.5" />
                      </svg>
                    </button>

                    <button
                      type="button"
                      class="inline-flex h-8 w-8 items-center justify-center border border-transparent text-[#4c4546] transition hover:border-[#ffdad6] hover:bg-[#ffdad6] hover:text-[#93000a]"
                      aria-label="Excluir produto"
                      @click="openDeleteModal(item)"
                    >
                      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 7H20" stroke="currentColor" stroke-width="1.5" />
                        <path d="M9 7V5H15V7" stroke="currentColor" stroke-width="1.5" />
                        <path d="M7 7L8 19H16L17 7" stroke="currentColor" stroke-width="1.5" />
                        <path d="M10 11V16" stroke="currentColor" stroke-width="1.5" />
                        <path d="M14 11V16" stroke="currentColor" stroke-width="1.5" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              </template>
            </tbody>
          </table>
        </div>

        <div class="flex items-center justify-between border-t border-[#cfc4c5] bg-[#f7f9fb] px-6 py-4">
          <button
            type="button"
            class="inline-flex h-9 items-center gap-2 border border-[#cfc4c5] bg-white px-4 text-sm font-semibold text-[#191c1e] transition hover:bg-[#eceef0] focus-visible:ring-2 focus-visible:ring-black disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="!canGoPrev || isLoading"
            @click="goToPreviousPage"
          >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="1.75" />
            </svg>
            Anterior
          </button>

          <div class="flex items-center gap-2 text-sm">
            <span class="inline-flex h-8 min-w-8 items-center justify-center border border-black bg-black px-2 font-semibold text-white">{{ currentPage }}</span>
            <span class="text-[#4c4546]">/ {{ lastPage }}</span>
          </div>

          <button
            type="button"
            class="inline-flex h-9 items-center gap-2 border border-[#cfc4c5] bg-white px-4 text-sm font-semibold text-[#191c1e] transition hover:bg-[#eceef0] focus-visible:ring-2 focus-visible:ring-black disabled:cursor-not-allowed disabled:opacity-40"
            :disabled="!canGoNext || isLoading"
            @click="goToNextPage"
          >
            Proximo
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="1.75" />
            </svg>
          </button>
        </div>
      </div>

      <ProductFormModal
        :open="isModalOpen"
        :mode="modalMode"
        :product="editingProduct"
        @close="isModalOpen = false"
        @saved="onModalSaved"
      />

      <ProductDeleteModal
        :open="isDeleteModalOpen"
        :product="deletingProduct"
        :is-submitting="isDeleting"
        @close="isDeleteModalOpen = false"
        @confirm="onDeleteConfirmed"
      />

      <StockHistoryDrawer
        :open="isHistoryDrawerOpen"
        :product="historyProduct"
        @close="isHistoryDrawerOpen = false"
      />
    </div>
  </section>
</template>
