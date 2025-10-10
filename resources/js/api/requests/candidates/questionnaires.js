import * as candidateQuestionnairesRoutes from '@/api/routes/candidates/questionnaires'
import { buildQueryString } from '@/api/queryStringBuilder'

import http from '@/http'

export async function checkAvailabilityRequest(code) {
  const response = await http.get(candidateQuestionnairesRoutes.check_availability(code))

  return response
}

export async function getAllRequest(code, payload) {
  let query = buildQueryString(payload)
  const response = await http.get(candidateQuestionnairesRoutes.get_all_route(code) + query)

  return response
}

export async function evaluateRequest(code, payload) {
  const response = await http.post(candidateQuestionnairesRoutes.get_evaluate_route(code), payload)

  return response
}
