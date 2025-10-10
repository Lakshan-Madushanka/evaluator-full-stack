<template>
  <FormLayout v-if="questionnairesStore.loading">
    <template #header>
      <p>
        Edit Questionnaire
        <span class="italic text-green-400 mr-1">{{ route.params.id }}</span>
        <span class="lowercase text-sm">(id)</span>
      </p>
    </template>
    <template #content>
      <FormSkeleton />
    </template>
  </FormLayout>
  <FormLayout v-else>
    <template #header>
      <div class="flex flex-wrap justify-center md:justify-start space-x-4 items-center">
        <p class="mb-2">
          Edit Questionnaire
          <span class="italic text-green-400 mr-1">{{ route.params.id }}</span>
          <span class="lowercase text-sm">(id)</span>
        </p>
        <PrimeButton class="p-button-sm h-10" icon="pi pi-refresh" @click="refresh" />
      </div>
    </template>
    <template #content>
      <div class="md:flex md:flex-wrap">
        <!-- name -->
        <div class="md:w-[calc(50%-1rem)] mb-8 md:mr-8">
          <span class="p-float-label">
            <InputText
              id="username"
              v-model="state.name"
              :class="['w-full', { 'p-invalid': v$.name.$invalid }]"
              type="text"
            />
            <label for="username">Name</label>
          </span>
          <!-- Client side errors -->
          <template v-if="v$.name.$invalid">
            <div class="mt-1">
              <p
                v-for="(error, index) in v$.name.$errors"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error.$message.replace('Value', 'Name') }}
              </p>
            </div>
          </template>
          <!-- Server side errors -->
          <template v-if="questionnairesStore.errors.name">
            <p
              v-for="(error, index) in questionnairesStore.errors.name"
              :key="index"
              class="text-red-500 text-sm"
            >
              {{ error }}
            </p>
          </template>
        </div>

        <!-- Difficulty -->
        <div class="md:w-[calc(50%-1rem)] mb-8">
          <span class="p-float-label">
            <Dropdown
              v-model="state.difficulty"
              :options="difficultyOptions"
              :class="['w-full', { 'p-invalid': v$.difficulty.$invalid }]"
              option-label="name"
              placeholder="Select a difficulty"
              class="w-full"
            />
            <label for="difficulty">Difficulty</label>
          </span>

          <!-- Client side errors -->
          <template v-if="v$.difficulty.$invalid">
            <div class="mt-1">
              <p
                v-for="(error, index) in v$.difficulty.$errors"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error.$message.replace('Value', 'Difficulty') }}
                {{ validationErrorMessages[error.$validator] }}
              </p>
            </div>
          </template>
          <!-- Server side errors -->
          <template v-if="questionnairesStore.errors.difficulty">
            <p
              v-for="(error, index) in questionnairesStore.errors.difficulty"
              :key="index"
              class="text-red-500 text-sm"
            >
              {{ error }}
            </p>
          </template>
        </div>

        <!-- Categories -->
        <div class="md:w-[calc(50%-1rem)] mb-8 md:mr-8">
          <span class="p-float-label">
            <MultiSelect
              v-model="state.categories"
              filter
              class="w-full"
              :options="categoriesOptions"
              option-label="name"
              placeholder="Select categories"
            />

            <label for="single_answers_type"> Categories</label>
          </span>

          <!-- Client side errors -->
          <template v-if="v$.categories.$invalid">
            <div class="mt-1">
              <p
                v-for="(error, index) in v$.categories.$errors"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error.$message.replace('Value', 'Caetegory') }}
                {{ validationErrorMessages[error.$validator] }}
              </p>
            </div>
          </template>
          <!-- Server side errors -->
          <template v-if="questionnairesStore.errors.categories">
            <p
              v-for="(error, index) in questionnairesStore.errors.categories"
              :key="index"
              class="text-red-500 text-sm"
            >
              {{ error }}
            </p>
          </template>
        </div>

        <!-- Answers type -->
        <div class="md:w-[calc(50%-1rem)] mb-8">
          <span class="p-float-label">
            <Dropdown
              v-model="state.single_answers_type"
              :options="answersTypeOptions"
              :class="['w-full', { 'p-invalid': v$.single_answers_type.$invalid }]"
              option-label="name"
              placeholder="Select answers type"
              class="w-full"
            />
            <label for="single_answers_type"> Answers type</label>
          </span>

          <!-- Client side errors -->
          <template v-if="v$.single_answers_type.$invalid">
            <div class="mt-1">
              <p
                v-for="(error, index) in v$.single_answers_type.$errors"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error.$message.replace('Value', 'Answers Type') }}
                {{ validationErrorMessages[error.$validator] }}
              </p>
            </div>
          </template>
          <!-- Server side errors -->
          <template v-if="questionnairesStore.errors.single_answers_type">
            <p
              v-for="(error, index) in questionnairesStore.errors.single_answers_type"
              :key="index"
              class="text-red-500 text-sm"
            >
              {{ error }}
            </p>
          </template>
        </div>

        <!-- No of questions -->
        <div class="flex flex-col md:flex-row justify-between w-full flex-wrap">
          <!-- No of easy questions -->
          <div class="mb-8">
            <span class="p-float-label">
              <InputNumber
                v-model="state.no_of_easy_questions"
                input-id="minmax-buttons"
                :class="['w-full', { 'p-invalid': v$.no_of_easy_questions.$invalid }]"
                :use-grouping="false"
                :min="0"
                show-buttons
              />
              <label for="no_of_easy_questions">No of easy questions</label>
            </span>

            <!-- Client side errors -->
            <template v-if="v$.no_of_easy_questions.$invalid">
              <div class="mt-1">
                <p
                  v-for="(error, index) in v$.no_of_easy_questions.$errors"
                  :key="index"
                  class="text-red-500 text-sm"
                >
                  {{ error.$message.replace('Value', 'No of Easy Questions') }}
                </p>
              </div>
            </template>
            <!-- Server side errors -->
            <template v-if="questionnairesStore.errors.no_of_easy_questions">
              <p
                v-for="(error, index) in questionnairesStore.errors.no_of_easy_questions"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error }}
              </p>
            </template>
          </div>

          <!-- No of medium questions -->
          <div class="mb-8">
            <span class="p-float-label">
              <InputNumber
                v-model="state.no_of_medium_questions"
                input-id="minmax-buttons"
                :class="['w-full', { 'p-invalid': v$.no_of_medium_questions.$invalid }]"
                :use-grouping="false"
                :min="0"
                show-buttons
              />
              <label for="no_of_medium_questions">No of Medium questions</label>
            </span>

            <!-- Client side errors -->
            <template v-if="v$.no_of_medium_questions.$invalid">
              <div class="mt-1">
                <p
                  v-for="(error, index) in v$.no_of_medium_questions.$errors"
                  :key="index"
                  class="text-red-500 text-sm"
                >
                  {{ error.$message.replace('Value', 'No of Medium Questions') }}
                </p>
              </div>
            </template>
            <!-- Server side errors -->
            <template v-if="questionnairesStore.errors.no_of_medium_questions">
              <p
                v-for="(error, index) in questionnairesStore.errors.no_of_medium_questions"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error }}
              </p>
            </template>
          </div>

          <!-- No of hard questions -->
          <div class="mb-8">
            <span class="p-float-label">
              <InputNumber
                v-model="state.no_of_hard_questions"
                input-id="minmax-buttons"
                :class="['w-full', { 'p-invalid': v$.no_of_hard_questions.$invalid }]"
                :use-grouping="false"
                :min="0"
                show-buttons
              />
              <label for="no_of_hard_questions">No of Hard questions</label>
            </span>

            <!-- Client side errors -->
            <template v-if="v$.no_of_hard_questions.$invalid">
              <div class="mt-1">
                <p
                  v-for="(error, index) in v$.no_of_hard_questions.$errors"
                  :key="index"
                  class="text-red-500 text-sm"
                >
                  {{ error.$message.replace('Value', 'No of Hard Questions') }}
                </p>
              </div>
            </template>
            <!-- Server side errors -->
            <template v-if="questionnairesStore.errors.no_of_hard_questions">
              <p
                v-for="(error, index) in questionnairesStore.errors.no_of_hard_questions"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error }}
              </p>
            </template>
          </div>

          <!-- No of total questions -->
          <div class="mb-8">
            <span class="p-float-label">
              <InputNumber
                v-model="state.no_of_questions"
                input-id="minmax-buttons"
                :class="['w-full', { 'p-invalid': v$.no_of_questions.$invalid }]"
                :use-grouping="false"
                :min="0"
                show-buttons
              />
              <label for="no_of_questions">No of total questions</label>
            </span>

            <!-- Client side errors -->
            <template v-if="v$.no_of_questions.$invalid">
              <div class="mt-1 w-[15rem]">
                <p
                  v-for="(error, index) in v$.no_of_questions.$errors"
                  :key="index"
                  class="text-red-500 text-sm"
                >
                  {{
                    error.$validator === 'equalTo'
                      ? 'Total no of questions must be equal to sum of easy, medium and hard questions'
                      : ''
                  }}
                  {{ error.$message.replace('Value', 'No of total questions') }}
                </p>
              </div>
            </template>
            <!-- Server side errors -->
            <template v-if="questionnairesStore.errors.no_of_questions">
              <p
                v-for="(error, index) in questionnairesStore.errors.no_of_questions"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error }}
              </p>
            </template>
          </div>
        </div>

        <!-- Allocated Time -->
        <div class="w-full">
          <div class="md:w-[calc(50%-1rem)] mb-8">
            <span class="p-float-label">
              <InputNumber
                v-model="state.allocated_time"
                input-id="minmax-buttons"
                :class="['w-full', { 'p-invalid': v$.allocated_time.$invalid }]"
                :use-grouping="false"
                suffix=" (minutes)"
                :min="0"
                show-buttons
              />
              <label for="no_of_questions">Allocated time</label>
            </span>

            <!-- Client side errors -->
            <template v-if="v$.allocated_time.$invalid">
              <div class="mt-1">
                <p
                  v-for="(error, index) in v$.allocated_time.$errors"
                  :key="index"
                  class="text-red-500 text-sm"
                >
                  {{ error.$message.replace('Value', 'Allocated time') }}
                  {{ validationErrorMessages[error.$validator] }}
                </p>
              </div>
            </template>
            <!-- Server side errors -->
            <template v-if="questionnairesStore.errors.allocated_time">
              <p
                v-for="(error, index) in questionnairesStore.errors.allocated_time"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error }}
              </p>
            </template>
          </div>
        </div>
      </div>

      <div class="flex justify-between md:justify-start !mt-[3rem] md:!mt-[1rem] space-x-8">
        <PrimeButton
          class=""
          :label="questionnairesStore.status === 'updating' ? 'Updating' : 'Update'"
          icon="pi pi-spinner"
          icon-pos="right"
          :disabled="v$.$invalid && updateQuestionnaireButtonClicked"
          :loading="questionnairesStore.status === 'updating'"
          @click="updateQuestionnaire"
        />
        <PrimeButton
          class="p-button-warning"
          label="Clear"
          icon="pi pi-times"
          icon-pos="right"
          @click="clearState"
        />
      </div>
    </template>
  </FormLayout>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue'

