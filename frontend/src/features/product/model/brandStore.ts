import { defineStore } from 'pinia'
import { ref } from 'vue'
import { productApi } from '../api/productApi'

interface BrandMinMaxItem {
  id: number
  price: number | string
  source?: string
}

interface BrandResult {
  min: BrandMinMaxItem
  max: BrandMinMaxItem
}

export const useBrandStore = defineStore('brand', () => {
  const brandResult = ref<BrandResult | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchBrandMinMax = async (brandName: string) => {
    loading.value = true
    error.value = null
    brandResult.value = null
    try {
      const response = await productApi.getBrandMinMax(brandName)
      const minData = response[0] && 'min' in response[0] ? response[0].min : null
      const maxData = response[1] && 'max' in response[1] ? response[1].max : null

      if (minData && maxData) {
        brandResult.value = {
          min: minData,
          max: maxData,
        }
      } else {
        error.value = 'Неверный формат ответа от сервера'
      }
    } catch (err: any) {
      if (err.response?.status === 404) {
        error.value = `Товары для бренда "${brandName}" не найдены`
      } else {
        error.value = err.response?.data?.error || err.message || 'Ошибка поиска'
      }
      brandResult.value = null
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  const reset = () => {
    brandResult.value = null
    loading.value = false
    error.value = null
  }

  return {
    brandResult,
    loading,
    error,
    fetchBrandMinMax,
    reset,
  }
})
