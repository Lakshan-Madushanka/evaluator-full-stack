import * as answersRequests from '@/api/requests/answers/index'

import { defineStore } from 'pinia'
import { useAppStore } from '../app'

import { ref } from 'vue'

export const useAnswersStore = defineStore('answers', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const errors = ref({})
  const answers = ref(null)
  const answer = ref(null)
  const images = ref([])

  async function getOne(id) {
    resetStatus(true, '', {})
    errors.value = {}

    try {
      const response = await answersRequests.getOneRequest(id)
      answer.value = response
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
      const response = await answersRequests.getAllRequest(payload)

      answers.value = response
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function createAnswer(payload) {
    resetStatus(false, 'creating')
    errors.value = {}

    try {
      const response = await answersRequests.createAnswerRequest(payload)

      appStore.setToast('success', 'Answer created successfully with id ' + response.data.id)
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

  async function editAnswer(id, payload) {
    resetStatus(false, 'updating')
    errors.value = {}

    try {
      const response = await answersRequests.editAnswerRequest(id, payload)

      appStore.setToast('success', 'Answer with id ' + response.data.id + ' updated successfully')
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

  async function deleteAnswer(id) {
    resetStatus(true, '')
    errors.value = {}

    try {
      await answersRequests.deleteAnswerRequest(id)

      status.value = 'deleted'
      appStore.setToast('success', 'Answer deleted successfully')
    } catch (data) {
      appStore.setToast('error', 'Error occurred while deleting answers please try again')
    } finally {
      loading.value = false
    }
  }

  async function bulkDeleteAnswers(payload) {
    resetStatus(true, 'deleting')
    errors.value = {}

    try {
      await answersRequests.bulkDeleteAnswersRequest(payload)

      status.value = 'deleted'
      appStore.setToast('success', `${payload.ids.length} no of answers deleted successfully`)
    } catch (data) {
      appStore.setToast('error', 'Error occurred while deleting answers please try again')
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
    answers,
    answer,
    images,
    getOne,
    getAll,
    createAnswer,
    editAnswer,
    deleteAnswer,
    bulkDeleteAnswers
  }
})
