<template>
  <div class="product-create-page">
    <div class="mb-4">
      <Button label="Назад" text @click="router.push('/products')" />
    </div>

    <Card>
      <template #title>Создание нового товара</template>
      <template #content>
        <ProductForm :loading="loading" @submit="handleCreate" />
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useProductStore } from '@features/product/model/productStore'
import { Card, Button } from '@shared/ui/primevue'
import { useToast } from 'primevue/usetoast'
import ProductForm from '@features/product/ui/ProductForm.vue'
import type { ProductFormData } from '@shared/types/global'

const router = useRouter()
const toast = useToast()
const productStore = useProductStore()
const { loading } = storeToRefs(productStore)

const handleCreate = async (data: ProductFormData) => {
  const result = await productStore.createProduct(data)

  if (result.success) {
    toast.add({
      severity: 'success',
      summary: 'Успешно',
      detail: 'Товар создан',
      life: 3000,
    })
    router.push('/products')
  } else {
    toast.add({
      severity: 'error',
      summary: 'Ошибка',
      detail: result.error || 'Не удалось создать товар',
      life: 5000,
    })
  }
}
</script>
