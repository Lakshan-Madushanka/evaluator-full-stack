import * as setupRoutes from '../routes/setup'

import http from '@/http'

/**
 * PHP Requirements Check
 */
export async function checkPHPVersionRequest() {
  return await http.get(setupRoutes.php_version_check)
}

export async function checkPHPExtensionsRequest() {
  return await http.get(setupRoutes.php_extensions_check)
}

/**
 * File Permissions Check
 */
export async function checkFilePermissions() {
  return await http.get(setupRoutes.file_permissions_check)
}