import { useRoute } from 'vue-router'

import { useQuestionnairesStore } from '@/stores/questionnaires'
import { useCategoriesStore } from '@/stores/categories'

import Dropdown from 'primevue/dropdown'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'
import MultiSelect from 'primevue/multiselect'
import PrimeButton from 'primevue/button'

import { useVuelidate } from '@vuelidate/core'
import { required, minLength, maxLength } from '@vuelidate/validators'

import FormLayout from '@/views/layouts/FormLayout.vue'
import FormSkeleton from '@/components/skeletons/FormSkeleton.vue'

import { messages as validationErrorMessages } from '@/validationRules'
import { findRelations } from '@/helpers'

export default {
  components: {
    Dropdown,
    FormLayout,
    InputText,
    PrimeButton,
    InputNumber,
    MultiSelect,
    FormSkeleton
  },
  setup() {
    const questionnairesStore = useQuestionnairesStore()
    const categoriesStore = useCategoriesStore()

    const route = useRoute()

    const initialState = {
      name: '',
      difficulty: '',
      categories: [],
      single_answers_type: '',
      no_of_easy_questions: null,
      no_of_medium_questions: null,
      no_of_hard_questions: null,
      no_of_questions: null,
      allocated_time: null
    }

    const state = reactive(JSON.parse(JSON.stringify(initialState)))

    const updateQuestionnaireButtonClicked = ref(false)

    const difficultyOptions = [
      { name: 'Easy', value: 1 },
      { name: 'Medium', value: 2 },
      { name: 'Hard', value: 3 }
    ]

    const answersTypeOptions = [
      { name: 'Single', value: 'true' },
      { name: 'Multiple', value: 'false' }
    ]

    const categoriesOptions = ref([])

    const equalToRule = (value) => {
      let total =
        state.no_of_easy_questions + state.no_of_medium_questions + state.no_of_hard_questions

      return value === total || value === null
    }

    const rules = {
      name: { required, minLength: minLength(3), maxLength: maxLength(50) },
      categories: { required },
      difficulty: { required },
      single_answers_type: { required },
      no_of_easy_questions: { required },
      no_of_medium_questions: { required },
      no_of_hard_questions: { required },
      no_of_questions: {
        required,
        equalTo: equalToRule
      },
      allocated_time: { required }
    }

    const v$ = useVuelidate(rules, state, { $autoDirty: true, $lazy: true })

    onMounted(() => {
      loadData()
    })

    watch(
      () => categoriesStore.categories,
      (newCategories) => {
        if (newCategories) {
          newCategories.data.forEach((category) => {
            categoriesOptions.value.push({
              name: category.attributes.name,
              value: category.id
            })
          })
        }
      }
    )

    watch(
      () => questionnairesStore.questionnaire,
      (newQuestionnaire) => {
        if (newQuestionnaire) {
          state.name = newQuestionnaire.data.attributes.name
          state.no_of_easy_questions = newQuestionnaire.data.attributes.no_of_easy_questions
          state.no_of_medium_questions = newQuestionnaire.data.attributes.no_of_medium_questions
          state.no_of_hard_questions = newQuestionnaire.data.attributes.no_of_hard_questions
          state.no_of_questions = newQuestionnaire.data.attributes.no_of_questions
          state.allocated_time = newQuestionnaire.data.attributes.allocated_time

          let relations = newQuestionnaire.included

          setDiffculty(newQuestionnaire)
          setCategories(newQuestionnaire, relations)
          setAnswersType(newQuestionnaire)
        }
      }
    )

    function setCategories(newQuestionnaire, relations) {
      newQuestionnaire.data.relationships.categories.data.forEach((category) => {
        let categoryRelation = findRelations(relations, category.id, category.type)

        state.categories.push({
          value: categoryRelation.id,
          name: categoryRelation.attributes.name
        })
      })
    }

    function setDiffculty(newQuestionnaire) {
      for (let difficulty of difficultyOptions) {
        if (newQuestionnaire.data.attributes.difficulty === difficulty.name.toUpperCase()) {
          state.difficulty = difficulty
          return
        }
      }
    }

    function setAnswersType(newQuestionnaire) {
      for (let answerType of answersTypeOptions) {
        if (answerType.name === 'Single' && newQuestionnaire.data.attributes.single_answers_type) {
          state.single_answers_type = answerType
          return
        }

        state.single_answers_type = answerType
      }
    }

    function prepareFormData() {
      let difficultyValue = state.difficulty.value
      let selectedCategories = []
      let single_answers_type = state.single_answer_type === 'true' ? true : false

      state.categories.forEach((category) => {
        selectedCategories.push(category.value)
      })

      return {
        ...state,
        categories: selectedCategories,
        single_answers_type,
        difficulty: difficultyValue
      }
    }

    function updateQuestionnaire() {
      updateQuestionnaireButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      questionnairesStore.editQuestionnaire(route.params.id, prepareFormData())
    }

    function clearState() {
      Object.assign(state, JSON.parse(JSON.stringify(initialState)))

      v$.value.$reset()

      questionnairesStore.errors = {}
    }

    function refresh() {
      loadData()
    }

    function loadData() {
      categoriesStore.getAll()
      questionnairesStore.getOne(route.params.id, {
        query: { includes: ['categories'] }
      })
    }

    return {
      state,
      route,
      difficultyOptions,
      answersTypeOptions,
      categoriesOptions,
      v$,
      validationErrorMessages,
      updateQuestionnaire,
      clearState,
      updateQuestionnaireButtonClicked,
      questionnairesStore,
      refresh
    }
  }
}
</script>
