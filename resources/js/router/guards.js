import router from './index'
import { useAppStore } from '@/stores/app'
import { useCandidatesQuestionnairesStore } from '@/stores/candidates/questionnaires'

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
