import { ref, type Ref } from 'vue'
import { productsService } from '@/modules/products/services/products.service'
import { useToast } from '@/shared/composables/useToast'
import { useProductsStore } from '@/modules/products/store/products.store'

export function useProductForm() {
  const { success, error } = useToast()
  const productsStore = useProductsStore()
  const isSubmitting = ref(false)

  async function createProduct(payload: {
    name: string
    description?: string | null
    price: number
    stock_quantity: number
  }) {
    isSubmitting.value = true
    try {
      const cleanPayload = {
        name: payload.name,
        description: payload.description ?? null,
        price: payload.price,
        stock_quantity: payload.stock_quantity,
      }
      await productsService.create(cleanPayload)
      success('Produto criado com sucesso.')
      await productsStore.fetchList()
      return true
    } catch {
      error('Nao foi possivel salvar o produto no momento.')
      return false
    } finally {
      isSubmitting.value = false
    }
  }

  async function updateProduct(
    id: number,
    payload: {
      name: string
      description?: string | null
      price: number
      stock_quantity: number
    },
  ) {
    isSubmitting.value = true
    try {
      const cleanPayload = {
        name: payload.name,
        description: payload.description ?? null,
        price: payload.price,
        stock_quantity: payload.stock_quantity,
      }
      await productsService.update(id, cleanPayload)
      success('Produto atualizado com sucesso.')
      await productsStore.fetchList()
      return true
    } catch {
      error('Nao foi possivel salvar o produto no momento.')
      return false
    } finally {
      isSubmitting.value = false
    }
  }

  return {
    isSubmitting: isSubmitting as Ref<boolean>,
    createProduct,
    updateProduct,
  }
}
