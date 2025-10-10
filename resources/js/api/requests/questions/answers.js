import * as questionsAnswersRoutes from '@/api/routes/questions/answers'

import http from '@/http'

export async function getAllRequest(id) {
  const response = await http.get(questionsAnswersRoutes.get_all_route(id))

  return response
}

export async function getSyncRequest(id, payload) {
  const response = await http.post(questionsAnswersRoutes.get_sync_route(id), payload)

  return response
}
