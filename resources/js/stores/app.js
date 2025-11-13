import { reactive, ref } from 'vue'

import { defineStore } from 'pinia'
import { useAuthStore } from './auth'

import * as appRequests from '../api/requests/app'

import { setInstance } from '@/http'

import { isDarkMode as checkIsDarkMode } from '@/helpers'

export const useAppStore = defineStore('app', () => {
  const authStore = useAuthStore()

  const info = ref({
    base_url: '',
    api_url: '',
    api_v1_url: ''
  })

  const initialized = ref(false)
  const authenticated = ref(false)
  const authLoadedStatus = ref('')
  const isDarkMode = ref(checkIsDarkMode())

  const toast = reactive({
    id: null,
    severity: '',
    summary: '',
    detail: '',
    life: ''
  })

  async function initApp() {
    try {
      await setAppInfo()
      await setAuthStatus()
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

  async function setAppInfo() {
    let data = localStorage.getItem('appInfo')

    if (data) {
      info.value = JSON.parse(data)
    } else {
      info.value = await appRequests.getInfo()
      localStorage.setItem('appInfo', JSON.stringify(info.value))
    }

    setInstance()
  }

  async function setAuthStatus() {
    await authStore.loadAuthUser()
    authenticated.value = true
    authLoadedStatus.value = ''
  }

  return {
    info,
    isDarkMode,
    authenticated,
    initialized,
    authLoadedStatus,
    toast,
    setToast,
    initApp
  }
})
