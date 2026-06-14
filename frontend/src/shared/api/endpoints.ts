export const ENDPOINTS = {
  // Продукты
  PRODUCTS: '/products',
  PRODUCT: (id: number) => `/product/${id}`,
  PRODUCT_CREATE: '/product/create',
  PRODUCT_UPDATE: (id: number) => `/product/update/${id}`,
  PRODUCT_DELETE: (id: number) => `/product/delete/${id}`,

  // Бренды
  BRAND: (name: string) => `/product/brand/${name}`,

  // Ауз
  LOGIN: '/auth/login',
} as const

export type Endpoints = typeof ENDPOINTS
