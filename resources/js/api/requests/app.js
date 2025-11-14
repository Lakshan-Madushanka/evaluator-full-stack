import * as appRoutes from '../routes/app'

import http from '@/http'

export async function getInfo() {
  console.log(appRoutes.get_info_route)
  return await http.get(appRoutes.get_info_route)
}

export async function storeSettings(payload) {
  return await http.post(appRoutes.store_settings_route(), payload)
}
