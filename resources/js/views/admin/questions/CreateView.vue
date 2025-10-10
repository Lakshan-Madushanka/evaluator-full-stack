<template>
  <FormLayout>
    <template #header> Create Question </template>
    <template #content>
      <div class="md:flex md:flex-wrap">
        <!-- Difficulty -->
        <div class="md:w-[calc(50%-1rem)] mb-8 md:mr-8">
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
          <template v-if="questionsStore.errors.difficulty">
            <p
              v-for="(error, index) in questionsStore.errors.difficulty"
              :key="index"
              class="text-red-500 text-sm"
            >
              {{ error }}
            </p>
          </template>
        </div>

        <!-- Categories -->
        <div class="md:w-[calc(50%-1rem)] mb-8">
          <span class="p-float-label">
            <MultiSelect
              v-model="state.categories"
              filter
              class="w-full"
              :options="categoriesOptions"
              option-label="name"
              placeholder="Select categories"
            />

            <label for="is_answers_type_single"> Categories</label>
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
          <template v-if="questionsStore.errors.categories">
            <p
              v-for="(error, index) in questionsStore.errors.categories"
              :key="index"
              class="text-red-500 text-sm"
            >
              {{ error }}
            </p>
          </template>
        </div>

        <!-- Answers type -->
        <div class="md:w-[calc(50%-1rem)] mb-8 md:mr-8">
          <span class="p-float-label">
            <Dropdown
              v-model="state.is_answers_type_single"
              :options="answersTypeOptions"
              :class="['w-full', { 'p-invalid': v$.is_answers_type_single.$invalid }]"
              option-label="name"
              option-value="value"
              placeholder="Select answers type"
              class="w-full"
            />
            <label for="is_answers_type_single"> Answers type</label>
          </span>

          <!-- Client side errors -->
          <template v-if="v$.is_answers_type_single.$invalid">
            <div class="mt-1">
              <p
                v-for="(error, index) in v$.is_answers_type_single.$errors"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error.$message.replace('Value', 'Answers Type') }}
                {{ validationErrorMessages[error.$validator] }}
              </p>
            </div>
          </template>
          <!-- Server side errors -->
          <template v-if="questionsStore.errors.is_answers_type_single">
            <p
              v-for="(error, index) in questionsStore.errors.is_answers_type_single"
              :key="index"
              class="text-red-500 text-sm"
            >
              {{ error }}
            </p>
          </template>
        </div>

        <!-- No of total questions -->
        <div class="mb-8 md:w-[calc(50%-1rem)]">
          <span class="p-float-label">
            <InputNumber
              v-model="state.no_of_answers"
              input-id="minmax-buttons"
              :class="['w-full', { 'p-invalid': v$.no_of_answers.$invalid }]"
              :use-grouping="false"
              :min="2"
              :max="5"
              show-buttons
            />
            <label for="no_of_answers">No of Answers</label>
          </span>

          <!-- Client side errors -->
          <template v-if="v$.no_of_answers.$invalid">
            <div class="mt-1 w-[15rem]">
              <p
                v-for="(error, index) in v$.no_of_answers.$errors"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error.$message.replace('Value', 'No of Answers') }}
              </p>
            </div>
          </template>
          <!-- Server side errors -->
          <template v-if="questionsStore.errors.no_of_answers">
            <p
              v-for="(error, index) in questionsStore.errors.no_of_answers"
              :key="index"
              class="text-red-500 text-sm"
            >
              {{ error }}
            </p>
          </template>
        </div>

        <!-- Content -->
        <div class="mb-8 w-full">
          <!-- <label class="mb-1 block" for="question_content">Content</label> -->
          <TextEditor
            id="question_content"
            v-model="state.text"
            editor-style="height: 320px"
            placeholder="Content"
          />

          <!-- Client side errors -->
          <template v-if="v$.text.$invalid">
            <div class="mt-1">
              <p
                v-for="(error, index) in v$.text.$errors"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error.$message.replace('Value', 'Text') }}
              </p>
            </div>
          </template>
          <!-- Server side errors -->
          <template v-if="questionsStore.errors.text">
            <p
              v-for="(error, index) in questionsStore.errors.text"
              :key="index"
              class="text-red-500 text-sm"
            >
              {{ error }}
            </p>
          </template>
        </div>
      </div>

      <div class="flex justify-between md:justify-start !mt-[3rem] md:!mt-[1rem] space-x-8">
        <PrimeButton
          class=""
          :label="questionsStore.status === 'creating' ? 'Creating' : 'Create'"
          icon="pi pi-plus"
          icon-pos="right"
          :disabled="v$.$invalid && createQuestionButtonClicked"
          :loading="questionsStore.status === 'creating'"
          @click="createQuestion"
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

import { useQuestionsStore } from '@/stores/questions'
import { useCategoriesStore } from '@/stores/categories'

import Dropdown from 'primevue/dropdown'
import InputNumber from 'primevue/inputnumber'
import MultiSelect from 'primevue/multiselect'
import PrimeButton from 'primevue/button'

import { useVuelidate } from '@vuelidate/core'
import { required, minLength } from '@vuelidate/validators'

import FormLayout from '@/views/layouts/FormLayout.vue'
import TextEditor from '@/components/form/textEditors/DefaultTextEditor.vue'

import { exists as existsRule, messages as validationErrorMessages } from '@/validationRules'

export default {
  components: {
    Dropdown,
    FormLayout,
    InputNumber,
    PrimeButton,
    MultiSelect,
    TextEditor
  },
  setup() {
    const questionsStore = useQuestionsStore()
    const categoriesStore = useCategoriesStore()

    const initialState = {
      text: '',
      difficulty: '',
      categories: [],
      is_answers_type_single: '',
      no_of_answers: null
    }

    const state = reactive({
      ...initialState
    })

    const createQuestionButtonClicked = ref(false)

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

    const rules = {
      text: { required, minLength: minLength(3) },
      categories: { required },
      difficulty: { required, exists: existsRule([1, 2, 3]) },
      no_of_answers: { required },
      is_answers_type_single: {
        required,
        exists: existsRule(['true', 'false'])
      }
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
      let is_answers_type_single = state.is_answers_type_single === 'true' ? true : false

      state.categories.forEach((category) => {
        selectedCategories.push(category.value)
      })

      return {
        ...state,
        categories: selectedCategories,
        is_answers_type_single
      }
    }

    function createQuestion() {
      createQuestionButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      questionsStore.createQuestion(prepareFormData())
    }

    function clearState() {
      Object.assign(state, { ...initialState })

      v$.value.$reset()

      questionsStore.errors = {}
    }

    return {
      state,
      difficultyOptions,
      answersTypeOptions,
      categoriesOptions,
      v$,
      validationErrorMessages,
      createQuestion,
      clearState,
      createQuestionButtonClicked,
      questionsStore
    }
  }
}
</script>
