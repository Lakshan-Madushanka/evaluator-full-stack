const baseURL = import.meta.env.VITE_BASE_API_URL

const base = `${baseURL}api/setup/`

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
