import * as setupRequests from '@/api/requests/setup'

import { defineStore } from 'pinia'

import { reactive, ref } from 'vue'

export const useSetupStore = defineStore('setup', () => {
  const loading = ref(false)
  const status = ref('')

  const errors = ref({})

  const data = reactive({
    php: {
      version: { current: '', minimum: '', supported: false, loading: false },
      extensions: { list: {}, is_passed: false, loading: false },
      isLoaded: false
    },
    filePermissions: {
      list: {},
      loading: false,
      isLoaded: false,
      is_passed: false
    },
    env: {
      app: {},
      is_exists: false,
      loading: false,
      isLoaded: false,
      is_passed: false
    },
    db: {
      info: { name: '', connection: '', config: {} },
      connection: { status: '', has_database_created: '', errors: '' },
      status: { checkingConnection: false, migrating: false },
      loadedStatus: { checkConnection: false },
      is_exists: false,
      loading: false,
      isLoaded: false,
      is_passed: false
    }
  })

  /**
   * PHP Requirements Check
   */
  async function checkPHPVersion() {
    resetStatus(true, '', {})
    data.php.version.loading = true
    errors.value = {}

    try {
      data.php.version = await setupRequests.checkPHPVersionRequest()
    } catch (data) {
      console.error(data)
    } finally {
      loading.value = false
      data.php.version.loading = false
    }
  }

  async function checkPHPExtensions() {
    resetStatus(true, '', {})
    data.php.extensions.loading = true
    errors.value = {}

    try {
      const response = await setupRequests.checkPHPExtensionsRequest()
      data.php.extensions.list = response.list
      data.php.extensions.is_passed = response.is_passed
    } catch (data) {
      console.error(data)
    } finally {
      loading.value = false
      data.php.extensions.loading = false
    }
  }

  /**
   * File Permissions Check
   */
  async function checkFilePermissions() {
    resetStatus(true, '', {})
    data.filePermissions.loading = true
    errors.value = {}

    try {
      const response = await setupRequests.checkFilePermissions()
      data.filePermissions.list = response.permissions
      data.filePermissions.is_passed = !response.hasErrors
      data.filePermissions.isLoaded = true
    } catch (data) {
      console.error(data)
    } finally {
      loading.value = false
      data.filePermissions.loading = false
    }
  }

  /**
   * ENV Check
   */
  async function checkEnv() {
    resetStatus(true, '', {})
    data.env.loading = true
    errors.value = {}

    try {
      const response = await setupRequests.checkEnv()
      data.env.is_exists = response.is_exists
      data.env.is_passed = response.is_passed
      data.env.app = response.app
      data.env.isLoaded = true
    } catch (data) {
      console.error(data)
    } finally {
      loading.value = false
      data.env.loading = false
    }
  }

  /**
   * DB Check
   */
  async function getDBInfo() {
    resetStatus(true, '', {})
    data.db.loading = true
    errors.value = {}

    try {
      data.db.info = await setupRequests.getDBInfo()
    } catch (data) {
      console.error(data)
    } finally {
      loading.value = false
      data.db.loading = false
    }
  }

  async function checkDBConnection() {
    resetStatus(true, '', {})
    data.db.status.checkingConnection = true
    errors.value = {}

    try {
      data.db.connection = await setupRequests.checkConnection()
      data.db.loadedStatus.checkConnection = true
    } catch (data) {
      console.error(data)
    } finally {
      data.db.status.checkingConnection = false
    }
  }

  async function migrateDB() {
    resetStatus(true, '', {})
    data.db.status.migrating = true
    errors.value = {}

    try {
      const response = await setupRequests.migrateDB()
      if (response.migrated_status === 0) {
        data.db.is_passed = true
        data.db.isLoaded = true
      } else {
        data.db.is_passed = false
        data.db.isLoaded = true
      }
    } catch (data) {
      console.error(data)
    } finally {
      data.db.status.migrating = false
    }
  }

  function resetStatus(isLoading, statusValue) {
    loading.value = isLoading
    status.value = statusValue
  }

  return {
    loading,
    status,
    errors,
    data,
    checkPHPVersion,
    checkPHPExtensions,
    checkFilePermissions,
    checkEnv,
    getDBInfo,
    checkDBConnection,
    migrateDB
  }
})
