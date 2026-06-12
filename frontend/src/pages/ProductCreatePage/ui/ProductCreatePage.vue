<template>
  <div class="product-create-page">
    <div class="mb-4">
      <Button label="Назад к списку" text @click="router.push('/products')" />
    </div>

    <Card>
      <template #title>
        <span class="text-xl font-bold">Создание нового товара</span>
      </template>

      <template #content>
        <form @submit.prevent="handleSubmit" class="space-y-6">
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

          <div class="flex gap-2 justify-end pt-4 border-t">
            <Button
              type="button"
              label="Отмена"
              severity="secondary"
              outlined
              @click="router.push('/products')"
            />
            <Button type="submit" label="Создать" severity="success" :loading="creating" />
          </div>
        </form>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useProductStore } from '@features/product/model/productStore'
import { Card, Button, InputText, InputNumber, Dropdown, RadioButton } from '@shared/ui/primevue'
import { useToast } from 'primevue/usetoast'
import { validateName, validatePrice } from '@shared/lib/validatePrice'
import type { ProductFormData } from '@shared/types/global'

const router = useRouter()
const toast = useToast()
const productStore = useProductStore()
const creating = ref(false)

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

  // Валидация названия
  const nameError = validateName(formData.name)
  if (nameError) {
    errors.name = nameError
    isValid = false
  } else {
    errors.name = ''
  }

  // Валидация цены
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

  creating.value = true
  const success = await productStore.createProduct(formData)
  creating.value = false

  if (success) {
    toast.add({
      severity: 'success',
      summary: 'Успешно',
      detail: 'Товар успешно создан',
      life: 3000,
    })
    router.push('/products')
  } else {
    toast.add({
      severity: 'error',
      summary: 'Ошибка',
      detail: productStore.error || 'Не удалось создать товар',
      life: 5000,
    })
  }
}
</script>

<style scoped>
@reference "tailwindcss";

.product-create-page {
  @apply max-w-2xl mx-auto;
}
</style>
