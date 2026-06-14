<template>
  <div class="min-h-screen bg-gray-100">
    <Header />

    <main class="container mx-auto px-4 py-8">
      <router-view />
    </main>

    <Toast position="bottom-right" />
    <ConfirmDialog />

    <!-- Глобальная модалка логина -->
    <Dialog
      v-model:visible="showLoginModal"
      header="Авторизация"
      :draggable="false"
      :modal="true"
      :style="{ width: '450px' }"
      :closable="true"
      :dismissableMask="true"
    >
      <LoginForm @success="handleLoginSuccess" @cancel="showLoginModal = false" />
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@features/auth/model/authStore'
import Header from '@widgets/Header/ui/Header.vue'
import Toast from 'primevue/toast'
import ConfirmDialog from 'primevue/confirmdialog'
import Dialog from 'primevue/dialog'
import LoginForm from '@features/auth/ui/LoginForm.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { isAuthenticated } = storeToRefs(authStore)

const showLoginModal = ref(false)
const returnUrlPath = ref<string | null>(null)

watch(
  () => route.query.showLogin,
  (showLogin) => {
    if (showLogin === 'true' && !isAuthenticated.value) {
      showLoginModal.value = true

      returnUrlPath.value = (route.query.returnUrl as string) || null
    }
  },
  { immediate: true }
)

watch(
  () => route.fullPath,
  () => {
    if (route.query.showLogin === 'true' && !isAuthenticated.value) {
      showLoginModal.value = true
      returnUrlPath.value = (route.query.returnUrl as string) || null
    }
  }
)

const handleLoginSuccess = () => {
  showLoginModal.value = false

  if (returnUrlPath.value) {
    router.push(returnUrlPath.value)
    returnUrlPath.value = null
  }
}
</script>
