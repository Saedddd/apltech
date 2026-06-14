<template>
  <div class="product-edit-page">
    <div class="mb-4">
      <Button label="Назад" icon="pi pi-arrow-left" text @click="router.back()" />
    </div>

    <div v-if="loading" class="flex justify-center py-8">
      <ProgressSpinner />
    </div>

    <div v-else-if="error">
      <Message severity="error" :text="error" />
    </div>

    <Card v-else-if="productForForm">
      <template #title>Редактирование товара</template>
      <template #content>
        <ProductForm
          :initial-data="productForForm"
          :loading="updating"
          @submit="handleUpdate"
          @cancel="router.back()"
        />
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useProductStore } from '@features/product/model/productStore'
import { Card, Button, ProgressSpinner, Message } from '@shared/ui/primevue'
import { useToast } from 'primevue/usetoast'
import ProductForm from '@features/product/ui/ProductForm.vue'
import type { ProductFormData } from '@shared/types/global'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const productStore = useProductStore()
const updating = ref(false)

const { currentProduct: product, loading, error } = storeToRefs(productStore)

// Преобразуем данные продукта для формы
const productForForm = computed<ProductFormData | null>(() => {
  if (!product.value) return null

  return {
    name: product.value.name,
    category_name: product.value.category_name || '',
    brand_name: product.value.brand_name || '',
    price: typeof product.value.price === 'number' ? product.value.price : 0,
    rrp_price: product.value.rrp_price || 0,
    status: product.value.status || 1,
  }
})

const handleUpdate = async (data: ProductFormData) => {
  updating.value = true
  const result = await productStore.updateProduct(Number(route.params.id), data)
  updating.value = false

  if (result.success) {
    toast.add({
      severity: 'success',
      summary: 'Успешно',
      detail: 'Товар обновлен',
      life: 3000,
    })
    router.push(`/product/${route.params.id}`)
  } else {
    toast.add({
      severity: 'error',
      summary: 'Ошибка',
      detail: result.error,
      life: 5000,
    })
  }
}

onMounted(() => {
  productStore.fetchProductById(Number(route.params.id))
})
</script>
