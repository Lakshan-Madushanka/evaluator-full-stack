const baseURL = import.meta.env.VITE_BASE_API_URL

const base = `${baseURL}api/setup/`

export const php_version_check = `${base}check-php-version`
export const php_extensions_check = `${base}check-php-extensions`
