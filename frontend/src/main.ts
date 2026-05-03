import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from '@/router'
import { setupApiInterceptors } from '@/shared/api/http'
import '@/style.css'
import 'vue-sonner/style.css'

const app = createApp(App)
const pinia = createPinia()

setupApiInterceptors(pinia)

app.use(pinia)
app.use(router)
app.mount('#app')
