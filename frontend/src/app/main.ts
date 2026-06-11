import { createApp } from 'vue'
import { createPinia } from 'pinia'
import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/config'
import ToastService from 'primevue/toastservice'

import './style.css'

import 'primevue/resources/themes/lara-light-blue/theme.css'
import 'primevue/resources/primevue.css'
import 'primeicons/primeicons.css'

import App from './App.vue'
// import router from './providers/router.provider'

const app = createApp(App)

app.use(createPinia())
// app.use(router)
app.use(PrimeVue, { ripple: true })
app.use(ToastService)
app.use(ConfirmationService)

app.mount('#app')
