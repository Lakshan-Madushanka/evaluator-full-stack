import * as questionsRoutes from '@/api/routes/questions'

import { buildQueryString } from '@/api/queryStringBuilder'
import http from '@/http'

export async function getAllRequest(payload) {
  let query = buildQueryString(payload)
  const response = await http.get(questionsRoutes.get_all_route + query)

  return response
}

export async function getOneRequest(id, payload) {
  let query = buildQueryString(payload)

  const response = await http.get(questionsRoutes.get_one_route(id) + query)

  return response
}

export async function createQuestionRequest(payload) {
  const response = await http.post(questionsRoutes.get_create_route, payload)

  return response
}

export async function editQuestionRequest(id, payload) {
  const response = await http.put(questionsRoutes.get_update_route(id), payload)

  return response
}

export async function deleteQuestionRequest(id) {
  const response = await http.delete(questionsRoutes.get_delete_route(id))

  return response
}

export async function bulkDeleteQuestionsRequest(ids) {
  const response = await http.post(questionsRoutes.get_bulk_delete_route, ids)

  return response
}
