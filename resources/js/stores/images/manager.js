import * as imageManagerRequests from '@/api/requests/images/manager'

import { defineStore } from 'pinia'

import { ref } from 'vue'

export const useImagesStore = defineStore('images', () => {
  const loading = ref(false)
  const status = ref('')
  const images = ref([])

  async function getImages(id, type) {
    resetStatus(true, '', {})
    images.value = []

    try {
      const response = await imageManagerRequests.getImagesRequest(id, type)
      console.log(response)
      images.value = response.data
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function chageOrderOfImages(type, ids) {
    resetStatus(true, 'changing', {})

    try {
      await imageManagerRequests.getChangeOrderOfImagesRequest(type, ids)

      //const images =
      status.value = 'changed'
    } catch (data) {
      //
      status.value = ''
    } finally {
      loading.value = false
    }
  }

  async function removeImages(type, ids) {
    resetStatus(true, 'removing', {})

    try {
      await imageManagerRequests.getRemoveImagesRequest(type, ids)

      //const images =
      status.value = 'removed'
    } catch (data) {
      //
      status.value = ''
    } finally {
      loading.value = false
    }
  }

  function resetStatus(isLoading, statusValue) {
    loading.value = isLoading
    status.value = statusValue
  }

  return {
    loading,
    status,
    images,
    getImages,
    chageOrderOfImages,
    removeImages
  }
})
