// Prime Vue styles
import 'nprogress/nprogress.css'
import 'primeicons/primeicons.css' //icons
import colorSchemes from './themes/colorSchemes'

// Main styles
import './assets/main.css'

// Vue
import App from './App.vue'
import { createApp } from 'vue'

//Router
import router from './router'

// Pinia
import { createPinia } from 'pinia'

// Prime vue
import { definePreset } from '@primeuix/themes'
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import Tooltip from 'primevue/tooltip'
import ConfirmationService from 'primevue/confirmationservice'

// Custom directives
import copyToClipboard from './directives/copyToClipboard'
import { getTheme } from '@/helpers'

const preset = definePreset(
  getTheme(),
  colorSchemes[import.meta.env.VITE_COLOR_SCHEME] ?? colorSchemes['purple']
)

const app = createApp(App)

app.directive('tooltip', Tooltip)
app.directive('copy-to-clipboard', copyToClipboard)

app.use(createPinia())
app.use(router)
app.use(PrimeVue, {
  theme: {
    preset: preset,
    options: {
      darkModeSelector: '.dark'
    }
  },
  ripple: true,
  inputStyle: 'filled'
})
app.use(ToastService)
app.use(ConfirmationService)
app.mount('#app')
