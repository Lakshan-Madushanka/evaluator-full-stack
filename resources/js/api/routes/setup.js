const baseURL = import.meta.env.VITE_BASE_API_URL

const base = `${baseURL}api/setup/`

export const check_setup_status = `${base}check-status`

/**
 * PHP Requirements
 */
export const php_version_check = `${base}check-php-version`
export const php_extensions_check = `${base}check-php-extensions`

/**
 * File Permissions
 */
export const file_permissions_check = `${base}check-file-permissions`

/**
 * ENV
 */
export const env_check = `${base}check-env`

/**
 * DB
 */
export const get_db_info = `${base}db/get-info`
export const check_db_connection = `${base}db/check-connection`
export const migrate_db = `${base}db/migrate`

/**
 * Account
 */
export const check_account_exists = `${base}account/check-exists`
export const create_account = `${base}account/create`
