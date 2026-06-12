<template>
  <div class="product-edit-page">
    <div class="mb-4">
      <Button label="Назад к списку" text @click="router.push('/products')" />
    </div>

    <Card>
      <template #title>
        <span class="text-xl font-bold">Редактирование товара</span>
      </template>

      <template #content>
        <div v-if="loading" class="flex justify-center py-8">
          <ProgressSpinner />
        </div>

        <div v-else-if="error">
          <Message severity="error" :text="error" />
        </div>

        <form v-else-if="product" @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Название -->
          <div>
            <label class="block font-semibold mb-2">
              Название <span class="text-red-500">*</span>
            </label>
            <InputText
              v-model="formData.name"
              :class="{ 'p-invalid': errors.name }"
              placeholder="Введите название товара"
              class="w-full"
            />
            <small v-if="errors.name" class="text-red-500">{{ errors.name }}</small>
          </div>

          <!-- Бренд и Категория -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold mb-2">Бренд</label>
              <Dropdown
                v-model="formData.brand_name"
                :options="brands"
                placeholder="Выберите бренд"
                class="w-full"
                editable
                :showClear="true"
              />
            </div>

            <div>
              <label class="block font-semibold mb-2">Категория</label>
              <Dropdown
                v-model="formData.category_name"
                :options="categories"
                placeholder="Выберите категорию"
                class="w-full"
                editable
                :showClear="true"
              />
            </div>
          </div>

          <!-- Цена и РРЦ -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold mb-2">
                Цена <span class="text-red-500">*</span>
              </label>
              <InputNumber
                v-model="formData.price"
                :class="{ 'p-invalid': errors.price }"
                placeholder="Введите цену"
                :min="1"
                :max="999999999"
                mode="currency"
                currency="RUB"
                locale="ru-RU"
                class="w-full"
              />
              <small v-if="errors.price" class="text-red-500">{{ errors.price }}</small>
            </div>

            <div>
              <label class="block font-semibold mb-2">Рекомендованная цена (РРЦ)</label>
              <InputNumber
                v-model="formData.rrp_price"
                placeholder="Введите РРЦ"
                :min="0"
                :max="999999999"
                mode="currency"
                currency="RUB"
                locale="ru-RU"
                class="w-full"
              />
            </div>
          </div>

          <!-- Статус -->
          <div>
            <label class="block font-semibold mb-2">Статус</label>
            <div class="flex gap-4">
              <div class="flex items-center gap-2">
                <RadioButton v-model="formData.status" :value="1" inputId="status_in_stock" />
                <label for="status_in_stock">В наличии</label>
              </div>
              <div class="flex items-center gap-2">
                <RadioButton v-model="formData.status" :value="2" inputId="status_on_order" />
                <label for="status_on_order">Под заказ</label>
              </div>
            </div>
          </div>

          <!-- Кнопки -->
          <div class="flex gap-2 justify-end pt-4 border-t">
            <Button
              type="button"
              label="Отмена"
              severity="secondary"
              outlined
              @click="router.push(`/product/${product.id}`)"
            />
            <Button type="submit" label="Сохранить" severity="success" :loading="updating" />
          </div>
        </form>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductStore } from '@features/product/model/productStore'
import {
  Card,
  Button,
  InputText,
  InputNumber,
  Dropdown,
  RadioButton,
  ProgressSpinner,
  Message,
} from '@shared/ui/primevue'
import { useToast } from 'primevue/usetoast'
import { validateName, validatePrice } from '@shared/lib/validatePrice'
import type { ProductFormData } from '@shared/types/global'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const productStore = useProductStore()

const updating = ref(false)
const product = computed(() => productStore.currentProduct)
const loading = computed(() => productStore.loading)
const error = computed(() => productStore.error)

const formData = reactive<ProductFormData>({
  name: '',
  category_name: '',
  brand_name: '',
  price: 0,
  rrp_price: 0,
  status: 1,
})

const errors = reactive({
  name: '',
  price: '',
})

const brands = ref([
  'Apple',
  'Samsung',
  'Dell',
  'Lenovo',
  'Asus',
  'HP',
  'Xiaomi',
  'Google',
  'Microsoft',
  'Sony',
])

const categories = ref([
  'Смартфоны',
  'Ноутбуки',
  'Планшеты',
  'Наушники',
  'Аксессуары',
  'Мониторы',
  'Клавиатуры',
  'Мыши',
])

const validateForm = (): boolean => {
  let isValid = true

  const nameError = validateName(formData.name)
  if (nameError) {
    errors.name = nameError
    isValid = false
  } else {
    errors.name = ''
  }

  const priceError = validatePrice(formData.price)
  if (priceError) {
    errors.price = priceError
    isValid = false
  } else {
    errors.price = ''
  }

  return isValid
}

const handleSubmit = async () => {
  if (!validateForm()) {
    toast.add({
      severity: 'error',
      summary: 'Ошибка валидации',
      detail: 'Пожалуйста, исправьте ошибки в форме',
      life: 5000,
    })
    return
  }

  updating.value = true
  const success = await productStore.updateProduct(Number(route.params.id), formData)
  updating.value = false

  if (success) {
    toast.add({
      severity: 'success',
      summary: 'Успешно',
      detail: 'Товар успешно обновлен',
      life: 3000,
    })
    router.push(`/product/${route.params.id}`)
  } else {
    toast.add({
      severity: 'error',
      summary: 'Ошибка',
      detail: productStore.error || 'Не удалось обновить товар',
      life: 5000,
    })
  }
}

// Загружаем данные товара при монтировании
onMounted(async () => {
  await productStore.fetchProductById(Number(route.params.id))

  // Заполняем форму данными товара
  if (product.value) {
    formData.name = product.value.name
    formData.category_name = product.value.category_name
    formData.brand_name = product.value.brand_name
    formData.price = typeof product.value.price === 'number' ? product.value.price : 0
    formData.rrp_price = product.value.rrp_price
    formData.status = product.value.status
  }
})
</script>

<script lang="ts">
// Добавляем недостающие импорты для ref и computed
import { ref, computed } from 'vue'
</script>

<style scoped>
@reference "tailwindcss";

.product-edit-page {
  @apply max-w-2xl mx-auto;
}
</style>
