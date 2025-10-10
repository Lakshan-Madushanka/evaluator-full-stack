import * as teamQuestionnairesRoutes from '@/api/routes/teams/questionnaires'
import { buildQueryString } from '@/api/queryStringBuilder'

import http from '@/http'

export async function getAllRequest(userId, payload) {
  let query = buildQueryString(payload)
  const response = await http.get(teamQuestionnairesRoutes.get_all_route(userId) + query)

  return response
}

export async function getAllUsersRequest(teamQuestionnaireId, payload) {
  let query = buildQueryString(payload)

  const response = await http.get(
    teamQuestionnairesRoutes.get_all_users_route(teamQuestionnaireId) + query
  )

  return response
}

export async function attach(teamId, questionnaireid) {
  const response = await http.post(
    teamQuestionnairesRoutes.get_attach_route(teamId, questionnaireid)
  )

  return response
}

export async function detach(teamId, questionnaireId) {
  const response = await http.delete(
    teamQuestionnairesRoutes.get_detach_route(teamId, questionnaireId)
  )

  return response
}
