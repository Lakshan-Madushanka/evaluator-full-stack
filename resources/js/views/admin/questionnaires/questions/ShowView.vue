<template>
  <QuestionnaireSkeleton
    v-if="questionnairesQuestionsStore.loading || questionnairesStore.loading"
  />

  <template v-else>
    <!-- Header -->
    <header class="bg-gray-400 mt-[-1rem] p-4 mb-2 text-white dark:bg-gray-900">
      <div class="flex flex-col items-center space-y-2 sm:flex-row sm:justify-between">
        <div class="flex items-center justify-center">
          <p class="text-xl font-bold mr-4">
            {{ questionnaire?.name }}
          </p>
          <i
            class="pi pi-print !text-[var(--p-primary-color)] hover:cursor-pointer"
            style="font-size: 1.5rem"
            @click="showPrintView"
          ></i>
        </div>
        <div>
          <Badge
            :value="formatMinutes(questionnaire?.allocated_time)"
            severity="info"
            size="large"
          ></Badge>
        </div>
      </div>
    </header>

    <!-- Body -->
    <div v-if="questionnairesQuestionsStore.questions" class="xl:grid grid-cols-[18%_82%]">
      <!-- Left side -->
      <div class="shadow p-4 bg-white dark:bg-gray-900 mt-4">
        <!-- Navigation map -->
        <div
          class="grid grid-cols-6 sm:grid-cols-10 xl:grid-cols-5 2xl:grid-cols-6 text-black dark:text-gray-400 text-center xl:sticky top-[10px]"
        >
          <a
            v-for="(question, index) in questionnairesQuestionsStore.questions"
            :key="question.id"
            :href="`#${question.id}_card`"
            class="shadow-md mr-1 mb-1 p-1 hover:cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 border border-solid border-1 border-gray-300"
            @click="navigate(index + 1)"
          >
            {{ index + 1 }}
          </a>
        </div>
        <div class="mt-8 xl:sticky top-[19rem]">
          <div class="flex justify-between items-center space-x-4 mb-2">
            <label for="showAnswersCheckbox">Show Answers</label>
            <Checkbox v-model="showAnswers" input-id="showAnswersCheckbox" binary />
          </div>
          <div class="flex justify-between items-center space-x-4">
            <label for="showMarksCheckbox">Show Marks</label>
            <Checkbox v-model="showMarks" input-id="showMarksCheckbox" binary />
          </div>
        </div>
      </div>

      <!-- Right side -->
      <div class="p-4 space-y-4">
        <!-- Questionnaire -->
        <Card
          v-for="(question, questionIndex) in currrentPageRecords"
          :id="`${question.id}_card`"
          :key="question.id"
        >
          <template #content>
            <div>
              <!-- Questions -->
              <div class="text-black dark:text-white">
                <div class="flex justify-between w-full">
                  <div class="flex w-[90%]">
                    <p class="mr-2">{{ getQuestionNo(questionIndex) }}).</p>
                    <p v-html="question.attributes.content"></p>
                  </div>
                  <p v-if="showMarks">
                    <span class="hidden lg:inline">marks</span> ({{ question.attributes.marks }})
                  </p>
                </div>
                <!--Question images-->
                <div
                  v-if="question.relationships.images?.data?.length > 0"
                  class="mt-4 flex flex-wrap justify-start space-y-2"
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
                    <p class="mr-4">{{ String.fromCharCode(97 + answerIndex) }}).</p>
                    <RadioButton
                      v-model="correctAnswers[question.id]"
                      :input-id="answer.id"
                      :name="answer.id"
                      :value="answer.id"
                      class="mr-2"
                      disabled
                    />

                    <label :for="answer.id" v-html="answer?.attributes?.text"></label>
                  </div>

                  <div v-else class="flex items-center">
                    <p class="mr-4">{{ String.fromCharCode(97 + answerIndex) }}).</p>
                    <Checkbox
                      v-model="correctAnswers[question.id]"
                      :input-id="answer.id"
                      :name="answer.id"
                      :value="answer.id"
                      class="mr-2"
                      disabled
                    />

                    <label :for="answer.id" v-html="answer?.attributes?.text"></label>
                  </div>

                  <!-- Answer images -->
                  <div
                    v-if="answer.relationships.images?.data?.length > 0"
                    class="mt-4 flex flex-wrap justify-start space-y-2"
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
          </template>
        </Card>
      </div>
    </div>

    <Paginator
      v-model:first="paginator.offset"
      :rows="10"
      :total-records="questionnairesQuestionsStore?.questions?.length"
      :rows-per-page-options="[10, 20, 30]"
      @page="onPageChange"
    ></Paginator>

    <ScrollTop />
  </template>
