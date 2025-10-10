import * as answersRoutes from '@/api/routes/answers'
import { sanitizeParam } from '@/api/helpers'

import { buildQueryString } from '@/api/queryStringBuilder'
import http from '@/http'

export async function getAllRequest(payload) {
  let query = buildQueryString(payload)
  const response = await http.get(answersRoutes.get_all_route + query)

  return response
}

export async function getOneRequest(id, payload) {
  let query = buildQueryString(payload)

  const response = await http.get(answersRoutes.get_one_route(id) + query)

  return response
}

export async function getRequestToCheckAnswerExists(id) {
  id = sanitizeParam(id)

  const response = await http.get(answersRoutes.get_route_to_check_answer_exists(id))

  return response
}

export async function createAnswerRequest(payload) {
  const response = await http.post(answersRoutes.get_create_route, payload)

  return response
}

export async function editAnswerRequest(id, payload) {
  const response = await http.put(answersRoutes.get_update_route(id), payload)

  return response
}

export async function deleteAnswerRequest(id) {
  const response = await http.delete(answersRoutes.get_delete_route(id))

  return response
}

export async function bulkDeleteAnswersRequest(ids) {
  const response = await http.post(answersRoutes.get_bulk_delete_route, ids)

  return response
}
