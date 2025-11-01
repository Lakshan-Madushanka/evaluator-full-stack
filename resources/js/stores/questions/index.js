import * as questionsRequests from '@/api/requests/questions/index'

import { defineStore } from 'pinia'
import { useAppStore } from '../app'

import { ref } from 'vue'

export const useQuestionsStore = defineStore('questions', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const errors = ref({})
  const questions = ref(null)
  const question = ref(null)
  const images = ref([])

  async function getOne(id, payload) {
    resetStatus(true, '', {})
    errors.value = {}

    try {
      const response = await questionsRequests.getOneRequest(id, payload)
      question.value = response
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
      const response = await questionsRequests.getAllRequest(payload)

      questions.value = response
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function createQuestion(payload) {
    resetStatus(false, 'creating')
    errors.value = {}

    try {
      const response = await questionsRequests.createQuestionRequest(payload)

      appStore.setToast('success', 'Question created successfully with id ' + response.data.id)
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

  async function editQuestion(id, payload) {
    resetStatus(false, 'updating')
    errors.value = {}

    try {
      const response = await questionsRequests.editQuestionRequest(id, payload)

      appStore.setToast('success', 'Question with id ' + response.data.id + ' updated successfully')
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

  async function deleteQuestion(id) {
    resetStatus(true, '')
    errors.value = {}

    try {
      await questionsRequests.deleteQuestionRequest(id)

      status.value = 'deleted'
      appStore.setToast('success', 'Question deleted successfully')
    } catch (data) {
      appStore.setToast('error', 'Error occurred while deleting questions please try again')
    } finally {
      loading.value = false
    }
  }

  async function bulkDeleteQuestions(payload) {
    resetStatus(true, 'deleting')
    errors.value = {}

    try {
      await questionsRequests.bulkDeleteQuestionsRequest(payload)

      status.value = 'deleted'
      appStore.setToast('success', `${payload.ids.length} no of questions deleted successfully`)
    } catch (data) {
      appStore.setToast('error', 'Error occurred while deleting questions please try again')
      status.value = ''
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
    questions,
    question,
    images,
    getOne,
    getAll,
    createQuestion,
    editQuestion,
    deleteQuestion,
    bulkDeleteQuestions
  }
})
