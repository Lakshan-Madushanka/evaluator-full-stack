<template>
  <ConfirmDialog />

  <div class="shadow-lg bg-white dark:bg-inherit p-4 pb-8">
    <div class="flex flex-wrap items-center justify-center sm:justify-between">
      <h1 class="text-2xl font-bold uppercase mb-2">
        Manage questions of questionnaire
        <span class="text-green-400 mr-1">{{ route.params.id }}</span>
        <span class="text-sm lowercase">(id)</span>
      </h1>
      <PrimeButton icon="pi pi-spinner" @click="reset" />
    </div>
    <div class="flex mt-8 justify-center sm:justify-start">
      <ul class="w-96">
        <li
          class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50 flex justify-between"
        >
          <p>Easy questions</p>
          <Tag severity="success">
            {{ data.assignedQuestions.easy }} /
            {{ route.query.no_of_easy_questions }}
          </Tag>
        </li>
        <li
          class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50 flex justify-between"
        >
          <p>Medium questions</p>
          <Tag severity="success">
            {{ data.assignedQuestions.medium }} /
            {{ route.query.no_of_medium_questions }}
          </Tag>
        </li>
        <li
          class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50 flex justify-between"
        >
          <p>Hard questions</p>
          <Tag severity="success">
            {{ data.assignedQuestions.hard }} /
            {{ route.query.no_of_hard_questions }}
          </Tag>
        </li>
        <li
          class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50 flex justify-between"
        >
          <p>Total questions</p>
          <Tag severity="success">
            {{ data.assignedQuestions.total }} /
            {{ route.query.no_of_total_questions }}
          </Tag>
        </li>
      </ul>
    </div>
    <div class="mt-8">
      <Skeleton v-if="questionnairesQuestionsStore.loading" class="mb-2 !w-full !h-72"></Skeleton>

      <div v-else class="shadow">
        <div
          class="flex justify-between px-4 items-center w-full py-8 bg-gray-100 dark:bg-black border-y-2 border-neutral-200 dark:border-neutral-700"
        >
          <div class="text-lg font-bold flex items-center gap-4">
            <p>List of Questions ({{ getListQuestionsCount() }})</p>
            <PrimeButton
              severity="info"
              icon="pi pi-search-plus"
              rounded
              size="small"
              area-label="save"
              @click="maximizeQuestionList = true"
            />
          </div>
          <Select
            v-model="selectedDiffculty"
            :options="difficultyFilterOptions"
            option-label="name"
            placeholder="Difficulty"
          />
        </div>

        <!--Question List-->
        <TransitionGroup name="list" tag="ul" class="h-[25rem] dark:bg-black overflow-y-auto">
          <li
            v-for="(question, index) of data.questions"
            :key="question.id"
            :class="{ 'bg-blue-200 dark:bg-gray-900': isQuestionSelected(question) }"
            @click="selectQuestion($event, question)"
          >
            <div
              v-if="question.show"
              class="flex justify-between border-b-2 border-neutral-200 dark:border-neutral-600 p-4 hover:cursor-pointer"
            >
              <div class="flex w-[75%]">
                <span class="mr-2">{{ index + 1 }}).</span>
                <p v-html="question.attributes.content"></p>
              </div>
              <div class="space-y-1">
                <p>
                  ID
                  <Tag severity="secondary" size="small">{{ question.attributes.pretty_id }}</Tag>
                </p>
                <p>
                  Single answers type
                  <Tag severity="secondary" size="small">{{
                    question.attributes.answers_type_single
                  }}</Tag>
                </p>
                <p>
                  Difficulty
                  <Tag severity="secondary" size="small"> {{ question.attributes.hardness }}</Tag>
                </p>
                <p>
                  Answers count
                  <Tag severity="secondary" size="small">
                    {{ question.attributes.no_of_answers }}
                  </Tag>
                </p>
                <p>
                  Images count
                  <Tag severity="secondary" size="small">
                    {{ question.attributes.images_count }}
                  </Tag>
                </p>
                <p class="mt-2">
                  Marks:
                  <InputNumber
                    v-model="question.attributes.marks"
                    input-class="w-24 h-10 dark:text-white"
                    show-buttons
                    :min="1"
                    :max="10"
                    @click.stop.prevent
                  />
                </p>
              </div>
            </div>
          </li>
        </TransitionGroup>
      </div>
      <div class="mt-8 flex justify-center flex-wrap items-start">
        <PrimeButton
          :label="questionnairesQuestionsStore.status === 'syncing' ? 'Syncing' : 'Sync All'"
          icon="pi pi-history"
          class="!mb-2 !mr-4 p-button-warning"
          :loading="questionnairesQuestionsStore.status === 'syncing'"
          @click="syncQuestions"
        />
        <span v-if="data.questions.length > 0" class="p-buttonset space-x-4 mr-4 mb-2">
          <PrimeButton label="Select All" icon="pi pi-clone" @click="selectAllQuestions" />
          <PrimeButton label="Deselect All" icon="pi pi-times" @click="deselectAllQuestions" />
        </span>
        <PrimeButton
          v-if="selectedQuestions.length > 0"
          icon="pi pi-trash"
          label="Remove Selected"
          class="p-button-warning"
          @click="removeSelectedQuestions"
        />
      </div>
    </div>

    <PrimeDialog
      v-if="maximizeQuestionList"
      v-model:visible="maximizeQuestionList"
      :closable="false"
      :maximizable="true"
      :dismissable-mask="true"
      modal
      :style="{ width: '90vw', height: '100vh' }"
    >
      <template #header>
        <div
          class="flex justify-between px-4 items-center w-full py-8 bg-gray-100 border-y-2 border-neutral-200"
        >
          <div class="text-lg font-bold flex items-centenr gap-4">
            <p>List of Questions ({{ getListQuestionsCount() }})</p>
            <PrimeButton
              icon="pi pi-search-minus"
              severity="info"
              rounded
              size="small"
              area-label="save"
              @click="maximizeQuestionList = false"
            />
          </div>
          <Select
            v-model="selectedDiffculty"
            :options="difficultyFilterOptions"
            option-label="name"
            placeholder="Difficulty"
          />
        </div>
      </template>

      <!--Question List-->
      <TransitionGroup name="list" tag="ul" class="h-[25rem]">
        <li
          v-for="(question, index) of data.questions"
          :key="question.id"
          :class="{ 'bg-blue-200': isQuestionSelected(question) }"
          @click="selectQuestion($event, question)"
        >
          <div
            v-if="question.show"
            class="flex justify-between border-b-2 border-neutral-200 p-4 hover:cursor-pointer"
          >
            <div class="flex w-[75%]">
              <span class="mr-2">{{ index + 1 }}).</span>
              <p v-html="question.attributes.content"></p>
            </div>
            <div class="space-y-1">
              <p>
                ID
                <Tag severity="secondary" size="small">{{ question.attributes.pretty_id }}</Tag>
              </p>
              <p>
                Single answers type
                <Tag severity="secondary" size="small">{{
                  question.attributes.answers_type_single
                }}</Tag>
              </p>
              <p>
                Difficulty
                <Tag severity="secondary" size="small"> {{ question.attributes.hardness }}</Tag>
              </p>
              <p>
                Answers count
                <Tag severity="secondary" size="small">
                  {{ question.attributes.no_of_answers }}
                </Tag>
              </p>
              <p>
                Images count
                <Tag severity="secondary" size="small">
                  {{ question.attributes.images_count }}
                </Tag>
              </p>
              <p class="mt-2">
                Marks:
                <InputNumber
                  v-model="question.attributes.marks"
                  input-class="w-16 h-10"
                  show-buttons
                  :min="1"
                  :max="10"
                  @click.stop.prevent
                />
              </p>
            </div>
          </div>
        </li>
      </TransitionGroup>
      <!--Actions-->
      <template #footer>
        <div class="mt-8 flex justify-center flex-wrap items-center">
          <PrimeButton
            :label="questionnairesQuestionsStore.status === 'syncing' ? 'Syncing' : 'Sync All'"
            icon="pi pi-history"
            class="!mb-2 !mr-4 p-button-warning"
            :loading="questionnairesQuestionsStore.status === 'syncing'"
            @click="syncQuestions"
          />
          <span v-if="data.questions.length > 0" class="p-buttonset space-x-4 mr-4 mb-2">
            <PrimeButton label="Select All" icon="pi pi-clone" @click="selectAllQuestions" />
            <PrimeButton label="Deselect All" icon="pi pi-times" @click="deselectAllQuestions" />
          </span>
          <PrimeButton
            v-if="selectedQuestions.length > 0"
            icon="pi pi-trash"
            label="Remove Selected"
            class="p-button-warning"
            @click="removeSelectedQuestions"
          />
        </div>
      </template>
    </PrimeDialog>

    <div class="mt-8">
      <p class="text-xl font-bold mb-4">Find Question</p>
      <div class="flex items-start">
        <div class="w-1/2">
          <InputText
            v-model="questionId"
            type="search"
            class="w-full"
            placeholder="Question Id"
            @keyup.enter="searchQuestion"
          />
          <p
            v-if="questionId === '' && queestionIdSearchButtonClicked"
            class="text-sm m-2 text-red-500"
          >
            Question id is required
            {{ questionnairesQuestionsStore.errors.questionId }}
          </p>
          <p v-if="questionnairesQuestionsStore.errors.questionId" class="text-sm m-2 text-red-500">
            {{ questionnairesQuestionsStore.errors.questionId }}
          </p>
        </div>

        <PrimeButton
          :label="questionnairesQuestionsStore.status === 'searching' ? 'Searching' : 'Search'"
          icon="pi pi-search"
          icon-pos="right"
          :loading="questionnairesQuestionsStore.status === 'searching'"
          @click="searchQuestion"
        />
      </div>
    </div>

    <div v-if="questionnairesQuestionsStore.question" class="my-8">
      <Card>
        <template #title>
          <div class="flex justify-between items-center">
            <div><p class="!text-lg">Results</p></div>
            <div>
              <i
                v-if="!canAddToList()"
                v-tooltip.left="{ value: addToListError }"
                class="pi pi-info-circle mr-4 !text-2xl text-yellow-500"
              />
              <PrimeButton
                class="p-button-sm"
                label="Add"
                :disabled="!canAddToList()"
                @click="addToList"
              />
            </div>
          </div>
        </template>
        <template #content>
          <div class="flex justify-between">
            <div class="flex w-[75%]">
              <p v-html="questionnairesQuestionsStore.question.attributes.content"></p>
            </div>
            <div>
              <p>
                ID:
                {{ questionnairesQuestionsStore.question.attributes.pretty_id }}
              </p>
              <p>
                Single answers type:
                {{ questionnairesQuestionsStore.question.attributes.answers_type_single }}
              </p>
              <p>
                Difficulty:
                {{ questionnairesQuestionsStore.question.attributes.hardness }}
              </p>
              <p>
                Answers count:
                {{ questionnairesQuestionsStore.question.attributes.no_of_answers }}
              </p>
              <p>
                Images count:
                {{ questionnairesQuestionsStore.question.attributes.images_count }}
              </p>
              <p>
                Marks:
                {{ questionnairesQuestionsStore.question.attributes.marks }}
              </p>
            </div>
          </div>
        </template>
        <template #footer> </template>
      </Card>
    </div>

    <div class="mt-16">
      <EligibleQuestionsList
        v-if="isVisible"
        :questionnaire-id="route.params.id"
        :refresh="shouldRefreshEligibleQuestionList"
        @selection-change="onSelectionChange"
        @add="onAdd"
      />
    </div>

    <div ref="el" class="invisible mt-16"></div>
  </div>
