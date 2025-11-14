import { reactive, ref } from 'vue'

import { defineStore } from 'pinia'
import { useAuthStore } from './auth'

import * as appRequests from '../api/requests/app'

import { setInstance } from '@/http'

import { isDarkMode as checkIsDarkMode, setTheme } from '@/helpers'

export const useAppStore = defineStore('app', () => {
  const authStore = useAuthStore()

  const isLoading = ref(false)
  const status = ref('')

  const errors = ref({})

  const info = ref({
    base_url: '',
    api_url: '',
    api_v1_url: '',
    preset: '',
    color_scheme: ''
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

  async function storeSettings(payload) {
    status.value = 'saving'
    errors.value = {}

    try {
      const response = await appRequests.storeSettings(payload)
      await setAppInfo(true)
      setToast('success', 'Settings stored successfully')
    } catch (data) {
      if (data.errors) {
        errors.value = data.errors
        setToast('error', data.message)
      }
    } finally {
      status.value = ''
    }
  }

  async function setAppInfo(force = false) {
    isLoading.value = true

    try {
      let data = localStorage.getItem('appInfo')

      if (data && !force) {
        info.value = JSON.parse(data)
      } else {
        info.value = await appRequests.getInfo()
        localStorage.setItem('appInfo', JSON.stringify(info.value))
      }

      setTheme()
      setInstance()
    } finally {
      isLoading.value = false
    }
  }

  async function setAuthStatus() {
    await authStore.loadAuthUser()
    authenticated.value = true
    authLoadedStatus.value = ''
  }

  return {
    isLoading,
    info,
    isDarkMode,
    authenticated,
    initialized,
    authLoadedStatus,
    status,
    errors,
    toast,
    setToast,
    initApp,
    storeSettings,
    setAppInfo
  }
})
