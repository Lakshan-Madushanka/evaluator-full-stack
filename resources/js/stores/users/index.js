import * as userRequests from '@/api/requests/users/index'

import { defineStore } from 'pinia'
import { useAppStore } from '../app'

import { ref } from 'vue'

export const useUsersStore = defineStore('users', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const errors = ref({})
  const users = ref(null)
  const user = ref(null)

  async function getOne(id) {
    resetStatus(true, '', {})
    errors.value = {}

    try {
      const response = await userRequests.getOneRequest(id)
      user.value = response
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function getAll(payload) {
    resetStatus(true, '')
    errors.value = {}

    try {
      const response = await userRequests.getAllRequest(payload)
      users.value = response
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function createUser(payload) {
    resetStatus(false, 'creating')
    errors.value = {}

    try {
      const response = await userRequests.createUserRequest(payload)

      appStore.setToast('success', 'User created successfully with id ' + response.data.id)
      errors.value = {}
    } catch (data) {
      if (data.errors) {
        errors.value = data.errors
        appStore.setToast('error', data.message)
      }
    } finally {
      resetStatus(false, '')
    }
  }

  async function editUser(id, payload) {
    resetStatus(false, 'updating')
    errors.value = {}

    try {
      const response = await userRequests.editUserRequest(id, payload)

      appStore.setToast('success', 'User with id ' + response.data.id + ' updated successfully')
      errors.value = {}
    } catch (data) {
      if (data.errors) {
        errors.value = data.errors
        appStore.setToast('error', data.message)
      }
    } finally {
      resetStatus(false, '')
    }
  }

  async function deleteUser(id) {
    resetStatus(true, '')
    errors.value = {}

    try {
      await userRequests.deleteUserRequest(id)

      status.value = 'deleted'
      appStore.setToast('success', 'User deleted successfully')
    } catch (data) {
      appStore.setToast('error', 'Error occurred while deleting user please try again')
    } finally {
      loading.value = false
    }
  }

  async function bulkDeleteUsers(payload) {
    resetStatus(true, 'deleting')
    errors.value = {}

    try {
      await userRequests.bulkDeleteUsersRequest(payload)

      status.value = 'deleted'
      appStore.setToast('success', `${payload.ids.length} no of users deleted successfully`)
    } catch (data) {
      appStore.setToast('error', 'Error occurred while deleting user please try again')
    } finally {
      loading.value = false
    }
  }

  function resetStatus(isLoading, statusValue) {
    loading.value = isLoading
    status.value = statusValue
  }

  return {
    loading,
    status,
    errors,
    users,
    user,
    getOne,
    getAll,
    createUser,
    editUser,
    deleteUser,
    bulkDeleteUsers
  }
})
