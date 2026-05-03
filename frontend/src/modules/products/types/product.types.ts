export interface Product {
  id: number
  name: string
  description: string | null
  price: number
  stock_quantity: number
  deleted_at: string | null
  created_at: string
  updated_at: string
}

export interface PaginationMeta {
  current_page: number
  from: number | null
  last_page: number
  path: string
  per_page: number
  to: number | null
  total: number
}

export interface PaginationLinks {
  first: string | null
  last: string | null
  prev: string | null
  next: string | null
}

export interface PaginatedPayload<T> {
  data: T[]
  meta: PaginationMeta
  links: PaginationLinks
}

export interface ApiEnvelope<T> {
  data: T
  message: string
  errors: unknown[]
}

export interface ProductListFilters {
  search?: string
  min_price?: number
  max_price?: number
  min_stock?: number
  max_stock?: number
  page?: number
}

export interface ProductMutationRequest {
  name: string
  description: string | null
  price: number
  stock_quantity: number
}

export interface StockHistoryUser {
  id: number
  name: string
}

export type StockType = 'entrada' | 'saída' | 'ajuste'

export interface StockHistoryEntry {
  id: number
  product_id: number
  user: StockHistoryUser
  type: StockType
  quantity: number
  previous_stock: number
  current_stock: number
  created_at: string
}
