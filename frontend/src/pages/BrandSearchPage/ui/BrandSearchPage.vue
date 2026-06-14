<template>
  <div class="brand-search-page">
    <Card>
      <template #title>
        <span class="text-xl font-bold">Поиск товаров</span>
      </template>

      <template #content>
        <div class="mb-6">
          <SelectButton
            v-model="searchType"
            :options="searchOptions"
            optionLabel="label"
            optionValue="value"
          />
        </div>

        <div v-if="searchType === 'brand'" class="flex gap-4 mb-6">
          <div class="flex-1">
            <InputText
              v-model="brandName"
              placeholder="Введите название бренда (например: Apple, Samsung, Dell)"
              class="w-full"
              @keyup.enter="handleBrandSearch"
            />
          </div>
          <Button
            label="Найти"
            @click="handleBrandSearch"
            :loading="brandLoading"
            :disabled="!brandName.trim()"
          />
        </div>

        <div v-else class="flex gap-4 mb-6">
          <div class="flex-1">
            <InputNumber
              v-model="productId"
              placeholder="Введите ID товара (например: 1, 2, 3)"
              class="w-full"
              :min="1"
              @keyup.enter="handleIdSearch"
            />
          </div>
          <Button
            label="Найти"
            @click="handleIdSearch"
            :loading="productLoading"
            :disabled="!productId"
          />
        </div>

        <template v-if="searchType === 'brand'">
          <div v-if="brandLoading" class="flex justify-center py-8">
            <ProgressSpinner />
          </div>

          <div v-else-if="brandError">
            <Message severity="error" :text="brandError" />
          </div>

          <div v-else-if="brandResult" class="grid grid-cols-2 gap-6">
            <div class="text-center">
              <div class="bg-green-50 rounded-lg p-6">
                <i class="pi pi-arrow-down text-green-500 text-2xl mb-2 block"></i>
                <div class="text-2xl font-bold text-green-600">
                  {{ formatPrice(brandResult.min.price) }}
                </div>
                <div class="text-gray-600 mt-2">ID товара: {{ brandResult.min.id }}</div>
                <div v-if="brandResult.min.source" class="text-xs text-gray-400 mt-1">
                  Источник: {{ brandResult.min.source }}
                </div>
                <Button
                  label="Посмотреть товар"
                  text
                  @click="router.push(`/product/${brandResult.min.id}`)"
                  class="mt-3"
                />
              </div>
            </div>

            <div class="text-center">
              <div class="bg-red-50 rounded-lg p-6">
                <i class="pi pi-arrow-up text-red-500 text-2xl mb-2 block"></i>
                <div class="text-2xl font-bold text-red-600">
                  {{ formatPrice(brandResult.max.price) }}
                </div>
                <div class="text-gray-600 mt-2">ID товара: {{ brandResult.max.id }}</div>
                <div v-if="brandResult.max.source" class="text-xs text-gray-400 mt-1">
                  Источник: {{ brandResult.max.source }}
                </div>
                <Button
                  label="Посмотреть товар"
                  text
                  @click="router.push(`/product/${brandResult.max.id}`)"
                  class="mt-3"
                />
              </div>
            </div>
          </div>

          <div
            v-else-if="brandSearched && !brandLoading && !brandError"
            class="text-center py-8 text-gray-500"
          >
            <p>Товары для бренда "{{ brandName }}" не найдены</p>
            <p class="text-sm mt-2">Попробуйте другой бренд</p>
          </div>
        </template>

        <template v-else>
          <div v-if="productLoading" class="flex justify-center py-8">
            <ProgressSpinner />
          </div>

          <div v-else-if="productError && typeof productError === 'string'">
            <Message severity="error" :text="productError" />
          </div>

          <div v-else-if="product && product.id" class="bg-white rounded-lg p-6 border">
            <div class="flex justify-between items-start mb-4">
              <h2 class="text-2xl text-blue-600 font-bold">{{ product.name }}</h2>
              <Tag
                :value="product.status === 1 ? 'В наличии' : 'Под заказ'"
                :severity="product.status === 1 ? 'success' : 'warning'"
              />
            </div>

            <Divider />

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="font-semibold text-gray-600">ID:</label>
                <p>{{ product.id }}</p>
              </div>
              <div>
                <label class="font-semibold text-gray-600">Бренд:</label>
                <p>{{ product.brand_name || '-' }}</p>
              </div>
              <div>
                <label class="font-semibold text-gray-600">Категория:</label>
                <p>{{ product.category_name || '-' }}</p>
              </div>
              <div>
                <label class="font-semibold text-gray-600">Цена:</label>
                <p class="text-xl text-green-600 font-bold">{{ formatPrice(product.price) }}</p>
              </div>
              <div>
                <label class="font-semibold text-gray-600">Рекомендованная цена:</label>
                <p>{{ formatPrice(product.rrp_price) }}</p>
              </div>
            </div>

            <div class="mt-6 flex gap-2">
              <Button label="Посмотреть детали" @click="router.push(`/product/${product.id}`)" />
              <Button
                v-if="isAuthenticated"
                label="Редактировать"
                severity="warning"
                @click="router.push(`/product/edit/${product.id}`)"
              />
            </div>
          </div>

          <div
            v-else-if="idSearched && !productLoading && !productError"
            class="text-center py-8 text-gray-500"
          >
            <i class="pi pi-search text-4xl mb-2 block opacity-50"></i>
            <p>Товар с ID "{{ productId }}" не найден</p>
            <p class="text-sm mt-2">Проверьте ID и попробуйте снова</p>
          </div>
        </template>

        <div
          v-if="!brandSearched && !idSearched && !brandLoading && !productLoading"
          class="text-center py-8 text-gray-500"
        >
          <p>
            {{
              searchType === 'brand'
                ? 'Введите название бренда для поиска'
                : 'Введите ID товара для поиска'
            }}
          </p>
          <p class="text-sm mt-2">
            {{
              searchType === 'brand'
                ? 'Примеры: Apple, Samsung, Dell, Lenovo'
                : 'Примеры: 1, 2, 3, 10'
            }}
          </p>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@features/auth/model/authStore'
