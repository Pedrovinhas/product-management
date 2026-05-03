import { storeToRefs } from 'pinia'
import { useProductsStore } from '@/modules/products/store/products.store'

export function useProductList() {
  const productsStore = useProductsStore()
  const { items, isLoading, hasProducts, currentPage, lastPage, total, from, to, filters } = storeToRefs(productsStore)

  return {
    items,
    isLoading,
    hasProducts,
    currentPage,
    lastPage,
    total,
    from,
    to,
    filters,
    fetchList: productsStore.fetchList,
  }
}
