import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import type { Product, ProductListFilters } from '@/modules/products/types/product.types'
import { productsService } from '@/modules/products/services/products.service'

export const useProductsStore = defineStore('products', () => {
  const items = ref<Product[]>([])
  const isLoading = ref(false)
  const currentPage = ref(1)
  const lastPage = ref(1)
  const total = ref(0)
  const from = ref<number | null>(null)
  const to = ref<number | null>(null)
  const filters = ref<ProductListFilters>({
    search: '',
    page: 1,
  })

  const hasProducts = computed(() => items.value.length > 0)

  async function fetchList(nextFilters: ProductListFilters = {}) {
    isLoading.value = true

    try {
      filters.value = {
        ...filters.value,
        ...nextFilters,
      }

      const payload = await productsService.list(filters.value)

      items.value = payload.data
      currentPage.value = payload.meta.current_page
      lastPage.value = payload.meta.last_page
      total.value = payload.meta.total
      from.value = payload.meta.from
      to.value = payload.meta.to
    } finally {
      isLoading.value = false
    }
  }

  return {
    items,
    isLoading,
    currentPage,
    lastPage,
    total,
    from,
    to,
    filters,
    hasProducts,
    fetchList,
  }
})
