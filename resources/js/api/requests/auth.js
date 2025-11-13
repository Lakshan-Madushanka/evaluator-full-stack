import * as authRoutes from '../routes/auth'

import http from '@/http'

export async function loginRequest(payload) {
  await http.get(authRoutes.csrf_route())

  return await http.post(authRoutes.login_route, payload)
}

export async function logOut() {
  return await http.post(authRoutes.logout_route)
}

export async function loadAuthUserRequest() {
  return await http.get(authRoutes.authenticated_user_route)
}

export async function updateProfileRequest(userId, payload) {
  return await http.put(authRoutes.get_update_profile_route(userId), payload)
}
