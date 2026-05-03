import { http } from '@/shared/api/http'
import type {
  ApiEnvelope,
  PaginatedPayload,
  Product,
  ProductListFilters,
  ProductMutationRequest,
  StockHistoryEntry,
} from '@/modules/products/types/product.types'

export class ProductsService {
  async list(filters: ProductListFilters = {}) {
    const { data } = await http.get<ApiEnvelope<PaginatedPayload<Product>>>('/products', {
      params: filters,
    })

    return data.data
  }

  async create(payload: ProductMutationRequest) {
    const { data } = await http.post<ApiEnvelope<Product>>('/products', payload)

    return data.data
  }

  async update(id: number, payload: ProductMutationRequest) {
    const { data } = await http.put<ApiEnvelope<Product>>(`/products/${id}`, payload)

    return data.data
  }

  async remove(id: number) {
    await http.delete(`/products/${id}`)
  }

  async stockHistory(productId: number, page = 1) {
    const { data } = await http.get<ApiEnvelope<PaginatedPayload<StockHistoryEntry>>>(
      `/products/${productId}/stock-history`,
      {
        params: { page },
      },
    )

    return data.data
  }
}

export const productsService = new ProductsService()
