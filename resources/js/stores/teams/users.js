import * as teamsUsersRequest from '@/api/requests/teams/users'

import { defineStore } from 'pinia'
import { useAppStore } from '../app'

import { ref } from 'vue'

export const useTeamsUsersStore = defineStore('teamsUsers', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const errors = ref({})
  const users = ref(null)

  async function getAll(teamId, payload) {
    resetStatus(true, '', {})

    try {
      const results = await teamsUsersRequest.getAllRequest(teamId, payload)
      users.value = results
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function detachUsers(teamId, userIds) {
    resetStatus(true, 'deleting', {})

    try {
      await teamsUsersRequest.getDetachRequest(teamId, userIds)

      status.value = 'deleted'

      appStore.setToast(
        'success',
        'Selected users of the team ' + teamId + ' detached successfully'
      )
    } catch (error) {
      //
      if (error.message) {
        appStore.setToast('error', error.message)
      }
    } finally {
      loading.value = false
    }
  }

  function resetStatus(isLoading, statusValue, errorsValue) {
    loading.value = isLoading
    status.value = statusValue
    errors.value = errorsValue
  }

  return {
    users,
    loading,
    status,
    errors,
    getAll,
    detachUsers
  }
})
