import * as appRoutes from '../routes/app'

import http from '@/http'

export async function getInfo() {
  return await http.get(appRoutes.get_info_route)
}
