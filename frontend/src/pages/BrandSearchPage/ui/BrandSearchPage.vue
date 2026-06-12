<template>
  <div class="brand-search-page">
    <Card>
      <template #title>
        <span class="text-xl font-bold">Поиск по бренду</span>
      </template>

      <template #content>
        <!-- форма -->
        <div class="flex gap-4 mb-6">
          <div class="flex-1">
            <InputText
              v-model="brandName"
              placeholder="Введите название бренда (например: Apple, Samsung, Dell)"
              class="w-full"
              @keyup.enter="handleSearch"
            />
          </div>
          <Button
            label="Найти"
            severity="primary"
            @click="handleSearch"
            :loading="loading"
            :disabled="!brandName.trim()"
          />
        </div>

        <!-- спиннер -->
        <div v-if="loading" class="flex justify-center py-8">
          <ProgressSpinner />
        </div>

        <!-- ошибка -->
        <div v-else-if="error">
          <Message severity="error" :text="error" />
        </div>

        <div v-else-if="brandResult" class="space-y-6">
          <Divider />

          <div class="grid grid-cols-2 gap-6">
            <div class="text-center">
              <div class="bg-green-50 rounded-lg p-6">
                <div class="flex items-center justify-center mb-4">
                  <i class="pi pi-arrow-down text-green-500 text-2xl"></i>
                  <span class="ml-2 text-green-600 font-semibold">Минимальная цена</span>
                </div>
                <div class="text-3xl font-bold text-green-600 mb-2">
                  {{ formatPrice(brandResult.min.price) }}
                </div>
                <div class="text-gray-600">ID товара: {{ brandResult.min.id }}</div>
                <div v-if="brandResult.min.source" class="text-sm text-gray-400 mt-2">
                  Источник: {{ brandResult.min.source }}
                </div>
                <div class="mt-4">
                  <Button
                    label="Посмотреть товар"
                    text
                    @click="router.push(`/product/${brandResult.min.id}`)"
                  />
                </div>
              </div>
            </div>

            <div class="text-center">
              <div class="bg-red-50 rounded-lg p-6">
                <div class="flex items-center justify-center mb-4">
                  <i class="pi pi-arrow-up text-red-500 text-2xl"></i>
                  <span class="ml-2 text-red-600 font-semibold">Максимальная цена</span>
                </div>
                <div class="text-3xl font-bold text-red-600 mb-2">
                  {{ formatPrice(brandResult.max.price) }}
                </div>
                <div class="text-gray-600">ID товара: {{ brandResult.max.id }}</div>
                <div v-if="brandResult.max.source" class="text-sm text-gray-400 mt-2">
                  Источник: {{ brandResult.max.source }}
                </div>
                <div class="mt-4">
                  <Button
                    label="Посмотреть товар"
                    text
                    @click="router.push(`/product/${brandResult.max.id}`)"
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="bg-blue-50 rounded-lg p-4 text-sm text-blue-700">
            <i class="pi pi-info-circle mr-2"></i>
            Данные собраны из базы данных и JSON файла
          </div>
        </div>

        <div v-else-if="!searched && !loading" class="text-center py-8 text-gray-500">
          <p>Введите название бренда для поиска</p>
          <p class="text-sm mt-2">Примеры: Apple, Samsung, Dell, Lenovo</p>
        </div>

        <div
          v-else-if="searched && !brandResult && !loading && !error"
          class="text-center py-8 text-gray-500"
        >
          <i class="pi pi-exclamation-triangle text-4xl mb-2 opacity-50"></i>
          <p>Товары для бренда "{{ brandName }}" не найдены</p>
          <p class="text-sm mt-2">Попробуйте другой бренд</p>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useBrandStore } from '@features/product/model/brandStore'
import { Card, Button, InputText, ProgressSpinner, Message, Divider } from '@shared/ui/primevue'
import { formatPrice } from '@shared/lib/formatPrice'

const router = useRouter()
const brandStore = useBrandStore()
const brandName = ref('')
const searched = ref(false)

const brandResult = computed(() => brandStore.brandResult)
const loading = computed(() => brandStore.loading)
const error = computed(() => brandStore.error)

const handleSearch = async () => {
  if (!brandName.value.trim()) return

  searched.value = true
  await brandStore.fetchBrandMinMax(brandName.value.trim())
}
</script>

<style scoped>
@reference "tailwindcss";

.brand-search-page {
  @apply max-w-4xl mx-auto;
}
</style>
