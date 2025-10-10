import * as questionnaireRoutes from '@/api/routes/questionnaires'

import { buildQueryString } from '@/api/queryStringBuilder'
import http from '@/http'

export async function getAllRequest(payload) {
  let query = buildQueryString(payload)
  const response = await http.get(questionnaireRoutes.get_all_route + query)

  return response
}

export async function getOneRequest(id, payload) {
  let query = buildQueryString(payload)

  const response = await http.get(questionnaireRoutes.get_one_route(id) + query)

  return response
}

export async function checkAvailability(id) {
  const response = await http.get(questionnaireRoutes.get_route_to_check_avalability(id))

  return response
}

export async function createQuestionnaireRequest(payload) {
  const response = await http.post(questionnaireRoutes.get_create_route, payload)

  return response
}

export async function editQuestionnaireRequest(id, payload) {
  const response = await http.put(questionnaireRoutes.get_update_route(id), payload)

  return response
}

export async function deleteQuestionnaireRequest(id) {
  const response = await http.delete(questionnaireRoutes.get_delete_route(id))

  return response
}
