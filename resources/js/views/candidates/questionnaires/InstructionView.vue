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
                  Once you start the quiz, <strong>do not change or close</strong> the browser, or
                  <strong>navigate away</strong> from the test window. Otherwise, your test will
                  <strong>be auto submitted</strong>.
                </p>
              </li>

              <li>
                <div class="flex">
                  <p class="text-xl mr-2">&#x25CF;</p>
                  <p>
                    This quiz is limited to a <strong>single attempt</strong>. Make sure you are
                    ready before starting. Any interruptions during the quiz will lead to
                    <strong>disqualification</strong>, and <strong>no retakes</strong> will be
                    permitted.
                  </p>
                </div>

                <div class="mt-2 ml-6">
                  <p>Make sure</p>
                  <ul class="list-disc ml-8 mt-2">
                    <li>You have stable internet connection</li>
                    <li>You have stable power, and a backup is available if needed.</li>
                  </ul>
                </div>
              </li>

              <li class="flex">
                <p class="text-xl mr-2">&#x25CF;</p>
                <p>
                  The quiz will be <strong>automatically submitted</strong> when the allotted time
                  ends. You will receive a warning and a short grace period before submission.
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
