import * as questionnaireRequests from '@/api/requests/questionnaires/index'

import { defineStore } from 'pinia'
import { useAppStore } from '../app'

import { ref } from 'vue'

export const useQuestionnairesStore = defineStore('questionnaires', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const errors = ref({})
  const questionnaires = ref(null)
  const questionnaire = ref(null)

  async function getOne(id, payload) {
    resetStatus(true, '', {})
    errors.value = {}

    try {
      const response = await questionnaireRequests.getOneRequest(id, payload)
      questionnaire.value = response
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
      const response = await questionnaireRequests.getAllRequest(payload)
      questionnaires.value = response
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function createQuestionnaire(payload) {
    resetStatus(false, 'creating')
    errors.value = {}

    try {
      const response = await questionnaireRequests.createQuestionnaireRequest(payload)

      appStore.setToast('success', 'Questionnaire created successfully with id ' + response.data.id)
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

  async function editQuestionnaire(id, payload) {
    resetStatus(false, 'updating')
    errors.value = {}

    try {
      const response = await questionnaireRequests.editQuestionnaireRequest(id, payload)

      appStore.setToast(
        'success',
        'Questionnaire with id ' + response.data.id + ' updated successfully'
      )
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

  async function deleteQuestionnaire(id) {
    resetStatus(true, '')
    errors.value = {}

    try {
      await questionnaireRequests.deleteQuestionnaireRequest(id)

      status.value = 'deleted'
      appStore.setToast('success', 'Questionnaire deleted successfully')
    } catch (data) {
      appStore.setToast('error', 'Error occurred while deleting questionnaire please try again')
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
    questionnaires,
    questionnaire,
    getOne,
    getAll,
    createQuestionnaire,
    editQuestionnaire,
    deleteQuestionnaire
  }
})
