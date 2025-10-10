import * as userQuestionnairesRoutes from '@/api/routes/users/questionnaires'
import { buildQueryString } from '@/api/queryStringBuilder'

import http from '@/http'

export async function getAllRequest(userId, payload) {
  let query = buildQueryString(payload)
  const response = await http.get(userQuestionnairesRoutes.get_all_route(userId) + query)

  return response
}

export async function attach(userId, questionnaireid) {
  const response = await http.post(
    userQuestionnairesRoutes.get_attach_route(userId, questionnaireid)
  )

  return response
}

export async function resendNotificarionRequest(userId, questionnaireid) {
  const response = await http.get(
    userQuestionnairesRoutes.get_resent_notification_route(userId, questionnaireid)
  )

  return response
}

export async function detach(userId, questionnaireid) {
  const response = await http.delete(
    userQuestionnairesRoutes.get_detach_route(userId, questionnaireid)
  )

  return response
}
