import * as teamRequests from '@/api/requests/teams/index'

import { defineStore } from 'pinia'
import { useAppStore } from '../app'

import { ref } from 'vue'

export const useTeamsStore = defineStore('teams', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const errors = ref({})
  const teams = ref(null)
  const team = ref(null)

  async function getOne(id) {
    resetStatus(true, '', {})
    errors.value = {}

    try {
      const response = await teamRequests.getOneRequest(id)
      team.value = response
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
      const response = await teamRequests.getAllRequest(payload)
      teams.value = response
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function createTeam(payload) {
    resetStatus(false, 'creating')
    errors.value = {}

    try {
      const response = await teamRequests.createTeamRequest(payload)

      appStore.setToast('success', 'Team created successfully with id ' + response.data.id)
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

  async function editTeam(id, payload) {
    resetStatus(false, 'updating')
    errors.value = {}

    try {
      const response = await teamRequests.editTeamRequest(id, payload)

      appStore.setToast('success', 'Team with id ' + response.data.id + ' updated successfully')
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

  async function deleteTeam(id) {
    resetStatus(true, '')
    errors.value = {}

    try {
      await teamRequests.deleteTeamRequest(id)

      status.value = 'deleted'
      appStore.setToast('success', 'Team deleted successfully')
    } catch (data) {
      appStore.setToast('error', 'Error occurred while deleting team please try again')
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
    teams,
    team,
    getOne,
    getAll,
    createTeam,
    editTeam,
    deleteTeam
  }
})
