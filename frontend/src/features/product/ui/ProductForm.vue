<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div>
      <label class="block font-semibold mb-2"> Название <span class="text-red-500">*</span> </label>
      <InputText
        v-model="formData.name"
        class="w-full"
        :class="{ 'p-invalid': errors.name }"
        @blur="validateField('name')"
      />
      <small v-if="errors.name" class="text-red-500">{{ errors.name }}</small>
      <small v-else class="text-gray-400"
        >Обязательное поле, не должно содержать слово "test"</small
      >
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block font-semibold mb-2">Бренд</label>
        <InputText v-model="formData.brand_name" class="w-full" />
      </div>

      <div>
        <label class="block font-semibold mb-2">Категория</label>
        <InputText v-model="formData.category_name" class="w-full" />
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block font-semibold mb-2"> Цена <span class="text-red-500">*</span> </label>
        <InputNumber
          v-model="formData.price"
          class="w-full"
          :min="1"
          :class="{ 'p-invalid': errors.price }"
          @blur="validateField('price')"
        />
        <small v-if="errors.price" class="text-red-500">{{ errors.price }}</small>
        <small v-else class="text-gray-400">Обязательное поле, цена должна быть больше 0</small>
      </div>

      <div>
        <label class="block font-semibold mb-2">Рекомендованная цена</label>
        <InputNumber v-model="formData.rrp_price" class="w-full" :min="0" />
      </div>
    </div>

    <div>
      <label class="block font-semibold mb-2">Статус</label>
      <SelectButton
        v-model="formData.status"
        :options="statusOptions"
        optionLabel="label"
        optionValue="value"
      />
    </div>

    <div class="flex gap-2 justify-end pt-4 border-t">
      <Button type="button" label="Отмена" severity="secondary" outlined @click="$emit('cancel')" />
      <Button type="submit" label="Сохранить" :loading="loading" />
    </div>
  </form>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue'
import { InputText, InputNumber, SelectButton, Button } from '@shared/ui/primevue'
import type { ProductFormData } from '@shared/types/global'

interface Props {
  initialData?: ProductFormData | null
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  initialData: null,
  loading: false,
})

const emit = defineEmits<{
  submit: [data: ProductFormData]
  cancel: []
}>()

const formData = reactive<ProductFormData>({
  name: '',
  brand_name: '',
  category_name: '',
  price: 0,
  rrp_price: 0,
  status: 1,
})

const errors = reactive({
  name: '',
  price: '',
})

const statusOptions = [
  { label: 'В наличии', value: 1 },
  { label: 'Под заказ', value: 2 },
]

const validateField = (field: 'name' | 'price') => {
  if (field === 'name') {
    if (!formData.name || formData.name.trim() === '') {
      errors.name = 'Название обязательно для заполнения'
    } else if (/test/i.test(formData.name)) {
      errors.name = 'Название не должно содержать слово "test"'
    } else {
      errors.name = ''
    }
  }

  if (field === 'price') {
    if (formData.price === undefined || formData.price === null || formData.price === 0) {
      errors.price = 'Цена обязательна и должна быть больше 0'
    } else if (formData.price < 0) {
      errors.price = 'Цена не может быть отрицательной'
    } else {
      errors.price = ''
    }
  }
}

const validateForm = (): boolean => {
  validateField('name')
  validateField('price')
  return !errors.name && !errors.price
}

const handleSubmit = () => {
  if (!validateForm()) {
    console.log('Validation errors:', errors)
    return
  }

  emit('submit', { ...formData })
}

watch(
  () => formData.name,
  () => {
    if (errors.name) validateField('name')
  }
)

watch(
  () => formData.price,
  () => {
    if (errors.price) validateField('price')
  }
)

watch(
  () => props.initialData,
  (newData) => {
    if (newData) {
      formData.name = newData.name || ''
      formData.brand_name = newData.brand_name || ''
      formData.category_name = newData.category_name || ''
      formData.price = typeof newData.price === 'number' ? newData.price : 0
      formData.rrp_price = newData.rrp_price || 0
      formData.status = newData.status || 1

      errors.name = ''
      errors.price = ''
    }
  },
  { immediate: true }
)
</script>
