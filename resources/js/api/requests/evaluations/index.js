import * as evaluationRoutes from '@/api/routes/evaluations/index'

import { buildQueryString } from '@/api/queryStringBuilder'
import http from '@/http'

export async function getAllRequest(payload) {
  let query = buildQueryString(payload)
  const response = await http.get(evaluationRoutes.get_all_route + query)

  return response
}

export async function getOneRequest(id) {
  const response = await http.get(evaluationRoutes.get_one_route(id))

  return response
}
