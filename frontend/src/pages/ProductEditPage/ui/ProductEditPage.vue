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

    <Card v-else-if="product">
      <template #title>Редактирование товара</template>
      <template #content>
        <ProductForm
          :initial-data="product"
          :loading="updating"
          @submit="handleUpdate"
          @cancel="router.back()"
        />
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
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