import { useBrandStore } from '@features/product/model/brandStore'
import { useProductStore } from '@features/product/model/productStore'
import {
  Card,
  Button,
  InputText,
  InputNumber,
  SelectButton,
  ProgressSpinner,
  Message,
  Tag,
  Divider,
} from '@shared/ui/primevue'
import { formatPrice } from '@shared/lib/formatPrice'

const router = useRouter()
const authStore = useAuthStore()
const brandStore = useBrandStore()
const productStore = useProductStore()

const searchType = ref<'brand' | 'id'>('brand')
const searchOptions = [
  { label: 'Поиск по бренду', value: 'brand' },
  { label: 'Поиск по ID', value: 'id' },
]

const brandName = ref('')
const brandSearched = ref(false)
const { brandResult, loading: brandLoading, error: brandError } = storeToRefs(brandStore)

const productId = ref<number | null>(null)
const idSearched = ref(false)
const {
  currentProduct: product,
  loading: productLoading,
  error: productError,
} = storeToRefs(productStore)
const { isAuthenticated } = storeToRefs(authStore)

watch(searchType, () => {
  brandStore.reset()
  productStore.currentProduct = null
  productStore.error = null
  brandSearched.value = false
  idSearched.value = false
  brandName.value = ''
  productId.value = null
})

const handleBrandSearch = async () => {
  if (!brandName.value.trim()) return
  brandSearched.value = true
  idSearched.value = false
  productStore.currentProduct = null
  productStore.error = null
  await brandStore.fetchBrandMinMax(brandName.value.trim())
}

const handleIdSearch = async () => {
  if (!productId.value) return
  idSearched.value = true
  brandSearched.value = false
  brandStore.brandResult = null
  brandStore.error = null
  await productStore.fetchProductById(productId.value)
}
</script>
