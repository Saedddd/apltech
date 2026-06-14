<template>
  <div class="products-page">
    <Card>
      <template #title>
        <div class="flex justify-between gap-4 items-center w-full">
          <span class="text-4xl font-bold">Список товаров</span>
          <Button
            label="Создать товар"
            severity="success"
            @click="router.push('/product/create')"
          />
        </div>
      </template>

      <template #content>
        <DataTable
          :value="products"
          :loading="loading"
          :paginator="true"
          :rows="10"
          :rowsPerPageOptions="[5, 10, 20]"
          stripedRows
          responsiveLayout="scroll"
        >
          <Column field="id" header="ID" style="width: 80px" />
          <Column field="name" header="Название" sortable style="min-width: 200px" />
          <Column field="brand_name" header="Бренд" sortable style="width: 150px" />
          <Column field="category_name" header="Категория" sortable style="width: 150px" />
          <Column field="price" header="Цена" style="width: 150px">
            <template #body="{ data }">
              <Tag :value="formatPrice(data.price)" />
            </template>
          </Column>
          <Column field="status" header="Статус" style="width: 120px">
            <template #body="{ data }">
              <Tag
                :value="data.status === 1 ? 'В наличии' : 'Под заказ'"
                :severity="data.status === 1 ? 'success' : 'warning'"
              />
            </template>
          </Column>
          <Column header="Действия" style="width: 120px">
            <template #body="{ data }">
              <div class="flex gap-1">
                <Button severity="info" text rounded @click="router.push(`/product/${data.id}`)"
                  >👀</Button
                >
                <Button
                  severity="warning"
                  text
                  rounded
                  @click="router.push(`/product/edit/${data.id}`)"
                  >✎</Button
                >
                <Button severity="danger" text rounded @click="confirmDelete(data)">❌</Button>
              </div>
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <ConfirmDialog />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useProductStore } from '@features/product/model/productStore'
import { Card, Button, DataTable, Column, Tag, ConfirmDialog } from '@shared/ui/primevue'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import { formatPrice } from '@shared/lib/formatPrice'

const router = useRouter()
const confirm = useConfirm()
const toast = useToast()
const productStore = useProductStore()

const { products, loading } = storeToRefs(productStore)

const confirmDelete = (product: any) => {
  confirm.require({
    message: `Вы уверены, что хотите удалить товар "${product.name}"?`,
    header: 'Подтверждение удаления',
    acceptLabel: 'Да, удалить',
    rejectLabel: 'Отмена',
    acceptClass: 'p-button-danger',
    rejectClass: 'p-button-secondary',
    accept: async () => {
      const success = await productStore.deleteProduct(product.id)

      confirm.close()

      if (success) {
        toast.add({
          severity: 'success',
          summary: 'Удалено',
          detail: `Товар "${product.name}" удален`,
          life: 3000,
        })

        await productStore.fetchProducts()
      } else {
        toast.add({
          severity: 'error',
          summary: 'Ошибка',
          detail: 'Не удалось удалить товар',
          life: 3000,
        })
      }
    },
    reject: () => {
      confirm.close()
    },
  })
}

onMounted(() => {
  productStore.fetchProducts()
})
</script>
