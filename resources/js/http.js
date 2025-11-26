import axios from 'axios'
import router from './router/index'
import { useAppStore } from './stores/app'
import { useAuthStore } from './stores/auth'
import Cookies from 'js-cookie'

axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true

const instance = axios.create({
  headers: {
    'X-XSRF-TOKEN': Cookies.get('XSRF-TOKEN')
  }
})

export function setInstance() {
  const appStore = useAppStore()

  instance.defaults.baseURL = appStore.info.api_v1_url
}

instance.interceptors.request.use((request) => {
  if (request.method !== 'get' && request.method !== 'post') {
    request.data['_method'] = request.method

    request.method = 'post'
  }

  return request
})

instance.interceptors.response.use(
  function (response) {
    return response.data ? response.data : response
  },
  function (error) {
    if (error.response) {
      if (parseInt(error.response.status) === 422) {
        return Promise.reject(error.response.data)
      }
      handleServerSideErrors(error)

      throw error.response
    } else {
      handleClientSideErrors(error)
      throw error
    }
  }
)

function handleClientSideErrors(errorResponse) {
  router.push({
    name: 'error',
    query: {
      status: errorResponse.code,
      message: errorResponse.message,
      type: 'client'
    }
  })
}
function handleServerSideErrors(errorResponse) {
  const appStore = useAppStore()
  const authStore = useAuthStore()

  const response = errorResponse.response

  let status = parseInt(response.status)
  let message = response.data && response.data.message ? response.data.message : ''

  // Setup message if not exists
  if (message === '') {
    switch (status) {
      case 404:
        message = 'Content you requested was not found on the server.'
        break
      case 403:
        message = 'You dont seem to have permission to access this'
        break
      default:
        message = 'Server error has occurred please try again !'
        break
    }
  }

  if (status === 401) {
    if (appStore.initialized) {
      let redirect = router.currentRoute.value.path

      if (appStore.authenticated) {
        appStore.status = 'SESSION_EXPIRED'
        appStore.authenticated = false
        authStore.resetState()

        router.push({ name: 'login', query: { redirect } })
      }
    }

    return
  }

  let query = {
    status: status,
    statusText: response.statusText,
    message: message,
    type: 'server'
  }

  if (status === 429) {
    query['retryAfter'] = errorResponse['response']['headers']['retry-after']
  }

  router.push({
    name: 'error',
    query
  })
}

export default instance
