import { checkAvailability as checkAvailabilityRequest } from '@/api/requests/questionnaires/index'
import * as usersQuestionnairesRequests from '@/api/requests/users/questionnaires'

import { defineStore } from 'pinia'

import { useAppStore } from '../app'

import { ref, reactive } from 'vue'

export const useUsersQuestionnairesStore = defineStore('usersQuestionnaires', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const availableId = ref(null)
  const questionnaires = ref(null)
  const errors = reactive({ questionnaireId: '' })

  async function getAll(userId, payload) {
    clearState()
    loading.value = true

    try {
      const results = await usersQuestionnairesRequests.getAllRequest(userId, payload)
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

  async function attach(userId) {
    errors.questionnaireId = ''
    status.value = 'attaching'

    try {
      await usersQuestionnairesRequests.attach(userId, availableId.value)
      status.value = 'attached'
      appStore.setToast(
        'success',
        `Questionnaire with ${availableId.value} attached to user ${userId}`
      )
    } catch (error) {
      appStore.setToast('error', 'Error occurred while attaching questionnaire please try again')
    }
  }

  async function resendNotificatiion(userId, questionnaireId) {
    errors.questionnaireId = ''
    loading.value = true

    try {
      await usersQuestionnairesRequests.resendNotificarionRequest(userId, questionnaireId)
      appStore.setToast('success', `Questionnaire attached notification resent to user ${userId}`)
    } catch (error) {
      appStore.setToast('error', 'Error occurred while attaching questionnaire please try again')
    } finally {
      loading.value = false
    }
  }

  async function revokeAccess(userId, userQuestionnaireId) {
    errors.questionnaireId = ''
    loading.value = true

    try {
      await usersQuestionnairesRequests.detach(userId, userQuestionnaireId)
      appStore.setToast('success', `Questionnaire revoked from the user ${userId}`)
    } catch (error) {
      appStore.setToast('error', 'Error occurred while revoking questionnaire please try again')
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
    errors,
    checkAvailability,
    attach,
    getAll,
    resendNotificatiion,
    revokeAccess,
    clearState
  }
})
