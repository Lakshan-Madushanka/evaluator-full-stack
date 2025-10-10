import * as authRequests from '../api/requests/auth'

import { reactive, ref } from 'vue'

import { ROLES } from '@/constants'
import { defineStore } from 'pinia'
import { useAppStore } from './app'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()

  const appStore = useAppStore()

  const status = ref('')
  const loading = ref(false)
  const errors = ref({})
  const user = reactive({ id: '', name: '', role: '', email: '' })

  async function login(payload) {
    loading.value = true
    try {
      const response = await authRequests.loginRequest(payload)
      let redirect = router.currentRoute.value.query.redirect

      setUser(response.data)
      appStore.status = ''
      appStore.authenticated = true
      errors.value = {}

      if (redirect) {
        router.replace(redirect)
      } else if (user.role !== 'REGULAR') {
        router.replace({ name: 'admin.dashboard' })
      } else {
        router.replace({ name: 'home' })
      }
    } catch (data) {
      appStore.setToast('error', data.message)
      errors.value = data.errors ? data.errors : {}
    } finally {
      loading.value = false
    }
  }

  async function update(payload) {
    const newPayload = { ...payload, role: ROLES[user.role] }
    status.value = 'updating'
    try {
      const response = await authRequests.updateProfileRequest(user.id, newPayload)
      setUser(response.data)
      errors.value = {}
      appStore.setToast('success', 'Profile updated successfully')
    } catch (data) {
      appStore.setToast('error', data.message)
      errors.value = data.errors
    } finally {
      status.value = ''
    }
  }

  async function loadAuthUser() {
    try {
      const response = await authRequests.loadAuthUserRequest()

      setUser(response.data)
    } catch (error) {
      return Promise.reject(error)
    }
  }

  async function logOut() {
    appStore.status = 'loggingOut'
    try {
      const response = await authRequests.logOut()
      appStore.authenticated = false
      resetState(response)

      router.replace({ name: 'home' })
    } catch (error) {
      //
    } finally {
      appStore.status = ''
    }
  }

  function setUser(response) {
    user.id = response.id
    user.name = response.attributes.name
    user.email = response.attributes.email
    user.role = response.attributes.role
  }

  function resetState() {
    user.id = ''
    user.name = ''
    user.email = ''
    user.role = ''
    errors.value = {}
    loading.value = false
  }
  return {
    login,
    update,
    logOut,
    loadAuthUser,
    resetState,
    status,
    loading,
    user,
    errors
  }
})
