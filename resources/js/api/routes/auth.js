const baseURL = import.meta.env.VITE_BASE_API_URL

export const csrf_route = `${baseURL}sanctum/csrf-cookie`
export const login_route = `administrative/login`
export const logout_route = `administrative/logout`
export const authenticated_user_route = `administrative/user`
export const get_update_profile_route = (userId) => `administrative/profile/${userId}`
