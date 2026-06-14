<template>
  <div class="product-detail-page">
    <div class="mb-4">
      <Button label="Назад к списку" text @click="router.push('/products')" />
    </div>

    <div v-if="loading" class="flex justify-center py-8">
      <ProgressSpinner />
    </div>

    <div v-else-if="error">
      <Message severity="error" :text="error" />
    </div>

    <div v-else-if="product">
      <Card>
        <template #title>
          <div class="flex justify-between items-center">
            <span class="text-xl font-bold">{{ product.name }}</span>
            <Badge
              :value="product.status === 1 ? 'В наличии' : 'Под заказ'"
              :severity="product.status === 1 ? 'success' : 'warning'"
            />
          </div>
        </template>

        <template #content>
          <div class="space-y-3">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="font-semibold text-gray-600">ID:</label>
                <p>{{ product.id }}</p>
              </div>
              <div>
                <label class="font-semibold text-gray-600">Категория:</label>
                <p>{{ product.category_name || '-' }}</p>
              </div>
              <div>
                <label class="font-semibold text-gray-600">Бренд:</label>
                <p>{{ product.brand_name || '-' }}</p>
              </div>
              <div>
                <label class="font-semibold text-gray-600">Рекомендованная цена:</label>
                <p>{{ formatPrice(product.rrp_price) }}</p>
              </div>
              <div>
                <label class="font-semibold text-gray-600">Цена:</label>
                <p class="text-xl text-green-600 font-bold">{{ formatPrice(product.price) }}</p>
              </div>
            </div>
          </div>
        </template>

        <template #footer>
          <div class="flex gap-2">
            <template v-if="product.source !== 'json'">
              <Button
                label="Редактировать"
                icon="pi pi-pencil"
                severity="warning"
                @click="router.push(`/product/edit/${product.id}`)"
              />
              <Button label="Удалить" icon="pi pi-trash" severity="danger" @click="confirmDelete" />
            </template>
            <span v-else class="text-gray-500 text-sm">
              ⓘ Этот товар из внешнего источника, редактирование недоступно
            </span>
          </div>
        </template>
      </Card>
    </div>

    <ConfirmDialog />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useProductStore } from '@features/product/model/productStore'
import { Card, Button, Badge, ProgressSpinner, Message, ConfirmDialog } from '@shared/ui/primevue'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import { formatPrice } from '@shared/lib/formatPrice'

const route = useRoute()
const router = useRouter()
const confirm = useConfirm()
const toast = useToast()
const productStore = useProductStore()

const { currentProduct: product, loading, error } = storeToRefs(productStore)

const confirmDelete = () => {
  confirm.require({
    message: `Вы уверены, что хотите удалить товар "${product.value?.name}"?`,
    header: 'Подтверждение удаления',
    acceptLabel: 'Да, удалить',
    rejectLabel: 'Отмена',
    acceptClass: 'p-button-danger',
    accept: async () => {
      const success = await productStore.deleteProduct(Number(route.params.id))
      if (success) {
        toast.add({
          severity: 'success',
          summary: 'Удалено',
          detail: 'Товар успешно удален',
          life: 3000,
        })
        router.push('/products')
      }
    },
  })
}

onMounted(() => {
  productStore.fetchProductById(Number(route.params.id))
})
</script>
