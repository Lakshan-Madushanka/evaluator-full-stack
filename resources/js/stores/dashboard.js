import { reactive, ref } from 'vue'

import { defineStore } from 'pinia'

import * as dashBoardRequests from '@/api/requests/dashboard'

export const useDashboardStore = defineStore('dashboard', () => {
  const loading = ref(false)
  const data = reactive({
    usersCount: { superAdmins: null, admins: null, regular: null, total: null },
    noOfCategories: null,
    noOfQuestionnaires: null,
    noOfQuestions: null,
    categoryQuestionnaires: null,
    categoryQuestions: null,
    latestEvaluations: null,
    evaluations: null,
    questionnaires: {}
  })

  async function getMainDashnboardData() {
    loading.value = true
    try {
      const response = await dashBoardRequests.getMainDashboardData()
      const responseData = response.data.attributes

      data.usersCount.superAdmins = responseData.users_count.super_admins
      data.usersCount.admins = responseData.users_count.admins
      data.usersCount.regular = responseData.users_count.regular_users
      data.usersCount.total = responseData.users_count.total_users

      data.noOfCategories = responseData.no_of_categories
      data.noOfQuestionnaires = responseData.no_of_questionnaires
      data.noOfQuestions = responseData.no_of_questions

      data.categoryQuestionnaires = responseData.category_questionnaires
      data.categoryQuestions = responseData.category_questions

      data.evaluations = responseData.evaluations
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function getQuestionnairesData() {
    loading.value = true
    try {
      const response = await dashBoardRequests.getQuestionnairesDataRequest()
      data.questionnaires = response
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  return { loading, data, getMainDashnboardData, getQuestionnairesData }
})
