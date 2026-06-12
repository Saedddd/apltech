/* eslint-disable @typescript-eslint/no-empty-object-type */
// Типы для продуктов
export interface Product {
  id: number
  name: string
  category_name: string
  brand_name: string
  price: number | string
  rrp_price: number
  status: 1 | 2
}

export interface ProductFormData {
  name: string
  category_name?: string
  brand_name?: string
  price: number
  rrp_price?: number
  status?: 1 | 2
}

// Типы для ответа бренда
export interface BrandMinMaxItem {
  id: number
  price: number | string
  source?: string
}

export interface BrandMinMaxResponse {
  min: BrandMinMaxItem
  max: BrandMinMaxItem
}

export interface BrandMinMaxArrayResponse extends Array<
  { min: BrandMinMaxItem } | { max: BrandMinMaxItem }
> {}

// Типы для аутентификации
export interface LoginRequest {
  username: string
  password: string
}

export interface AuthResponse {
  status: string
  token: string
  user: {
    id: number
    username: string
  }
}

// Типы для API ошибок
export interface ApiError {
  name: string
  message: string
  code?: number
  errors?: Record<string, string[]>
}
