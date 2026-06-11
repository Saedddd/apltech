<template>
  <div class="min-h-screen bg-gray-100">
    <Header />

    <main class="container mx-auto px-4 py-8">
      <Card class="mb-8">
        <template v-slot:content>
          <div class="space-y-6">
            <div class="flex gap-2 flex-wrap">
              <Button label="Primary" severity="primary" @click="toastAdd('primary')" />
              <div>
                <label class="block mb-2">Text Input</label>
                <InputText v-model="formData.text" placeholder="Enter text" class="w-full" />
              </div>
            </div>
          </div>
        </template>
      </Card>

      <router-view />
    </main>

    <Toast />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Header } from '@widgets/Header'
import Card from 'primevue/card'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

const toast = useToast()

const formData = ref({
  text: '',
  number: null,
  icon: '',
  disabled: 'Disabled field',
  brand: null,
})

const toastAdd = (type: string) => {
  toast.add({
    severity: type,
    summary: `${type} button`,
    detail: `You clicked ${type} button`,
    life: 3000,
  })
}
</script>
