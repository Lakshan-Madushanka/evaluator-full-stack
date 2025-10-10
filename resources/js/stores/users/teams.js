import * as teamsUsersRequest from '@/api/requests/users/teams'

import { defineStore } from 'pinia'
import { useAppStore } from '../app'

import { ref } from 'vue'

export const useUsersTeamsStore = defineStore('usersTeams', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const errors = ref({})
  const teams = ref(null)

  async function getAll(userId, payload) {
    resetStatus(true, '')
    errors.value = {}

    try {
      const response = await teamsUsersRequest.getAllRequest(userId, payload)
      teams.value = response
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function attachTeams(userId, teamIds) {
    resetStatus(true, 'attaching', {})

    try {
      await teamsUsersRequest.getAttachTeamsRequest(userId, teamIds)

      status.value = 'attached'

      appStore.setToast('success', 'Selected team(s) were attached to the users')
    } catch (error) {
      //
      if (error.message) {
        appStore.setToast('error', error.message)
      }
    } finally {
      loading.value = false
    }
  }

  function resetStatus(isLoading, statusValue, errorsValue, answerValue = null) {
    loading.value = isLoading
    status.value = statusValue
    errors.value = errorsValue
    teams.value = answerValue
  }

  return {
    teams,
    loading,
    status,
    errors,
    getAll,
    attachTeams
  }
})
