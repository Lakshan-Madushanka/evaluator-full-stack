import { checkAvailability as checkAvailabilityRequest } from '@/api/requests/questionnaires/index'
import * as teamsQuestionnairesRequests from '@/api/requests/teams/questionnaires'

import { defineStore } from 'pinia'

import { useAppStore } from '../app'

import { ref, reactive } from 'vue'

export const useTeamsQuestionnairesStore = defineStore('teamsQuestionnaires', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const availableId = ref(null)
  const questionnaires = ref(null)
  const users = ref(null)
  const errors = reactive({ questionnaireId: '' })

  async function getAll(teamId, payload) {
    clearState()
    loading.value = true

    try {
      const results = await teamsQuestionnairesRequests.getAllRequest(teamId, payload)
      questionnaires.value = results
    } catch (error) {
      appStore.setToast(
        'error',
        'Error occurred while obtaining questionnaire data please try again'
      )
    } finally {
      loading.value = false
    }
  }

  async function getAllUsers(teamQuestionnaireId, payload) {
    clearState()
    loading.value = true

    try {
      const results = await teamsQuestionnairesRequests.getAllUsersRequest(
        teamQuestionnaireId,
        payload
      )
      users.value = results
    } catch (error) {
      appStore.setToast(
        'error',
        'Error occurred while obtaining questionnaire data please try again'
      )
    } finally {
      loading.value = false
    }
  }

  async function checkAvailability(id) {
    clearState()
    status.value = 'searching'

    try {
      const response = await checkAvailabilityRequest(id)
      if (response.available) {
        availableId.value = id
      } else {
        errors.questionnaireId = 'Incompleted or invalid questionnaire id'
      }
    } catch (error) {
      appStore.setToast(
        'error',
        'Error occurred while cheking availablity of the questionnaire please try again'
      )
    } finally {
      status.value = ''
    }
  }

  async function attach(teamId) {
    errors.questionnaireId = ''
    status.value = 'attaching'

    try {
      await teamsQuestionnairesRequests.attach(teamId, availableId.value)
      status.value = 'attached'
      appStore.setToast(
        'success',
        `Questionnaire with ${availableId.value} attached to team ${teamId}`
      )
    } catch (error) {
      appStore.setToast('error', 'Error occurred while attaching questionnaire please try again')
    }
  }

  async function detach(teamId, userIds) {
    clearState()
    status.value = 'detaching'

    try {
      await teamsQuestionnairesRequests.detach(teamId, userIds)

      status.value = 'detached'

      appStore.setToast(
        'success',
        'Selected questionnaire of the team ' + teamId + 'removed successfully'
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

  function clearState() {
    status.value = ''
    errors.questionnaireId = ''
    availableId.value = null
  }

  return {
    loading,
    status,
    availableId,
    questionnaires,
    users,
    errors,
    checkAvailability,
    attach,
    getAll,
    getAllUsers,
    detach,
    clearState
  }
})
