<template>
  <FormLayout>
    <template #header> Create Questionnaire </template>
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
              option-value="value"
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

            <label for="categories"> Categories</label>
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
              option-value="value"
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
          :label="questionnairesStore.status === 'creating' ? 'Creating' : 'Create'"
          icon="pi pi-plus"
          icon-pos="right"
          :disabled="v$.$invalid && createQuestionnaireButtonClicked"
          :loading="questionnairesStore.status === 'creating'"
          @click="createQuestionnaire"
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

import { exists as existsRule, messages as validationErrorMessages } from '@/validationRules'

export default {
  components: {
    Dropdown,
    FormLayout,
    InputText,
    PrimeButton,
    InputNumber,
    MultiSelect
  },
  setup() {
    const questionnairesStore = useQuestionnairesStore()
    const categoriesStore = useCategoriesStore()

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

    const state = reactive({
      ...initialState
    })

    const createQuestionnaireButtonClicked = ref(false)

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
      difficulty: { required, exists: existsRule([1, 2, 3]) },
      single_answers_type: { required, exists: existsRule(['true', 'false']) },
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
      categoriesStore.getAll()
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

    function prepareFormData() {
      let selectedCategories = []
      let single_answers_type = state.single_answers_type === 'true' ? true : false

      state.categories.forEach((category) => {
        selectedCategories.push(category.value)
      })

      return { ...state, categories: selectedCategories, single_answers_type }
    }

    function createQuestionnaire() {
      createQuestionnaireButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      questionnairesStore.createQuestionnaire(prepareFormData())
    }

    function clearState() {
      Object.assign(state, { ...initialState })

      v$.value.$reset()

      questionnairesStore.errors = {}
    }

    return {
      state,
      difficultyOptions,
      answersTypeOptions,
      categoriesOptions,
      v$,
      validationErrorMessages,
      createQuestionnaire,
      clearState,
      createQuestionnaireButtonClicked,
      questionnairesStore
    }
  }
}
</script>
