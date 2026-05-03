import { ref, watch, type Ref } from 'vue'
import { productsService } from '@/modules/products/services/products.service'
import { useToast } from '@/shared/composables/useToast'
import type { Product, StockHistoryEntry } from '@/modules/products/types/product.types'

export function useStockHistory(productRef: Ref<Product | null>, isOpenRef: Ref<boolean>) {
  const { error } = useToast()
  const isLoading = ref(false)
  const items = ref<StockHistoryEntry[]>([])
  const page = ref(1)
  const lastPage = ref(1)

  async function fetchPage(pageNum: number) {
    if (!productRef.value) return

    isLoading.value = true
    try {
      const payload = await productsService.stockHistory(productRef.value.id, pageNum)
      items.value = payload.data
      page.value = payload.meta.current_page
      lastPage.value = payload.meta.last_page
    } catch {
      error('Não foi possível carregar o histórico deste produto.')
    } finally {
      isLoading.value = false
    }
  }

  function nextPage() {
    if (page.value < lastPage.value) {
      fetchPage(page.value + 1)
    }
  }

  function previousPage() {
    if (page.value > 1) {
      fetchPage(page.value - 1)
    }
  }

  watch(
    isOpenRef,
    (isOpen) => {
      if (isOpen) {
        page.value = 1
        fetchPage(1)
      } else {
        items.value = []
        page.value = 1
      }
    },
  )

  return {
    isLoading,
    items,
    page,
    lastPage,
    fetchPage,
    nextPage,
    previousPage,
  }
}
