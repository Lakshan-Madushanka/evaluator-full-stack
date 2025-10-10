import * as userRoutes from '@/api/routes/users/index'

import { buildQueryString } from '@/api/queryStringBuilder'
import http from '@/http'

export async function getAllRequest(payload) {
  let query = buildQueryString(payload)
  const response = await http.get(userRoutes.get_all_route + query)

  return response
}

export async function getOneRequest(id) {
  const response = await http.get(userRoutes.get_one_route(id))

  return response
}

export async function createUserRequest(payload) {
  const response = await http.post(userRoutes.get_create_route, payload)

  return response
}

export async function editUserRequest(id, payload) {
  if (payload.password === '') {
    delete payload.password
    delete payload.password_confirmation
  }

  const response = await http.put(userRoutes.get_update_route(id), payload)

  return response
}

export async function deleteUserRequest(id) {
  const response = await http.delete(userRoutes.get_delete_route(id))

  return response
}

export async function bulkDeleteUsersRequest(payload) {
  const response = await http.post(userRoutes.get_bulk_delete_route(), payload)

  return response
}
