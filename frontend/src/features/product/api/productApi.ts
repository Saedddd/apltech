import { axiosInstance } from '@shared/api/axiosInstance'
import { ENDPOINTS } from '@shared/api/endpoints'
import type { Product, ProductFormData, BrandMinMaxResponse } from '@shared/types/global'

export const productApi = {
  getAll: async (): Promise<Product[]> => {
    const response = await axiosInstance.get(ENDPOINTS.PRODUCTS)
    return response.data
  },

  getById: async (id: number): Promise<Product> => {
    const response = await axiosInstance.get(ENDPOINTS.PRODUCT(id))
    return response.data
  },

  create: async (data: ProductFormData): Promise<Product> => {
    const response = await axiosInstance.post(ENDPOINTS.PRODUCT_CREATE, data)
    return response.data
  },

  update: async (id: number, data: Partial<ProductFormData>): Promise<Product> => {
    const response = await axiosInstance.patch(ENDPOINTS.PRODUCT_UPDATE(id), data)
    return response.data
  },

  delete: async (id: number): Promise<void> => {
    await axiosInstance.delete(ENDPOINTS.PRODUCT(id))
  },

  getBrandMinMax: async (brandName: string): Promise<BrandMinMaxResponse> => {
    const response = await axiosInstance.get(ENDPOINTS.BRAND(brandName))
    return response.data
  },
}
