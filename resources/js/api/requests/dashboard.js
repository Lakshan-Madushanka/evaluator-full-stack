import * as dashboardRoutes from '@/api/routes/dashboard'

import http from '@/http'

export async function getQuestionnairesDataRequest() {
  const response = await http.get(dashboardRoutes.questionnaires_data_route)

  return response
}

export async function getMainDashboardData() {
  const response = await http.get(dashboardRoutes.main_data_route)

  return response
}
