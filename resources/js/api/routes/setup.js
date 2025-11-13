import { getApiUrl } from '@/helpers'

export const check_setup_status = () => `${getSetupBaseUrl()}/check-status`

/**
 * PHP Requirements
 */
export const php_version_check = () => `${getSetupBaseUrl()}/check-php-version`
export const php_extensions_check = () => `${getSetupBaseUrl()}/check-php-extensions`

/**
 * File Permissions
 */
export const file_permissions_check = () => `${getSetupBaseUrl()}/check-file-permissions`

/**
 * ENV
 */
export const env_check = () => `${getSetupBaseUrl()}/check-env`
export const generate_key = () => `${getSetupBaseUrl()}/generate-key`

/**
 * DB
 */
export const get_db_info = () => `${getSetupBaseUrl()}/db/get-info`
export const check_db_connection = () => `${getSetupBaseUrl()}/db/check-connection`
export const migrate_db = () => `${getSetupBaseUrl()}/db/migrate`

/**
 * Account
 */
export const check_account_exists = () => `${getSetupBaseUrl()}/account/check-exists`
export const create_account = () => `${getSetupBaseUrl()}/account/create`

/**
 * Optimize
 */
export const optimize_route = () => `${getSetupBaseUrl()}/optimize`

/**
 * Symlink
 */
export const create_symlink_route = () => `${getSetupBaseUrl()}/create-symlink`

function getSetupBaseUrl() {
  return getApiUrl() + '/setup'
}
