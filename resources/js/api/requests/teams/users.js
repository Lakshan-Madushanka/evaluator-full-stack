import * as usersRoutes from '@/api/routes/teams/users'

import { buildQueryString } from '@/api/queryStringBuilder'
import http from '@/http'

export async function getAllRequest(teamId, payload) {
  let query = buildQueryString(payload)
  const response = await http.get(usersRoutes.get_all_route(teamId) + query)

  return response
}

export async function getDetachRequest(teamId, userIds) {
  const response = await http.post(usersRoutes.get_detach_route(teamId), {
    userIds: userIds
  })

  return response
}
