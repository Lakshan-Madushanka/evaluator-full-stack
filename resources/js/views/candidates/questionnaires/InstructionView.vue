<template>
  <div class="pt-4">
    <Card class="mx-2 md:mx-8 xl:max-w-4xl xl:mx-auto border mb-4">
      <template #title>
        <p class="text-center uppercase">
          {{ candidatesQuestionnairesStore.questionnaireInfo.name }}
        </p>
      </template>
      <template #content>
        <div class="py-4 px-2 space-y-8">
          <div>
            <p class="text-xl font-bold mb-4">Summary</p>
            <table class="table-auto border">
              <tbody class>
                <tr class="border">
                  <td class="border border-black px-4 py-2">Allocated time</td>
                  <td class="border border-black px-4 py-2">
                    {{
                      formatDuration(
                        candidatesQuestionnairesStore.questionnaireInfo.allocated_time,
                        'minutes'
                      )
                    }}
                  </td>
                </tr>
                <tr class="border">
                  <td class="border border-black px-4 py-2">Max time</td>
                  <td class="border border-black px-4 py-2">
                    {{
                      moment(candidatesQuestionnairesStore.questionnaireInfo.expires_at)
                        .local()
                        .format('YYYY-MM-DD HH:mm:ss')
                    }}
                  </td>
                </tr>
                <tr class="border">
                  <td class="border border-black px-4 py-2">No of questions</td>
                  <td class="border border-black px-4 py-2">
                    {{ candidatesQuestionnairesStore.questionnaireInfo.no_of_questions }}
                  </td>
                </tr>
                <tr class="border">
                  <td class="border border-black px-4 py-2">Answer type</td>
                  <td class="border border-black px-4 py-2">
                    {{
                      candidatesQuestionnairesStore.questionnaireInfo.single_answer_type
                        ? 'Single'
                        : 'Multiple'
                    }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div>
            <p class="text-xl font-bold mb-4">Instructions</p>
            <ul class="space-y-4 flex flex-col">
              <li class="flex">
                <p class="text-xl mr-2">&#x25CF;</p>
                <p>
                  Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem
                  Ipsum has been the industry's standard dummy text ever since the 1500s, when an
                  unknown printer took a galley of type and scrambled it to make a type specimen
                  book. It has
                </p>
              </li>
              <li class="flex">
                <p class="text-xl mr-2">&#x25CF;</p>
                <p>
                  Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem
                  Ipsum has been the industry's standard dummy text ever since the 1500s, when an
                  unknown printer took a galley of type and scrambled it to make a type specimen
                  book. It has
                </p>
              </li>
              <li class="flex">
                <p class="text-xl mr-2">&#x25CF;</p>
                <p>
                  Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem
                  Ipsum has been the industry's standard dummy text ever since the 1500s, when an
                  unknown printer took a galley of type and scrambled it to make a type specimen
                  book. It has
                </p>
              </li>
            </ul>
          </div>
        </div>
      </template>
      <template #footer>
        <div class="flex justify-between">
          <PrimeButton label="Cancel" @click="() => router.push({ name: 'home' })" />
          <PrimeButton
            label="Attempt"
            @click="() => router.push({ name: 'candidate.questionnaires.questions.show' })"
          />
        </div>
      </template>
    </Card>
  </div>
</template>

<script>
import { useRouter } from 'vue-router'
import { useCandidatesQuestionnairesStore } from '@/stores/candidates/questionnaires'
import PrimeButton from 'primevue/button'
import Card from 'primevue/card'
import moment from 'moment'
import { formatDuration } from '@/helpers'

export default {
  components: { Card, PrimeButton },
  setup() {
    const router = useRouter()
    const candidatesQuestionnairesStore = useCandidatesQuestionnairesStore()

    return {
      router,
      candidatesQuestionnairesStore,
      moment,
      formatDuration
    }
  }
}
</script>
