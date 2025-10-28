import router from './index'
import { useAppStore } from '@/stores/app'
import { useCandidatesQuestionnairesStore } from '@/stores/candidates/questionnaires'
import { useSetupStore } from '@/stores/setup'

export const auth = async (to) => {
  const appStore = useAppStore()

  if (appStore.authenticated) {
    return true
  }

  router.push({ name: 'login', query: { redirect: to.fullPath } })

  return false
}

export const candidate = async (to) => {
  const candidatesQuestionnaires = useCandidatesQuestionnairesStore()

  if (candidatesQuestionnaires.availableCode) {
    return true
  }

  router.push({ name: 'home', query: { redirect: to.fullPath } })

  return false
}

export const setup = async () => {
  const setupStore = useSetupStore()

  if (!setupStore.status) {
    router.replace({ name: 'check-setup' })
    return false
  }

  return true
}
