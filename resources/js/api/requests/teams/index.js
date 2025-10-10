import * as teamsRoutes from '@/api/routes/teams'

import { buildQueryString } from '@/api/queryStringBuilder'
import http from '@/http'

export async function getAllRequest(payload) {
  let query = buildQueryString(payload)
  const response = await http.get(teamsRoutes.get_all_route + query)

  return response
}

export async function getOneRequest(id) {
  const response = await http.get(teamsRoutes.get_one_route(id))

  return response
}

export async function createTeamRequest(payload) {
  const response = await http.post(teamsRoutes.get_create_route, payload)

  return response
}

export async function editTeamRequest(id, payload) {
  const response = await http.put(teamsRoutes.get_update_route(id), payload)

  return response
}

export async function deleteTeamRequest(id) {
  const response = await http.delete(teamsRoutes.get_delete_route(id))

  return response
}
