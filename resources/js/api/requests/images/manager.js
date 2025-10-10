import * as imageManagerRoutes from '@/api/routes/images/manager'
import http from '@/http'

export async function getImagesRequest(id, type) {
  const response = await http.get(imageManagerRoutes.get_route_to_load_images(id, type))

  return response
}

export async function getChangeOrderOfImagesRequest(type, ids) {
  const response = await http.post(
    imageManagerRoutes.get_route_to_change_order_of_images(type),
    ids
  )

  return response
}

export async function getRemoveImagesRequest(type, ids) {
  const response = await http.post(imageManagerRoutes.get_route_to_remove_images(type), ids)

  return response
}