</template>

<script>
import { computed, onMounted, reactive, ref, watch } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useQuestionnairesQuestionsStore } from '@/stores/questionnaires/questions'
import { useQuestionnairesStore } from '@/stores/questionnaires'

import Badge from 'primevue/badge'
import Card from 'primevue/card'
import Checkbox from 'primevue/checkbox'
import Paginator from 'primevue/paginator'
import PrimeImage from 'primevue/image'
import RadioButton from 'primevue/radiobutton'
import ScrollTop from 'primevue/scrolltop'

import QuestionnaireSkeleton from '@/components/skeletons/QuestionnaireSkeleton.vue'

import { findRelations, formatMinutes } from '@/helpers'

export default {
  components: {
    Badge,
    Card,
    Checkbox,
    Paginator,
    PrimeImage,
    RadioButton,
    ScrollTop,
    QuestionnaireSkeleton
  },
  setup() {
    const route = useRoute()
    const router = useRouter()

    const questionnairesQuestionsStore = useQuestionnairesQuestionsStore()
    const questionnairesStore = useQuestionnairesStore()

    const includes = ['images', 'answers.images']

    const showAnswers = ref(true)
    const showMarks = ref(true)

    let questionAnswers = reactive({})
    let correctAnswers = ref({})
    let cachedCorrectAnswers = {}

    const currrentPageRecords = ref()
    const paginator = { perPage: 10, page: 1, offset: 0 }

    onMounted(() => {
      getQuestionsData()
      getQuestionnaireData()
    })

    watch(
      () => questionnairesQuestionsStore.questions,
      (newQuestions) => {
        if (newQuestions) {
          setAnwers(newQuestions)
          currrentPageRecords.value = getPaginatorRecords()
        }
      }
    )

    watch(showAnswers, (showAnswers) => {
      if (showAnswers) {
        correctAnswers.value = cachedCorrectAnswers
      } else {
        correctAnswers.value = {}
      }
    })

    function setAnwers(newQuestions) {
      for (let question of newQuestions) {
        questionAnswers[question.id] = []

        for (let answer of question.relationships.answers.data) {
          let relatedAnswer = findRelations(
            questionnairesQuestionsStore.meta.included,
            answer.id,
            answer.type
          )
          questionAnswers[question.id].push(relatedAnswer)
          setCorrectAnswer(question, relatedAnswer)
          cachedCorrectAnswers = JSON.parse(JSON.stringify(correctAnswers.value))
        }
      }
    }

    function setCorrectAnswer(question, answer) {
      if (question.attributes.answers_type_single) {
        if (answer.attributes.correct_answer) {
          correctAnswers.value[question.id] = answer.id
        }
      } else {
        if (!correctAnswers.value[question.id]) {
          correctAnswers.value[question.id] = []
        }
        if (answer.attributes.correct_answer) {
          correctAnswers.value[question.id].push(answer.id)
        }
      }
    }

    function getQuestionsData() {
      questionnairesQuestionsStore.getAll(route.params.id, {
        query: { includes }
      })
    }

    function getQuestionnaireData() {
      questionnairesStore.getOne(route.params.id)
    }

    function onPageChange(event) {
      paginator.page = event.page + 1 // paginator start with page 0
      paginator.perPage = event.rows

      currrentPageRecords.value = getPaginatorRecords()
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

    function navigate(questionNo) {
      let page = Math.ceil(parseFloat(questionNo / paginator.perPage))

      paginator.page = page
      paginator.offset = page * paginator.perPage

      onPageChange({ page: page - 1, rows: paginator.perPage })
    }

    function showPrintView() {
      const routeData = router.resolve({
        name: 'admin.questionnaires.questions.print',
        params: { id: route.params.id },
        query: { showMarks: showMarks.value, showAnswers: showAnswers.value }
      })

      window.open(routeData.href, '_blank')
    }

    return {
      route,
      router,
      currrentPageRecords,
      questionnairesQuestionsStore,
      questionnairesStore,
      questionnaire: computed(() => questionnairesStore.questionnaire?.data?.attributes),
      showAnswers,
      showMarks,
      questionAnswers,
      correctAnswers,
      findRelations,
      onPageChange,
      getQuestionNo,
      navigate,
      showPrintView,
      paginator,
      formatMinutes
    }
  }
}
</script>
