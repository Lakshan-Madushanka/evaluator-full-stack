<template>
  <main class="h-[calc(100vh-3.7rem)]">
    <div class="h-full flex flex-col lg:flex-row justify-center items-center w-full">
      <div class="w-2/3 lg:w-2/5 relative">
        <InputText
          v-model="secretCode"
          class="w-full !rounded !border-2 !border-purple-700 outline outline-purple-700 outline-2 outline-offset-2 focus:!shadow-none focus:!outline focus:!outline-purple-700 focus:!outline-4 focus:!outline-offset-3"
          placeholder="Enter your questionnaire code"
          @keyup.enter="searchQuestionnaire"
        />
        <p
          v-if="
            searchButtonClicked && secretCode === '' && !candidatesQuestionnairesStore.errors.code
          "
          class="text-red-600 mt-2 text-center absolute w-full"
        >
          Questionnaire code is required !
        </p>

        <p
          v-if="candidatesQuestionnairesStore.errors.code"
          class="text-red-600 mt-2 text-center lg:absolute w-full"
        >
          {{ candidatesQuestionnairesStore.errors.code }}
        </p>
      </div>

      <div class="flex lg:hidden self-stretch w-full justify-center">
        <PrimeButton
          type="button"
          :label="candidatesQuestionnairesStore.status === 'searching' ? 'Searching' : 'Search'"
          icon-pos="right"
          class="!ml-[-2px] self-center !mt-2 lg:!mt-0 w-2/3"
          :loading="candidatesQuestionnairesStore.status === 'searching'"
          @click="searchQuestionnaire"
        />
      </div>

      <div class="hidden lg:inline-block">
        <PrimeButton
          type="button"
          :label="candidatesQuestionnairesStore.status === 'searching' ? 'Searching' : 'Search'"
          icon="pi pi-search"
          icon-pos="right"
          class="!ml-[-2px] self-center !mt-2 lg:!mt-0 lg:w-auto"
          :loading="candidatesQuestionnairesStore.status === 'searching'"
          @click="searchQuestionnaire"
        />
      </div>
    </div>
  </main>
</template>

<script>
import { ref, watch } from 'vue'

import { useRouter } from 'vue-router'

import { useCandidatesQuestionnairesStore } from '@/stores/candidates/questionnaires'

import PrimeButton from 'primevue/button'
import InputText from 'primevue/inputtext'

export default {
  components: { PrimeButton, InputText },
  setup() {
    const router = useRouter()

    const candidatesQuestionnairesStore = useCandidatesQuestionnairesStore()

    const secretCode = ref('')
    const searchButtonClicked = ref(false)

    watch(
      () => candidatesQuestionnairesStore.availableCode,
      (availableCode) => {
        if (availableCode) {
          router.push({ name: 'candidate.questionnaires.instructions' })
        }
      }
    )

    function searchQuestionnaire() {
      if (!searchButtonClicked.value) {
        searchButtonClicked.value = true
      }

      if (secretCode.value === '' || candidatesQuestionnairesStore.status === 'searching') {
        return
      }

      candidatesQuestionnairesStore.checkAvalability(secretCode.value)
    }

    return {
      candidatesQuestionnairesStore,
      secretCode,
      searchButtonClicked,
      searchQuestionnaire
    }
  }
}
</script>
