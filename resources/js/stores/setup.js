import * as setupRequests from '@/api/requests/setup'

import { defineStore } from 'pinia'

import { useAppStore } from '@/stores/app'

import { reactive, ref } from 'vue'

export const useSetupStore = defineStore('setup', () => {
  const appStore = useAppStore()

  const status = ref('')

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
      keyStatus: '',
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
    },
    account: {
      status: { checkingExistence: false, creating: false },
      errors: {},
      exists: false,
      loading: false,
      isLoaded: false,
      is_passed: false
    },
    optimize: {
      status: '',
      isLoaded: false,
      is_passed: false
    }
  })

  /**
   * Check setup status
   */
  async function checkStatus() {
    try {
      const response = await setupRequests.checkStatus()
      status.value = response.status
    } catch (data) {
      console.error(data)
    } finally {
    }
  }

  /**
   * PHP Requirements Check
   */
  async function checkPHPVersion() {
    data.php.version.loading = true

    try {
      data.php.version = await setupRequests.checkPHPVersionRequest()
    } catch (data) {
      console.error(data)
    } finally {
      data.php.version.loading = false
    }
  }

  async function checkPHPExtensions() {
    data.php.extensions.loading = true

    try {
      const response = await setupRequests.checkPHPExtensionsRequest()
      data.php.extensions.list = response.list
      data.php.extensions.is_passed = response.is_passed
    } catch (data) {
      console.error(data)
    } finally {
      data.php.extensions.loading = false
    }
  }

  /**
   * File Permissions Check
   */
  async function checkFilePermissions() {
    data.filePermissions.loading = true

    try {
      const response = await setupRequests.checkFilePermissions()
      data.filePermissions.list = response.permissions
      data.filePermissions.is_passed = !response.hasErrors
      data.filePermissions.isLoaded = true
    } catch (data) {
      console.error(data)
    } finally {
      data.filePermissions.loading = false
    }
  }

  /**
   * ENV Check
   */
  async function checkEnv() {
    data.env.loading = true

    try {
      const response = await setupRequests.checkEnv()
      data.env.is_exists = response.is_exists
      data.env.is_passed = response.is_passed
      data.env.app = response.app
      data.env.isLoaded = true
    } catch (data) {
      console.error(data)
    } finally {
      data.env.loading = false
    }
  }

  async function generateKey() {
    data.env.keyStatus = 'generating'

    try {
      const response = await setupRequests.generateKey()
      if (response.status) {
        data.env.keyStatus = 'generated'
        status.value = 'completed'
      } else {
        data.env.keyStatus = 'error'
      }
    } catch (data) {
      data.env.keyStatus = 'error'
      console.error(data)
    } finally {
    }
  }

  /**
   * DB Check
   */
  async function getDBInfo() {
    data.db.loading = true

    try {
      data.db.info = await setupRequests.getDBInfo()
    } catch (data) {
      console.error(data)
    } finally {
      data.db.loading = false
    }
  }

  async function checkDBConnection() {
    data.db.status.checkingConnection = true

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
    data.db.status.migrating = true

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

  /**
   * Account
   */
  async function checkAccountExists() {
    data.account.status.checkingExistence = true

    try {
      const response = await setupRequests.checkAccountExists()
      if (response.exists) {
        data.account.exists = true
        data.account.is_passed = true
        data.account.isLoaded = true
      } else {
        data.account.exists = false
      }
    } catch (data) {
      console.error(data)
    } finally {
      data.account.status.checkingExistence = false
    }
  }

  async function createAccount(payload) {
    data.account.status.creating = true

    try {
      const response = await setupRequests.createAccount(payload)
      data.account.exists = true
      data.account.is_passed = true
      data.account.isLoaded = true
    } catch (errors) {
      data.account.errors = errors.errors
      appStore.setToast('error', errors.message)
    } finally {
      data.account.status.creating = false
    }
  }

  /**
   * Optimize
   */
  async function optimize() {
    data.optimize.status = 'optimizing'

    try {
      const response = await setupRequests.optimize()
      if (response.status) {
        data.optimize.status = 'optimized'
      } else {
        data.optimize.status = 'error'
      }
    } catch (data) {
      data.optimize.status = 'error'
      console.error(data)
    } finally {
    }
  }
  return {
    status,
    data,
    checkStatus,
    checkPHPVersion,
    checkPHPExtensions,
    checkFilePermissions,
    checkEnv,
    generateKey,
    getDBInfo,
    checkDBConnection,
    migrateDB,
    checkAccountExists,
    createAccount,
    optimize
  }
})
