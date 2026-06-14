<template>
  <Menubar :model="menuItems">
    <template #start>
      <div class="flex items-center cursor-pointer" @click="navigateTo('/products')">
        <span class="text-xl font-bold">Apltech</span>
      </div>
    </template>

    <template #end>
      <div class="flex items-center gap-2">
        <Button
          v-if="!isAuthenticated"
          label="Login"
          @click="openLoginModal"
          outlined
          size="small"
        />
        <div v-else class="flex items-center gap-2">
          <Badge :value="username || ''" severity="info" />
          <Button label="Logout" @click="logout" text size="small" />
        </div>
      </div>
    </template>
  </Menubar>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@features/auth/model/authStore'
import Menubar from 'primevue/menubar'
import Button from 'primevue/button'
import Badge from 'primevue/badge'

const router = useRouter()
const authStore = useAuthStore()
const { isAuthenticated, username } = storeToRefs(authStore)

const getMenuItems = () => {
  const items = [
    {
      label: 'Товары',
      command: () => router.push('/products'),
    },
    {
      label: 'Поиск по бренду',
      command: () => router.push('/brand/search'),
    },
  ]

  if (isAuthenticated.value) {
    items.push({
      label: 'Создать товар',
      command: () => router.push('/product/create'),
    })
  }

  return items
}

const menuItems = ref(getMenuItems())

watch(isAuthenticated, () => {
  menuItems.value = getMenuItems()
})

const navigateTo = (path: string) => {
  router.push(path)
}

const openLoginModal = () => {
  router.push({
    path: '/products',
    query: { showLogin: 'true' },
  })
}

const logout = () => {
  authStore.clearAuth()
  router.push('/products')
}
</script>
