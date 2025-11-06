<template>
  <EvaluationView v-if="showEvaluation" />

  <ConfirmDialog />

  <QuestionnaireSkeleton v-if="candidatesQuestionnairesStore.loading" />
  <template v-else>
    <!-- Header -->
    <header
      class="bg-gray-900 shadow border p-4 text-white dark:text-white dark:bg-gray-900 flex flex-col items-center space-y-2 sm:space-y-0 sm:flex-row sm:justify-between sticky top-0 z-10"
    >
      <div class="flex items-center justify-center">
        <p class="text-xl font-bold mr-2">
          {{ questionnaire?.name }}
        </p>
      </div>
      <div>
        <vue-countdown
          v-slot="{ hours, minutes, seconds }"
          :time="getRemainingTime()"
          @end="onTimeElapsed()"
        >
          <span class="flex space-x-2">
            <span class="p-4 bg-white text-black font-bold">{{ hours }} H</span>
            <span class="p-4 bg-white text-black font-bold">{{ minutes }} M </span>
            <span class="p-4 bg-white text-black font-bold">{{ seconds }} S</span>
          </span>
        </vue-countdown>
      </div>
    </header>

    <!-- Body -->
    <div
      v-if="candidatesQuestionnairesStore.questions"
      :class="{ 'xl:grid grid-cols-[18%_82%]': showSidebar }"
    >
      <!-- Left side -->
      <div v-if="showSidebar" class="shadow p-4 bg-gray-50/10">
        <!-- Navigation map -->
        <div
          class="grid grid-cols-6 sm:grid-cols-10 xl:grid-cols-5 2xl:grid-cols-6 text-black dark:text-gray-400 text-center xl:sticky top-[6rem]"
        >
          <div
            v-for="(question, index) in candidatesQuestionnairesStore.questions"
            :key="`navigation-index-${question.id}`"
            :href="`#${question.id}_card`"
            class="shadow-md mr-1 mb-1 p-1 hover:cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 border border-solid border-1 border-gray-300"
            :class="{
              'bg-green-200 dark:bg-gray-600': getQuestionAnsweredStatus(question.id)
            }"
            @click="navigate(index + 1, question.id)"
          >
            {{ index + 1 }}
          </div>
        </div>
        <div class="mt-8 xl:fixed">
          <div class="flex justify-between items-center space-x-4 mb-2">
            <PrimeButton
              class="!w-44 !px-2"
              icon="pi pi-send"
              icon-pos="right"
              @click="showSubmitConfirmDialog"
            >
              <div class="flex justify-between items-center w-full space-x-2">
                <span class="font-bold">Submit</span>
                <Tag severity="success">
                  {{ noOfAnsweredQuestions }} /
                  {{ questionnaire.no_of_questions }}
                </Tag>
              </div>
            </PrimeButton>
          </div>
        </div>
      </div>
      <!--Sidebar toggler-->
      <div
        v-if="showSidebar"
        class="fixed bottom-2 left-1 z-50 hidden xl:block"
        @click="showSidebar = false"
      >
        <PrimeButton icon="pi pi-chevron-circle-left" severity="info" rounded aria-label="Save" />
      </div>
      <div v-else class="fixed bottom-2 z-50 left-2" @click="showSidebar = true">
        <PrimeButton icon="pi pi-chevron-circle-right" severity="info" rounded aria-label="Save" />
      </div>

      <!-- Right side -->
      <div class="p-4 space-y-4">
        <!-- Questionnaire -->
        <Card
          v-for="(question, questionIndex) in currentPageRecords"
          :id="`${question.id}_card`"
          :key="`question-${question.id}-card`"
        >
          <template #content>
            <div>
              <!-- Questions -->
              <div class="text-black dark:text-white">
                <div class="flex justify-between w-full">
                  <div class="flex w-[90%]">
                    <span class="mr-2">{{ getQuestionNo(questionIndex) }}).</span>
                    <div v-html="question.attributes.content" class="space-y-2"></div>
                  </div>
                </div>
                <!--Question images-->
                <div
                  v-if="question.relationships.images.data.length > 0"
                  class="mt-4 gap-2 flex flex-wrap justify-center"
                >
                  <PrimeImage
                    v-for="questionImage in question.relationships.images.data"
                    :key="`question-${question.id}-image-${questionImage.id}`"
                    :src="
                      findRelations(
                        candidatesQuestionnairesStore.meta.included,
                        questionImage.id,
                        questionImage.type
                      ).attributes.original_url
                    "
                    :alt="
                      findRelations(
                        candidatesQuestionnairesStore.meta.included,
                        questionImage.id,
                        questionImage.type
                      ).attributes.file_name
                    "
                    preview
                  />
                </div>
              </div>

              <!-- Answers -->
              <div class="ml-8 mt-8">
                <div
                  v-for="(answer, answerIndex) in questionAnswers[question.id]"
                  :key="`question-${question.id}-answer-${answer.id}`"
                  class="mt-4"
                >
                  <div v-if="question.attributes.answers_type_single" class="flex items-center">
                    <p class="mr-4">{{ String.fromCharCode(97 + answerIndex) }}).</p>
                    <RadioButton
                      v-model="userAnswers[question.id]"
                      :input-id="answer.id"
                      :name="answer.id"
                      :value="answer.id"
                      class="mr-2"
                    />

                    <label :for="answer.id" v-html="answer?.attributes?.text"></label>
                  </div>

                  <div v-else class="flex items-center">
                    <p class="mr-4">{{ String.fromCharCode(97 + answerIndex) }}).</p>
                    <Checkbox
                      v-model="userAnswers[question.id]"
                      :input-id="answer.id"
                      :name="answer.id"
                      :value="answer.id"
                      class="mr-2"
                    />

                    <label :for="answer.id" v-html="answer?.attributes?.text"></label>
                  </div>

                  <!-- Answer images -->
                  <div
                    v-if="answer.relationships.images?.data?.length > 0"
                    class="mt-4 gap-x-2 flex flex-wrap justify-start items-start gap-y-2"
                  >
                    <PrimeImage
                      v-for="answerImage in answer.relationships.images.data"
                      :key="`answer-${answer.id}-image-${answerImage.id}`"
                      :src="
                        findRelations(
                          candidatesQuestionnairesStore.meta.included,
                          answerImage.id,
                          answerImage.type
                        ).attributes.original_url
                      "
                      :alt="
                        findRelations(
                          candidatesQuestionnairesStore.meta.included,
                          answerImage.id,
                          answerImage.type
                        ).attributes.file_name
                      "
                      preview
                    />
                  </div>
                </div>
              </div>
            </div>
          </template>
        </Card>
      </div>
    </div>

    <Paginator
      v-model:first="paginator.offset"
      :rows="paginator.perPage"
      :total-records="candidatesQuestionnairesStore?.questions?.length"
      :rows-per-page-options="[10, 20, 30]"
      @page="onPageChange"
    ></Paginator>

    <ScrollTop />
  </template>
