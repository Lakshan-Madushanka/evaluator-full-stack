import * as categoryRoutes from '@/api/routes/categories'

import { buildQueryString } from '@/api/queryStringBuilder'
import http from '@/http'

export async function getAllRequest(payload) {
  let query = buildQueryString(payload)
  const response = await http.get(categoryRoutes.get_all_route + query)

  return response
}

export async function getOneRequest(id) {
  const response = await http.get(categoryRoutes.get_one_route(id))

  return response
}

export async function createCategoryRequest(payload) {
  const response = await http.post(categoryRoutes.get_create_route, payload)

  return response
}

export async function editCategoryRequest(id, payload) {
  const response = await http.put(categoryRoutes.get_update_route(id), payload)

  return response
}

export async function deleteCategoryRequest(id) {
  const response = await http.delete(categoryRoutes.get_delete_route(id))

  return response
}
