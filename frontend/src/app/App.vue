<template>
  <div class="min-h-screen bg-gray-100">
    <Header />

    <main class="container mx-auto px-4 py-8">
      <router-view />
    </main>

    <Toast position="bottom-right" />
    <ConfirmDialog />
  </div>
</template>

<script setup lang="ts">
import Header from '@widgets/Header/ui/Header.vue'
import Toast from 'primevue/toast'
import ConfirmDialog from 'primevue/confirmdialog'
</script>
