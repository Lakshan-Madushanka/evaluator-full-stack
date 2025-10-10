import * as questionsAnswersRequests from '@/api/requests/questions/answers'
import { getRequestToCheckAnswerExists } from '@/api/requests/answers/index'

import { defineStore } from 'pinia'
import { useAppStore } from '../app'

import { ref } from 'vue'

export const useQuestionsAnswersStore = defineStore('questionsAnswers', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const errors = ref({})
  const answers = ref(null)
  const answer = ref(null)

  async function getAll(id) {
    resetStatus(true, '', {})

    try {
      const results = await questionsAnswersRequests.getAllRequest(id)
      answers.value = results.data
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function checkAnswersExists(answerId) {
    resetStatus(false, 'searching', {})

    try {
      const results = await getRequestToCheckAnswerExists(answerId)

      if (results.exists === false) {
        errors.value.answerId = 'Invalid or not eligible answer id'
        appStore.setToast('warn', "Couldn't not find eligible answer for id " + answerId)
      }
      answer.value = results.answer
    } catch (data) {
      //
    } finally {
      status.value = ''
    }
  }

  async function syncAnswers(answerId, payload) {
    resetStatus(false, 'syncing', {})

    try {
      await questionsAnswersRequests.getSyncRequest(answerId, payload)

      appStore.setToast(
        'success',
        'Questions of questionnaire ' + answerId + ' synced successfully'
      )
    } catch (error) {
      //
      if (error.message) {
        appStore.setToast('error', error.message)
      }
    } finally {
      status.value = ''
    }
  }

  function resetStatus(isLoading, statusValue, errorsValue, answerValue = null) {
    loading.value = isLoading
    status.value = statusValue
    errors.value = errorsValue
    answer.value = answerValue
  }

  return {
    loading,
    status,
    errors,
    answers,
    answer,
    getAll,
    checkAnswersExists,
    syncAnswers
  }
})
