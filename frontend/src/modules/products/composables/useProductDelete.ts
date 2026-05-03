import { ref, type Ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useToast } from '@/shared/composables/useToast'
import { useProductsStore } from '@/modules/products/store/products.store'
import { productsService } from '@/modules/products/services/products.service'

export function useProductDelete() {
  const { success, error } = useToast()
  const productsStore = useProductsStore()
  const { currentPage } = storeToRefs(productsStore)
  const isDeleting = ref(false)

  async function deleteProduct(id: number) {
    isDeleting.value = true

    try {
      await productsService.remove(id)
      success('Produto excluido com sucesso.')
      await productsStore.fetchList({ page: currentPage.value })
      return true
    } catch {
      error('Nao foi possivel excluir o produto no momento.')
      return false
    } finally {
      isDeleting.value = false
    }
  }

  return {
    isDeleting: isDeleting as Ref<boolean>,
    deleteProduct,
  }
}
