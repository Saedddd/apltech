export const ENDPOINTS = {
  // Продукты
  PRODUCTS: 'api/products',
  PRODUCT: (id: number) => `api/product/${id}`,
  PRODUCT_CREATE: 'api/product/create',
  PRODUCT_UPDATE: (id: number) => `api/product/update/${id}`,
  PRODUCT_DELETE: (id: number) => `api/product/delete/${id}`,

  // Бренды
  BRAND: (name: string) => `api/product/brand/${name}`,

  // Ауз
  LOGIN: 'api/auth/login',
} as const

export type Endpoints = typeof ENDPOINTS
