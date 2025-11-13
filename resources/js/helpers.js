import moment from 'moment'
import { usePrimeVue } from 'primevue/config'

import Aura from '@primeuix/themes/aura'
import Lara from '@primeuix/themes/lara'
import Nora from '@primeuix/themes/nora'
import Material from '@primeuix/themes/material'

import { useAppStore } from '@/stores/app'

export function getBaseUrl() {
  const appStore = useAppStore()
  return appStore.info.base_url
}

export function getApiUrl() {
  const appStore = useAppStore()
  return appStore.info.api_url
}

export function getApiV1Url() {
  const appStore = useAppStore()
  return appStore.info.api_v1_url
}

export function uppercaseFirstLetter(str) {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

export function lowercaseFirstLetter(str) {
  return str.charAt(0).toLowerCase() + str.slice(1)
}

export function snake(str, symbol = '_') {
  return str.replace(/\s+/g, symbol)
}

export function formatText(str, limit = 10) {
  if (str.length <= limit) {
    return str
  }
  return str.slice(0, limit) + '...'
}

// Get related relationship from api response
export function findRelations(relationships, id, type) {
  for (const relationship in relationships) {
    if (type === relationships[relationship].type && id === relationships[relationship].id) {
      return relationships[relationship]
    }
  }
}

export function getCookie(name) {
  const cookies = document.cookie.split(';')

  for (let i = 0; i < cookies.length; i++) {
    const cookie = cookies[i].trim()

    if (cookie.startsWith(`${name}=`)) {
      // Return the decoded cookie value
      return decodeURIComponent(cookie.substring(name.length + 1))
    }
  }

  return null
}

export function formatMinutes(minutes, withoutSeconds = false) {
  var hours = Math.floor(minutes / 60)
  var mins = Math.floor(minutes % 60)
  var secs = Math.floor((minutes - hours * 60 - mins) * 60)

  if (hours < 10) {
    hours = '0' + hours
  }

  if (mins < 10) {
    mins = '0' + mins
  }

  if (secs < 10) {
    secs = '0' + secs
  }

  if (withoutSeconds) {
    return hours + ' : ' + mins
  }

  return hours + ' : ' + mins + ' : ' + secs
}

export function formatDuration(duration, format) {
  const allocatedTime = moment.duration(duration, format)

  let formattedTime = ''

  if (allocatedTime.hours() && allocatedTime.minutes()) {
    formattedTime = `${allocatedTime.hours()} (hours) : ${allocatedTime.minutes()} (minutes)`
  }

  if (allocatedTime.hours() && allocatedTime.minutes()) {
    formattedTime = `${allocatedTime.hours()} (hours) : ${allocatedTime.minutes()} (minutes)`
  }

  if (!allocatedTime.hours() && allocatedTime.minutes()) {
    formattedTime = `${allocatedTime.minutes()} (minutes)`
  }

  if (allocatedTime.hours() && !allocatedTime.minutes()) {
    formattedTime = `${allocatedTime.hours()} (hours)`
  }

  return formattedTime
}

export function arraysHaveSameValues(a, b) {
  if (a.length !== b.length) return false

  const sortedA = [...a].sort()
  const sortedB = [...b].sort()

  return sortedA.every((val, i) => val === sortedB[i])
}

export function isDarkMode() {
  const saved = localStorage.getItem('theme')
  if (saved === 'dark') {
    return true
  } else if (saved === 'light') {
    return false
  } else {
    // Check system preference
    return window.matchMedia('(prefers-color-scheme: dark)').matches
  }
}
export function formatFileSize(bytes) {
  const $primevue = usePrimeVue()

  const k = 1024
  const dm = 3
  const sizes = $primevue.config.locale.fileSizeTypes

  if (bytes === 0) {
    return `0 ${sizes[0]}`
  }

  const i = Math.floor(Math.log(bytes) / Math.log(k))
  const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm))

  return `${formattedSize} ${sizes[i]}`
}

export function getTheme() {
  const chosenTheme = import.meta.env.VITE_PRESET

  switch (chosenTheme) {
    case 'aura':
      return Aura
    case 'lara':
      return Lara
    case 'nova':
      return Nora
    case 'material':
      return Material
    default:
      return Aura
  }
}
