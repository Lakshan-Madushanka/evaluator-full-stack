import * as teamsRoutes from '@/api/routes/users/teams'

import http from '@/http'
import { buildQueryString } from '@/api/queryStringBuilder'

export async function getAttachTeamsRequest(userId, teamIds) {
  const response = await http.post(teamsRoutes.get_bulk_attach_route(userId), {
    teamIds: teamIds
  })

  return response
}

export async function getAllRequest(userId, payload) {
  let query = buildQueryString(payload)

  const response = await http.get(teamsRoutes.get_all_route(userId) + query)

  return response
}
