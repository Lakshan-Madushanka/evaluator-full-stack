import * as candidatesQuestionnairesRequests from '@/api/requests/candidates/questionnaires'

import { defineStore } from 'pinia'

import { useAppStore } from '../app'

import { ref, reactive } from 'vue'

export const useCandidatesQuestionnairesStore = defineStore('candidatesQuestionnaires', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const availableCode = ref('')
  const questions = ref()
  const questionnaireInfo = ref()
  const evaluation = ref()
  const meta = reactive({ included: null })
  const errors = reactive({ code: '' })

  async function getAll(payload) {
    clearState()
    loading.value = true

    try {
      const results = await candidatesQuestionnairesRequests.getAllRequest(
        availableCode.value,
        payload
      )
      questions.value = results.data
      meta.included = results.included
    } catch (error) {
      appStore.setToast(
        'error',
        'Error occurred while obtaining questionnaire data please try again'
      )
    } finally {
      loading.value = false
    }
  }
  async function checkAvalability(code) {
    clearState()
    status.value = 'searching'

    try {
      const response = await candidatesQuestionnairesRequests.checkAvailabilityRequest(code)
      questionnaireInfo.value = response

      if (response.name) {
        availableCode.value = code
      } else {
        let errorMsg = "We couldn't found a questionnaire associated with this code !"
        errors.code = errorMsg
        appStore.setToast('warn', errorMsg)
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

  async function evaluate(payload) {
    status.value = 'evaluating'

    try {
      const response = await candidatesQuestionnairesRequests.evaluateRequest(
        availableCode.value,
        payload
      )

      status.value = 'evaluated'

      evaluation.value = response.data
    } catch (error) {
      appStore.setToast('error', 'Error occurred while evaluatng questionnaire please try again')
    }
  }

  function clearState() {
    status.value = ''
    errors.code = ''
  }

  return {
    loading,
    status,
    availableCode,
    questions,
    questionnaireInfo,
    evaluation,
    meta,
    errors,
    getAll,
    checkAvalability,
    evaluate,
    clearState
  }
})
