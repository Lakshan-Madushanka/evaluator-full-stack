<template>
  <div
    v-if="
      questionnairesQuestionsStore.loading ||
      questionnairesStore.loading ||
      evaluationsStore.loading
    "
    class="w-[210mm] mx-auto"
  >
    <Skeleton height="8rem" class="mb-6" />
    <div class="bg-white flex flex-col items-center justify-center dark:bg-black">
      <Skeleton v-for="n in 10" :key="n" class="m-4" height="16rem" width="95%" />
    </div>
  </div>
  <div v-else class="w-[210mm] mx-auto">
    <!-- Header -->
    <header
      class="p-4 mb-4 text-black dark:text-white border-b-2 border-black dark:border-white"
      @click="test"
    >
      <div class="flex flex-col items-center justify-center space-y-2">
        <div class="flex items-center justify-center mb-4">
          <p class="text-2xl font-bold mr-4 uppercase">
            {{ questionnaire?.name }}
          </p>
          <i
            @click="print"
            class="print:!hidden pi pi-print !text-xl text-[var(--p-primary-color)] cursor-pointer"
          />
        </div>
        <div class="flex flex-col justify-center items-center text-base font-bold w-[25rem]">
          <p class="flex justify-between w-full">
            <span class="mr-2">Time limit</span>
            <span>{{ formatMinutes(questionnaire?.allocated_time) }}</span>
          </p>
          <p class="flex justify-between w-full">
            <span class="mr-2">Answers type</span>
            <span>{{ questionnaire?.single_answers_type ? 'Single' : 'Multiple' }}</span>
          </p>
        </div>
      </div>
    </header>

    <!-- Questionnaire -->
    <div class="p-4 space-y-8">
      <div
        v-for="(question, questionIndex) in currentPageRecords"
        :id="`${question.id}_card`"
        :key="question.id"
      >
        <!-- Questions -->
        <div class="text-black dark:text-white">
          <div class="flex justify-between w-full">
            <div class="flex w-[90%]">
              <p class="mr-2">{{ getQuestionNo(questionIndex) }}).</p>
              <p v-html="question.attributes.content"></p>
            </div>
            <p v-if="route.query.showMarks === 'true'">
              <span class="hidden lg:inline">marks</span> ({{ question.attributes.marks }})
            </p>
          </div>
          <!--Question images-->
          <div
            v-if="question.relationships?.images?.data?.length > 0"
            class="mt-4 flex flex-wrap justify-center space-y-2"
          >
            <PrimeImage
              v-for="questionImage in question.relationships.images.data"
              :key="questionImage.id"
              :src="
                findRelations(
                  questionnairesQuestionsStore.meta.included,
                  questionImage.id,
                  questionImage.type
                ).attributes.original_url
              "
              :alt="
                findRelations(
                  questionnairesQuestionsStore.meta.included,
                  questionImage.id,
                  questionImage.type
                ).attributes.file_name
              "
              preview
            />
          </div>
        </div>

        <!-- Answers -->
        <div class="ml-8 mt-2">
          <div
            v-for="(answer, answerIndex) in questionAnswers[question.id]"
            :key="answer.id"
            class="mt-4"
          >
            <div v-if="question.attributes.answers_type_single" class="flex items-center">
              <p class="mr-4">{{ answerIndex + 1 }}</p>
              <RadioButton
                v-if="evaluationsStore.evaluation.answers?.[question.id]?.[0]"
                v-model="evaluationsStore.evaluation.answers[question.id][0]"
                :input-id="answer.id"
                :name="answer.id"
                :value="answer.id"
                class="mr-2"
                disabled
              />
              <RadioButton
                v-else
                :input-id="answer.id"
                :name="answer.id"
                :value="answer.id"
                class="mr-2"
                disabled
              />

              <label :for="answer.id" v-html="answer?.attributes?.text"> </label>

              <i
                v-if="answer.id === correctAnswers[question.id]"
                class="ml-4 text-green-500 pi pi-check"
              ></i>
            </div>

            <div v-else class="flex items-center">
              <p class="mr-4">{{ answerIndex + 1 }}</p>
              <Checkbox
                v-model="evaluationsStore.evaluation.answers[question.id]"
                :input-id="answer.id"
                :name="answer.id"
                :value="answer.id"
                class="mr-2"
                disabled
              />

              <label :for="answer.id" v-html="answer?.attributes?.text"></label>

              <i
                v-if="correctAnswers[question.id].includes(answer.id)"
                class="ml-4 text-green-500 pi pi-check"
              ></i>
            </div>

            <!-- Answer images -->
            <div
              v-if="answer.relationships?.images?.data?.length > 0"
              class="mt-4 flex flex-wrap justify-center space-y-2"
            >
              <PrimeImage
                v-for="answerImage in answer.relationships.images.data"
                :key="answerImage.id"
                :src="
                  findRelations(
                    questionnairesQuestionsStore.meta.included,
                    answerImage.id,
                    answerImage.type
                  ).attributes.original_url
                "
                :alt="
                  findRelations(
                    questionnairesQuestionsStore.meta.included,
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
    </div>
  </div>
</template>

<script>
import { computed, onMounted, reactive, ref, watch } from 'vue'

import { useRoute } from 'vue-router'

import { useQuestionnairesQuestionsStore } from '@/stores/questionnaires/questions'
import { useQuestionnairesStore } from '@/stores/questionnaires'
import { useEvaluationsStore } from '@/stores/evaluations'

import Checkbox from 'primevue/checkbox'
import PrimeImage from 'primevue/image'
import RadioButton from 'primevue/radiobutton'
import Skeleton from 'primevue/skeleton'

import { findRelations, formatMinutes } from '@/helpers'

export default {
  components: {
    Checkbox,
    PrimeImage,
    RadioButton,
    Skeleton
  },
  setup() {
    const route = useRoute()

    const questionnairesQuestionsStore = useQuestionnairesQuestionsStore()
    const questionnairesStore = useQuestionnairesStore()
    const evaluationsStore = useEvaluationsStore()

    const includes = ['images', 'answers.images']

    let questionAnswers = reactive({})
    const correctAnswers = reactive({})

    const currentPageRecords = ref()
    const paginator = { perPage: 10, page: 1, offset: 0 }

    onMounted(() => {
      getQuestionsData()
      getQuestionnaireData()
      getEvaluationData()
    })

    watch(
      () => questionnairesQuestionsStore.questions,
      (newQuestions) => {
        if (newQuestions) {
          setAnswers(newQuestions)
          currentPageRecords.value = getPaginatorRecords()
        }
      }
    )

    function setAnswers(newQuestions) {
      for (let question of newQuestions) {
        questionAnswers[question.id] = []

        for (let answer of question.relationships.answers.data) {
          let relatedAnswer = findRelations(
            questionnairesQuestionsStore.meta.included,
            answer.id,
            answer.type
          )
          questionAnswers[question.id].push(relatedAnswer)
          if (route.query.showAnswers === 'true') {
            setCorrectAnswer(question, relatedAnswer)
          }
        }
      }
    }

    function setCorrectAnswer(question, answer) {
      if (question.attributes.answers_type_single) {
        if (answer.attributes.correct_answer) {
          correctAnswers[question.id] = answer.id
        }
      } else {
        if (!correctAnswers[question.id]) {
          correctAnswers[question.id] = []
        }
        if (answer.attributes.correct_answer) {
          correctAnswers[question.id].push(answer.id)
        }
      }
    }

    function getQuestionsData() {
      questionnairesQuestionsStore.getAll(route.params.questionnaireId, {
        query: { includes }
      })
    }

    function getQuestionnaireData() {
      questionnairesStore.getOne(route.params.questionnaireId)
    }

    function getEvaluationData() {
      evaluationsStore.getOne(route.params.evaluationId)
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

      return questionnairesQuestionsStore.questions?.slice(start_index, end_index)
    }

    async function navigate(questionNo) {
      let page = Math.ceil(parseFloat(questionNo / paginator.perPage))

      paginator.page = page
      paginator.offset = page * paginator.perPage

      onPageChange({ page: page - 1, rows: paginator.perPage })
    }

    function print() {
      window.print()
    }

    return {
      route,
      currentPageRecords,
      questionnairesStore,
      questionnairesQuestionsStore,
      questionnaire: computed(() => questionnairesStore.questionnaire?.data?.attributes),
      questionAnswers,
      correctAnswers,
      findRelations,
      onPageChange,
      getQuestionNo,
      navigate,
      paginator,
      formatMinutes,
      print,
      evaluationsStore
    }
  }
}
</script>
