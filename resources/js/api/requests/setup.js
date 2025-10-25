import * as setupRoutes from '../routes/setup'

import http from '@/http'

export async function checkStatus() {
  return await http.get(setupRoutes.check_setup_status)
}

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

/**
 * ENV Check
 */
export async function checkEnv() {
  return await http.get(setupRoutes.env_check)
}

/**
 * DB Check
 */
export async function getDBInfo() {
  return await http.get(setupRoutes.get_db_info)
}

export async function checkConnection() {
  return await http.get(setupRoutes.check_db_connection)
}

export async function migrateDB() {
  return await http.get(setupRoutes.migrate_db)
}

/**
 * Account
 */
export async function checkAccountExists() {
  return await http.get(setupRoutes.check_account_exists)
}

export async function createAccount(payload) {
  return await http.post(setupRoutes.create_account, payload)
}
