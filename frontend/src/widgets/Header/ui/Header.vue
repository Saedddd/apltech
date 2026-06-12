<template>
  <Menubar :model="menuItems">
    <template #start>
      <div class="flex items-center cursor-pointer" @click="navigateTo('/')">
        <span class="text-xl font-bold">Apltech</span>
      </div>
    </template>

    <template #end>
      <div class="flex items-center gap-2">
        <Button
          v-if="!isAuthenticated"
          label="Login"
          @click="showLoginModal = true"
          outlined
          size="small"
        />
        <div v-else class="flex items-center gap-2">
          <Badge :value="username" severity="info" />
          <Button label="Logout" @click="logout" text size="small" />
        </div>
      </div>
    </template>
  </Menubar>

  <Dialog v-model:visible="showLoginModal" header="Login" :modal="true" :style="{ width: '450px' }">
    <LoginForm @success="handleLoginSuccess" />
  </Dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import Menubar from 'primevue/menubar'
import Button from 'primevue/button'
import Badge from 'primevue/badge'
import Dialog from 'primevue/dialog'
import LoginForm from '@features/auth/ui/LoginForm.vue'

const router = useRouter()
const showLoginModal = ref(false)
const isAuthenticated = ref(!!localStorage.getItem('token'))
const username = ref(localStorage.getItem('username') || '')

const menuItems = ref([
  {
    label: 'Товары',
    command: () => router.push('/products'),
  },
  {
    label: 'Создать товар',
    command: () => router.push('/product/create'),
  },
  {
    label: 'Поиск по бренду',
    command: () => router.push('/brand/search'),
  },
])

const navigateTo = (path: string) => {
  router.push(path)
}

const handleLoginSuccess = (user: any) => {
  isAuthenticated.value = true
  username.value = user.username
  showLoginModal.value = false
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('username')
  isAuthenticated.value = false
  username.value = ''
  router.push('/')
}
</script>
