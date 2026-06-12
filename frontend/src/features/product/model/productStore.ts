/* eslint-disable @typescript-eslint/no-explicit-any */
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { productApi } from '../api/productApi'
import type { Product, ProductFormData } from '@shared/types/global'

export const useProductStore = defineStore('product', () => {
  const products = ref<Product[]>([])
  const currentProduct = ref<Product | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchProducts = async () => {
    loading.value = true
    error.value = null
    try {
      products.value = await productApi.getAll()
    } catch (err: any) {
      error.value = err.message || 'Ошибка загрузки товаров'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const fetchProductById = async (id: number) => {
    loading.value = true
    error.value = null
    try {
      currentProduct.value = await productApi.getById(id)
    } catch (err: any) {
      error.value = err.message || 'Ошибка загрузки товара'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const createProduct = async (data: ProductFormData) => {
    loading.value = true
    error.value = null
    try {
      const newProduct = await productApi.create(data)
      products.value.unshift(newProduct)
      return true
    } catch (err: any) {
      error.value = err.message || 'Ошибка создания товара'
      console.error(err)
      return false
    } finally {
      loading.value = false
    }
  }

  const updateProduct = async (id: number, data: ProductFormData) => {
    loading.value = true
    error.value = null
    try {
      const updated = await productApi.update(id, data)
      const index = products.value.findIndex((p) => p.id === id)
      if (index !== -1) products.value[index] = updated
      if (currentProduct.value?.id === id) currentProduct.value = updated
      return true
    } catch (err: any) {
      error.value = err.message || 'Ошибка обновления товара'
      console.error(err)
      return false
    } finally {
      loading.value = false
    }
  }

  const deleteProduct = async (id: number) => {
    loading.value = true
    error.value = null
    try {
      await productApi.delete(id)
      products.value = products.value.filter((p) => p.id !== id)
      if (currentProduct.value?.id === id) currentProduct.value = null
      return true
    } catch (err: any) {
      error.value = err.message || 'Ошибка удаления товара'
      console.error(err)
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    products,
    currentProduct,
    loading,
    error,
    fetchProducts,
    fetchProductById,
    createProduct,
    updateProduct,
    deleteProduct,
  }
})
