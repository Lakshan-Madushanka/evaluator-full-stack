import * as authRoutes from '../routes/auth'

import http from '@/http'

export async function loginRequest(payload) {
  await http.get(authRoutes.csrf_route)
  const response = await http.post(authRoutes.login_route, payload)

  return response
}

export async function logOut() {
  const response = await http.post(authRoutes.logout_route)

  return response
}

export async function loadAuthUserRequest() {
  const response = await http.get(authRoutes.authenticated_user_route)

  return response
}

export async function updateProfileRequest(userId, payload) {
  const response = await http.put(authRoutes.get_update_profile_route(userId), payload)

  return response
}