</template>

<script>
import { onMounted, reactive, ref, watch } from 'vue'

import { useRoute } from 'vue-router'

import { useQuestionnairesQuestionsStore } from '@/stores/questionnaires/questions'

import Card from 'primevue/card'
import ConfirmDialog from 'primevue/confirmdialog'
import Select from 'primevue/select'
import PrimeDialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import PrimeButton from 'primevue/button'
import Skeleton from 'primevue/skeleton'
import Tag from 'primevue/tag'
import { useConfirm } from 'primevue/useconfirm'

import EligibleQuestionsList from '@/views/admin/questionnaires/questions/components/EligibleQuestionsList.vue'

import useIntersectionObserver from '@/composables/useIntersectionObserver'

export default {
  components: {
    Card,
    ConfirmDialog,
    Select,
    PrimeDialog,
    PrimeButton,
    Skeleton,
    Tag,
    InputText,
    InputNumber,
    EligibleQuestionsList
  },
  setup() {
    const confirm = useConfirm()

    const route = useRoute()

    const el = ref(null)

    const { isVisible } = useIntersectionObserver(el)

    const questionnairesQuestionsStore = useQuestionnairesQuestionsStore()

    const maximizeQuestionList = ref(false)

    const showEligibleQuestionList = ref(false)

    const shouldRefreshEligibleQuestionList = ref(false)

    const difficultyFilterOptions = [
      { name: 'All', value: 1 },

      { name: 'Easy', value: 2 },
      { name: 'Medium', value: 3 },
      { name: 'Hard', value: 4 }
    ]
    const selectedDiffculty = ref(null)

    const data = reactive({
      questions: [],
      assignedQuestions: { easy: 0, medium: 0, hard: 0, total: 0 }
    })
    const selectedQuestions = ref([])
    const questionId = ref('')
    const queestionIdSearchButtonClicked = ref(false)
    const addToListError = ref('')

    onMounted(() => {
      getData()
    })

    watch(selectedDiffculty, (diffculty) => {
      const diffcultyValue = diffculty.value

      if (diffcultyValue === 1) {
        data.questions.forEach((question) => {
          question.show = true
        })
      } else if (diffcultyValue === 2) {
        data.questions.forEach((question) => {
          if (question.attributes.hardness === 'EASY') {
            question.show = true
          } else {
            question.show = false
          }
        })
      } else if (diffcultyValue === 3) {
        data.questions.forEach((question) => {
          if (question.attributes.hardness === 'MEDIUM') {
            question.show = true
          } else {
            question.show = false
          }
        })
      } else if (diffcultyValue === 4) {
        data.questions.forEach((question) => {
          if (question.attributes.hardness === 'HARD') {
            question.show = true
          } else {
            question.show = false
          }
        })
      }
    })

    watch(
      () => questionnairesQuestionsStore.questions,
      (newQuestions) => {
        data.questions = JSON.parse(JSON.stringify(newQuestions))
        data.questions.forEach((question) => {
          question.show = true
          setAssignedQuestionsCount(question, 'increment')
        })
      }
    )

    function getData() {
      questionnairesQuestionsStore
        .getAll(route.params.id)
        .then(() => (shouldRefreshEligibleQuestionList.value = true))
    }

    function setAssignedQuestionsCount(question, operation) {
      if (question.attributes.hardness === 'EASY') {
        if (operation === 'increment') {
          data.assignedQuestions.easy++
        } else {
          data.assignedQuestions.easy--
        }
      } else if (question.attributes.hardness === 'MEDIUM') {
        if (operation === 'increment') {
          data.assignedQuestions.medium++
        } else {
          data.assignedQuestions.medium--
        }
      } else if (question.attributes.hardness === 'HARD') {
        if (operation === 'increment') {
          data.assignedQuestions.hard++
        } else {
          data.assignedQuestions.hard--
        }
      }

      if (operation === 'increment') {
        data.assignedQuestions.total++
        return
      }

      data.assignedQuestions.total--
    }

    function removeSelectedQuestions() {
      let selectedQuestionsLength = selectedQuestions.value.length
      let questions = JSON.parse(JSON.stringify(data.questions))

      for (
        let selectedQuestion = 0;
        selectedQuestion < selectedQuestionsLength;
        selectedQuestion++
      ) {
        for (let question = 0; question < questions.length; question++) {
          if (selectedQuestions.value[selectedQuestion].id === questions[question].id) {
            setAssignedQuestionsCount(questions[question], 'decrement')
            questions.splice(question, 1)
            data.assignedQuestions.total--
            break
          }
        }
      }

      data.questions = questions
      selectedQuestions.value = []
    }

    function searchQuestion() {
      queestionIdSearchButtonClicked.value = true
      if (questionId.value === '') {
        return
      }

      questionnairesQuestionsStore.checkQuestionEligibility(route.params.id, questionId.value)
    }

    function canAddToList(question = null) {
      if (!question) {
        question = questionnairesQuestionsStore.question
      }
      const difficulty = question.attributes.hardness

      if (data.assignedQuestions.total === parseInt(route.query.no_of_total_questions)) {
        addToListError.value = 'No of allowed total questions exceeded'
        return false
      }
      if (
        difficulty === 'EASY' &&
        data.assignedQuestions.easy === parseInt(route.query.no_of_easy_questions)
      ) {
        addToListError.value = 'No of allowed easy questions exceeded'
        return false
      }
      if (
        difficulty === 'MEDIUM' &&
        data.assignedQuestions.medium === parseInt(route.query.no_of_medium_questions)
      ) {
        addToListError.value = 'No of allowed medium questions exceeded'
        return false
      }
      if (
        difficulty === 'HARD' &&
        data.assignedQuestions.hard === parseInt(route.query.no_of_hard_questions)
      ) {
        addToListError.value = 'No of allowed hard questions exceeded'
        return false
      }

      if (checkQuestionExists(question)) {
        addToListError.value = 'Question already exists'

        return false
      }

      return true
    }

    function checkQuestionExists(question) {
      for (const q of data.questions) {
        if (q.id === question.id) {
          return true
        }
      }

      return false
    }

    function addToList(question = null) {
      if (!question) {
        question = questionnairesQuestionsStore.question
      }
      question.show = true
      question.attributes.marks = 1
      setAssignedQuestionsCount(question, 'increment')
      data.questions.unshift(question)
    }

    function getListQuestionsCount() {
      let count = 0

      data.questions.forEach((question) => {
        if (question.show) {
          count++
        }
      })

      return count
    }

    function selectQuestion(event, question) {
      const length = selectedQuestions.value.length
      for (let $i = 0; $i < length; $i++) {
        if (selectedQuestions.value[$i].id === question.id) {
          selectedQuestions.value.splice($i, 1)
          return
        }
      }
      selectedQuestions.value.push(question)
    }

    function deselectAllQuestions() {
      selectedQuestions.value = []
    }

    function selectAllQuestions() {
      selectedQuestions.value = data.questions
    }

    function reset() {
      selectedQuestions.value = []
      selectedDiffculty.value = difficultyFilterOptions[0]
      questionId.value = ''
      data.assignedQuestions.easy = 0
      data.assignedQuestions.medium = 0
      data.assignedQuestions.hard = 0
      data.assignedQuestions.total = 0
      shouldRefreshEligibleQuestionList.value = false
      getData()
    }

    function isQuestionSelected(question) {
      const length = selectedQuestions.value.length
      for (let $i = 0; $i < length; $i++) {
        if (selectedQuestions.value[$i].id === question.id) {
          return true
        }
      }

      return false
    }

    function prepareQuestionsToSync() {
      let questions = []

      data.questions.forEach((question) => {
        let tmpQuestion = {}
        tmpQuestion.id = question.id
        tmpQuestion.marks = question.attributes.marks

        questions.push(tmpQuestion)
      })

      return questions
    }

    function syncQuestions() {
      shouldRefreshEligibleQuestionList.value = false

      questionnairesQuestionsStore
        .syncQuestions(route.params.id, {
          questions: prepareQuestionsToSync()
        })
        .then(() => {
          shouldRefreshEligibleQuestionList.value = true
        })
    }

    function onEligibleQuestionListVisible(isVisible) {
      if (isVisible) {
        showEligibleQuestionList.value = true
      }
    }

    function onAdd(question) {
      if (!canAddToList(question)) {
        showWarningDialog(addToListError.value)
        return
      }

      addToList(question)
      showSuccessDialog('Question is added to the list')
    }

    function onSelectionChange(questions) {
      if (questions.length > route.query.no_of_total_questions - data.assignedQuestions.total) {
        showWarningDialog('No of allowed questions limit exceeded!')
        return
      }

      const q = { hard: 0, easy: 0, medium: 0 }

      for (let key in questions) {
        if (data.questions.some((quest) => quest.id === questions[key]['id'])) {
          showWarningDialog(`Queestion with id ${questions[key]['id']} already exists!`)
          return
        }
        if (questions[key]['attributes']['hardness'] === 'EASY') {
          q.easy++
        } else if (questions[key]['attributes']['hardness'] === 'MEDIUM') {
          q.medium++
        } else {
          q.hard++
        }
        questions[key].show = true
        questions[key].attributes.marks = 1
      }

      if (data.assignedQuestions.easy + q.easy > route.query.no_of_easy_questions) {
        showWarningDialog('No of allowed easy questions limit exceeded!')
        return
      }

      if (data.assignedQuestions.medium + q.medium > route.query.no_of_medium_questions) {
        showWarningDialog('No of allowed medium questions limit exceeded!')
        return
      }

      if (data.assignedQuestions.hard + q.hard > route.query.no_of_hard_questions) {
        showWarningDialog('No of allowed hard questions limit exceeded!')
        return
      }

      data.questions = [...data.questions, ...questions]

      questions.forEach((question) => {
        setAssignedQuestionsCount(question, 'increment')
      })

      showSuccessDialog("Selected questions added to list; Don't forget to sync!")
    }

    function showWarningDialog(msg) {
      confirm.require({
        message: msg,
        header: 'Warning',
        icon: 'pi pi-info-circle',
        rejectLabel: 'Cancel',
        rejectProps: {
          label: 'Cancel',
          severity: 'warn',
          outlined: true
        },
        acceptProps: {
          class: '!hidden'
        }
      })
    }

    function showSuccessDialog(msg) {
      confirm.require({
        message: msg,
        header: 'Success',
        icon: 'pi pi-check',
        rejectProps: {
          class: '!hidden'
        },
        acceptProps: {
          label: 'Back',
          severity: 'success',
          outlined: true
        }
      })
    }

    return {
      isVisible,
      el,
      data,
      selectedDiffculty,
      difficultyFilterOptions,
      selectedQuestions,
      addToListError,
      route,
      questionnairesQuestionsStore,
      removeSelectedQuestions,
      reset,
      questionId,
      searchQuestion,
      queestionIdSearchButtonClicked,
      addToList,
      canAddToList,
      getListQuestionsCount,
      selectQuestion,
      isQuestionSelected,
      selectAllQuestions,
      deselectAllQuestions,
      syncQuestions,
      maximizeQuestionList,
      showEligibleQuestionList,
      onEligibleQuestionListVisible,
      onSelectionChange,
      shouldRefreshEligibleQuestionList,
      onAdd
    }
  }
}
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
