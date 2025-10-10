import { reactive, watch } from 'vue'

import { useDashboardStore } from '@/stores/dashboard'

export function useDashboard() {
  const dashboardStore = useDashboardStore()

  const categoryQuestionnairesChartData = reactive({
    labels: [],
    datasets: [
      {
        label: 'questionnaires',
        data: [],
        backgroundColor: 'rgba(255, 159, 64, 0.2)',
        borderColor: 'rgb(255, 159, 64)',
        borderWidth: 1
      }
    ]
  })

  const categoryQuestionsChartData = reactive({
    labels: [],
    datasets: [
      {
        label: 'questions',
        data: [],
        backgroundColor: 'rgba(153, 102, 255, 0.2)',
        borderColor: 'rgb(153, 102, 255)',
        borderWidth: 1
      }
    ]
  })

  watch(
    [() => dashboardStore.data.categoryQuestionnaires, () => dashboardStore.data.categoryQuestions],
    ([newcategoryQuestionnaires, newcategoryQuestions]) => {
      if (newcategoryQuestionnaires) {
        categoryQuestionnairesChartData.labels = Object.keys(newcategoryQuestionnaires)
        categoryQuestionnairesChartData.datasets[0]['data'] =
          Object.values(newcategoryQuestionnaires)
      }

      if (newcategoryQuestions) {
        categoryQuestionsChartData.labels = Object.keys(newcategoryQuestions)
        categoryQuestionsChartData.datasets[0]['data'] = Object.values(newcategoryQuestions)
      }
    }
  )

  return { categoryQuestionnairesChartData, categoryQuestionsChartData }
}
