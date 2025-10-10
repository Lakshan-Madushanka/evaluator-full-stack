import * as categoryRequests from '@/api/requests/categories/index'

import { defineStore } from 'pinia'
import { useAppStore } from '../app'

import { ref } from 'vue'

export const useCategoriesStore = defineStore('categories', () => {
  const appStore = useAppStore()

  const loading = ref(false)
  const status = ref('')
  const errors = ref({})
  const categories = ref(null)
  const category = ref(null)

  async function getOne(id) {
    resetStatus(true, '', {})
    errors.value = {}

    try {
      const response = await categoryRequests.getOneRequest(id)
      category.value = response
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function getAll(payload) {
    resetStatus(true, '')
    errors.value = {}

    try {
      const response = await categoryRequests.getAllRequest(payload)
      categories.value = response
    } catch (data) {
      //
    } finally {
      loading.value = false
    }
  }

  async function createCategory(payload) {
    resetStatus(false, 'creating')
    errors.value = {}

    try {
      const response = await categoryRequests.createCategoryRequest(payload)

      appStore.setToast('success', 'Category created successfully with id ' + response.data.id)
      errors.value = {}
    } catch (data) {
      if (data.errors) {
        errors.value = data.errors
        appStore.setToast('error', data.message)
      }
    } finally {
      resetStatus(false, '')
    }
  }

  async function editCategory(id, payload) {
    resetStatus(false, 'updating')
    errors.value = {}

    try {
      const response = await categoryRequests.editCategoryRequest(id, payload)

      appStore.setToast('success', 'Category with id ' + response.data.id + ' updated successfully')
      errors.value = {}
    } catch (data) {
      if (data.errors) {
        errors.value = data.errors
        appStore.setToast('error', data.message)
      }
    } finally {
      resetStatus(false, '')
    }
  }

  async function deleteCategory(id) {
    resetStatus(true, '')
    errors.value = {}

    try {
      await categoryRequests.deleteCategoryRequest(id)

      status.value = 'deleted'
      appStore.setToast('success', 'Category deleted successfully')
    } catch (data) {
      appStore.setToast('error', 'Error occurred while deleting category please try again')
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
    errors,
    categories,
    category,
    getOne,
    getAll,
    createCategory,
    editCategory,
    deleteCategory
  }
})
