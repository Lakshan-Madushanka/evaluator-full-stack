import { reactive, ref } from 'vue'

import { defineStore } from 'pinia'
import { useAuthStore } from './auth'

import { isDarkMode as checkIsDarkMode } from '@/helpers'

export const useAppStore = defineStore('app', () => {
  const authStore = useAuthStore()

  const initialized = ref(false)
  const authenticated = ref(false)
  const authLoadedStatus = ref('')
  const isDarkMode = ref(checkIsDarkMode() ? true : false)

  const toast = reactive({
    id: null,
    severity: '',
    summary: '',
    detail: '',
    life: ''
  })

  async function initApp() {
    try {
      await authStore.loadAuthUser()
      authenticated.value = true
      authLoadedStatus.value = ''
    } catch (error) {
      if (!error.status || error.status !== 401) {
        authLoadedStatus.value = 'error'
      }
    } finally {
      initialized.value = true
    }
  }

  function setToast(severity, summary, detail, life) {
    toast.id = Date.now()
    toast.severity = severity
    toast.summary = summary
    toast.detail = detail
    toast.life = life
  }

  return {
    isDarkMode,
    authenticated,
    initialized,
    authLoadedStatus,
    toast,
    setToast,
    initApp
  }
})
