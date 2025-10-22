import * as setupRoutes from '../routes/setup'

import http from '@/http'

export async function checkPHPVersionRequest() {
  return await http.get(setupRoutes.php_version_check)
}

export async function checkPHPExtensionsRequest() {
  return await http.get(setupRoutes.php_extensions_check)
}
