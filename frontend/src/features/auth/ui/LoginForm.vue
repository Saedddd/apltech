<template>
  <form @submit.prevent="handleLogin" class="space-y-4">
    <div>
      <label class="block font-semibold mb-2">Логин</label>
      <InputText v-model="username" placeholder="Введите логин" class="w-full" required />
    </div>

    <div>
      <label class="block font-semibold mb-2">Пароль</label>
      <Password
        v-model="password"
        placeholder="Введите пароль"
        class="w-full"
        :feedback="false"
        required
      />
    </div>

    <div class="flex gap-2 justify-end pt-4">
      <Button type="button" label="Отмена" severity="secondary" outlined @click="$emit('cancel')" />
      <Button type="submit" label="Войти" severity="primary" :loading="loading" />
    </div>
  </form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import { useToast } from 'primevue/usetoast'
import { authApi } from '../api/authApi'
import { useAuthStore } from '../model/authStore'

const emit = defineEmits<{
  success: [user: { id: number; username: string; token: string }]
  cancel: []
}>()

const router = useRouter()
const toast = useToast()
const authStore = useAuthStore()
const username = ref('')
const password = ref('')
const loading = ref(false)

const handleLogin = async () => {
  if (!username.value || !password.value) {
    toast.add({
      severity: 'error',
      summary: 'Ошибка',
      detail: 'Заполните все поля',
      life: 3000,
    })
    return
  }

  loading.value = true
  try {
    const response = await authApi.login({
      username: username.value,
      password: password.value,
    })

    console.log('Login response:', response)

    authStore.setAuth(response.token, response.user.username)

    toast.add({
      severity: 'success',
      summary: 'Успешно',
      detail: `Добро пожаловать, ${response.user.username}!`,
      life: 3000,
    })

    emit('success', response.user)

    router.push('/products')
  } catch (error: any) {
    console.error('Login error:', error)
    toast.add({
      severity: 'error',
      summary: 'Ошибка входа',
      detail: error.response?.data?.error || error.message || 'Неверный логин или пароль',
      life: 5000,
    })
  } finally {
    loading.value = false
  }
}
</script>
