import { getApiUrl } from '@/helpers'

export const get_info_route = window.location.origin + '/api/frontend'
export const store_settings_route = () => getApiUrl() + '/frontend'