</template>

<script>
import { computed, nextTick, onMounted, onUnmounted, onUpdated, reactive, ref, watch } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useCandidatesQuestionnairesStore } from '@/stores/candidates/questionnaires'

import Card from 'primevue/card'
import Checkbox from 'primevue/checkbox'
import ConfirmDialog from 'primevue/confirmdialog'
import Paginator from 'primevue/paginator'
import PrimeButton from 'primevue/button'
import PrimeImage from 'primevue/image'
import RadioButton from 'primevue/radiobutton'
import ScrollTop from 'primevue/scrolltop'
import Tag from 'primevue/tag'
import { useToast } from 'primevue/usetoast'
import { useConfirm } from 'primevue/useconfirm'

import VueCountdown from '@chenfengyuan/vue-countdown'
import hljs from 'highlight.js'

import QuestionnaireSkeleton from '@/components/skeletons/QuestionnaireSkeleton.vue'
import EvaluationView from '@/views/candidates/questionnaires/EvaluationView.vue'

import { findRelations, formatMinutes } from '@/helpers'

import 'highlight.js/styles/stackoverflow-light.css'

export default {
  components: {
    Card,
    Checkbox,
    ConfirmDialog,
    Paginator,
    PrimeButton,
    PrimeImage,
    RadioButton,
    ScrollTop,
    Tag,
    QuestionnaireSkeleton,
    EvaluationView,
    VueCountdown
  },
  setup() {
    const route = useRoute()
    const router = useRouter()

    const toast = useToast()
    const confirm = useConfirm()

    const candidatesQuestionnairesStore = useCandidatesQuestionnairesStore()

    const includes = ['images', 'onlyAnswers.images']

    const showAnswers = ref(true)
    const showMarks = ref(true)
    const showSidebar = ref(true)

    let questionAnswers = reactive({})
    const userAnswers = ref({})

    let elapsedMinutes = 0
    const warnMessageTime = 5

    const currentPageRecords = ref()
    const paginator = { perPage: 10, page: 1, offset: 0 }

    const showEvaluation = ref(false)

    onMounted(() => {
      getQuestionsData()
    })

    onUpdated(() => {
      highlightCodeBlock()
      highlightInlineCode()
    })

    watch(
      () => candidatesQuestionnairesStore.questions,
      (newQuestions) => {
        if (newQuestions) {
          setAnswers(newQuestions)
          currentPageRecords.value = getPaginatorRecords()
        }
      },
      { immediate: true }
    )

    function getQuestionsData() {
      candidatesQuestionnairesStore.getAll({
        query: { includes }
      })
    }

    const minutesCounter = setInterval(() => {
      if (candidatesQuestionnairesStore.loading) {
        return
      }

      elapsedMinutes++

      if (
        candidatesQuestionnairesStore.questionnaireInfo?.allocated_time - elapsedMinutes ===
        warnMessageTime
      ) {
        toast.add({
          severity: 'warn',
          summary: `You have only ${warnMessageTime} minutes left`,
          detail: 'Answewrs are auto submitted after the time limit reached',
          life: '10000'
        })
      }
    }, 60000)

    function getRemainingTime() {
      return candidatesQuestionnairesStore.questionnaireInfo?.allocated_time * 60 * Math.pow(10, 3)
    }

    function setAnswers(newQuestions) {
      for (let question of newQuestions) {
        questionAnswers[question.id] = []

        for (let answer of question.relationships.onlyAnswers.data) {
          let relatedAnswer = findRelations(
            candidatesQuestionnairesStore.meta.included,
            answer.id,
            answer.type
          )

          questionAnswers[question.id].push(relatedAnswer)
        }
      }

      console.log(questionAnswers)
    }

    function onPageChange(event) {
      paginator.page = event.page + 1 // paginator start with page 0
      paginator.perPage = event.rows

      currentPageRecords.value = getPaginatorRecords()
    }

    function getQuestionNo(index) {
      index = parseInt(index)

      return (paginator.page - 1) * paginator.perPage + index + 1
    }

    function getPaginatorRecords() {
      let start_index = (paginator.page - 1) * paginator.perPage
      let end_index = start_index + paginator.perPage

      return candidatesQuestionnairesStore.questions?.slice(start_index, end_index)
    }

    async function navigate(questionNo, questionId) {
      setPaginatorOnNavigation(questionNo)

      await nextTick() // Await until finiish the update

      // Navigate to question
      let offSetTop = document.getElementById(`${questionId}_card`).offsetTop
      window.scrollTo({ top: offSetTop - 90, behavior: 'smooth' })
    }

    function setPaginatorOnNavigation(questionNo) {
      let page = Math.ceil(parseFloat(questionNo / paginator.perPage))

      paginator.page = page
      paginator.offset = page * paginator.perPage

      onPageChange({ page: page - 1, rows: paginator.perPage })
    }

    function getQuestionAnsweredStatus(questionId) {
      if (Array.isArray(userAnswers.value[questionId])) {
        return userAnswers.value[questionId]?.length > 0
      }
      return !!userAnswers.value[questionId]
    }

    const noOfAnsweredQuestions = computed(() => {
      let noOfAnswers = 0

      for (let answer in userAnswers.value) {
        if (Array.isArray(userAnswers.value[answer])) {
          if (userAnswers.value[answer].length > 0) {
            noOfAnswers++
          }
        } else if (userAnswers.value[answer]) {
          noOfAnswers++
        }
      }

      return noOfAnswers
    })

    function submit() {
      showEvaluation.value = true
      candidatesQuestionnairesStore.evaluate({ answers: getUserAnswers() })
    }

    function showSubmitConfirmDialog() {
      confirm.require({
        message: `${noOfAnsweredQuestions.value}  out of ${candidatesQuestionnairesStore.questionnaireInfo.no_of_questions} has answered !`,
        header: 'Are you sure you want to submit? ',
        icon: 'pi pi-info-circle',
        accept: () => {
          submit()
        },
        reject: () => {}
      })
    }

    function getUserAnswers() {
      let answers = {}

      for (let answer in userAnswers.value) {
        if (Array.isArray(userAnswers.value[answer])) {
          answers[answer] = userAnswers.value[answer]
        } else {
          answers[answer] = [userAnswers.value[answer]]
        }
      }

      if (Object.keys(answers).length === 0) {
        answers[candidatesQuestionnairesStore['questions'][0]['id']] = []
      }

      return answers
    }

    function onTimeElapsed() {
      showTimeElapseToast()
      submit()
    }

    function showTimeElapseToast() {
      toast.add({
        severity: 'warn',
        summary: 'Time elapsed',
        detail: 'Auto submitting answers ...',
        life: 5000
      })
    }

    onUnmounted(() => {
      clearInterval(minutesCounter)
    })

    function highlightCodeBlock() {
      hljs.configure({
        cssSelector: 'pre '
      })
      hljs.highlightAll()
    }

    function highlightInlineCode() {
      hljs.configure({
        cssSelector: 'code'
      })
      hljs.highlightAll()
    }

    return {
      route,
      router,
      currentPageRecords,
      candidatesQuestionnairesStore,
      questionnaire: computed(() => candidatesQuestionnairesStore.questionnaireInfo),
      paginator,
      userAnswers,
      showAnswers,
      showMarks,
      questionAnswers,
      showEvaluation,
      findRelations,
      onPageChange,
      getQuestionNo,
      navigate,
      formatMinutes,
      getRemainingTime,
      getQuestionAnsweredStatus,
      noOfAnsweredQuestions,
      showSubmitConfirmDialog,
      onTimeElapsed,
      showSidebar
    }
  }
}
</script>

<style>
pre {
  padding: 2px 4px !important;
}
</style>
