import * as evaluationRequests from '@/api/requests/evaluations/index'

import { defineStore } from 'pinia'

import { ref } from 'vue'

export const useEvaluationsStore = defineStore('evaluations', () => {
  const loading = ref(false)
  const status = ref('')
  const errors = ref({})
  const evaluations = ref(null)
  const evaluation = ref()

  async function getOne(id) {
    resetStatus(true, '')
    errors.value = {}

    try {
      const response = await evaluationRequests.getOneRequest(id)

      evaluation.value = response.data.attributes
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
      const response = await evaluationRequests.getAllRequest(payload)
      evaluations.value = response
    } catch (data) {
      //
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
    evaluations,
    evaluation,
    getAll,
    getOne
  }
})
