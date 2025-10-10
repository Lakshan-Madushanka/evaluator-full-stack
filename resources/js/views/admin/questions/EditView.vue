<template>
  <FormLayout v-if="questionsStore.loading">
    <template #header>
      <p>
        Edit Question
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
          Edit Question
          <span class="italic text-green-400 mr-1">{{ route.params.id }}</span>
          <span class="lowercase text-sm">(id)</span>
        </p>
        <PrimeButton class="p-button-sm h-10" icon="pi pi-refresh" @click="refresh" />
      </div>
    </template>
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

        <!-- No of answers -->
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
          :label="questionsStore.status === 'updating' ? 'Updating' : 'Update'"
          icon="pi pi-spinner"
          icon-pos="right"
          :disabled="v$.$invalid && updateQuestionButtonClicked"
          :loading="questionsStore.status === 'updating'"
          @click="updateQuestion"
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

import { useQuestionsStore } from '@/stores/questions'
import { useCategoriesStore } from '@/stores/categories'

import Dropdown from 'primevue/dropdown'
import InputNumber from 'primevue/inputnumber'
import MultiSelect from 'primevue/multiselect'
import PrimeButton from 'primevue/button'

import { useVuelidate } from '@vuelidate/core'
import { required, minLength } from '@vuelidate/validators'

import FormLayout from '@/views/layouts/FormLayout.vue'
import FormSkeleton from '@/components/skeletons/FormSkeleton.vue'
import TextEditor from '@/components/form/textEditors/DefaultTextEditor.vue'

import { findRelations } from '@/helpers'

import { messages as validationErrorMessages } from '@/validationRules'

export default {
  components: {
    Dropdown,
    FormLayout,
    PrimeButton,
    InputNumber,
    MultiSelect,
    FormSkeleton,
    TextEditor
  },
  setup() {
    const questionsStore = useQuestionsStore()
    const categoriesStore = useCategoriesStore()

    const route = useRoute()

    const initialState = {
      text: '',
      difficulty: '',
      categories: [],
      is_answers_type_single: '',
      no_of_answers: null
    }

    const state = reactive(JSON.parse(JSON.stringify(initialState)))

    const updateQuestionButtonClicked = ref(false)

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
      difficulty: { required },
      no_of_answers: { required },
      is_answers_type_single: {
        required
      }
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
      () => questionsStore.question,
      (newQuestion) => {
        if (newQuestion) {
          state.text = newQuestion.data.attributes.content
          state.no_of_answers = newQuestion.data.attributes.no_of_answers

          let relations = newQuestion.included

          setDiffculty(newQuestion)
          setCategories(newQuestion, relations)
          setAnswersType(newQuestion)
        }
      }
    )

    function setCategories(newQuestion, relations) {
      newQuestion.data.relationships.categories.data.forEach((category) => {
        let categoryRelation = findRelations(relations, category.id, category.type)

        state.categories.push({
          value: categoryRelation.id,
          name: categoryRelation.attributes.name
        })
      })
    }

    function setDiffculty(newQuestion) {
      for (let difficulty of difficultyOptions) {
        if (newQuestion.data.attributes.hardness === difficulty.name.toUpperCase()) {
          state.difficulty = difficulty
          return
        }
      }
    }

    function setAnswersType(newQuestion) {
      for (let answerType of answersTypeOptions) {
        if (answerType.name === 'Single' && newQuestion.data.attributes.answers_type_single) {
          state.is_answers_type_single = answerType
          return
        }

        state.is_answers_type_single = answerType
      }
    }

    function prepareFormData() {
      let difficultyValue = state.difficulty.value
      let selectedCategories = []
      let is_answers_type_single = state.is_answer_type_single === 'true' ? true : false

      state.categories.forEach((category) => {
        selectedCategories.push(category.value)
      })

      return {
        ...state,
        categories: selectedCategories,
        is_answers_type_single,
        difficulty: difficultyValue
      }
    }

    function updateQuestion() {
      updateQuestionButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      questionsStore.editQuestion(route.params.id, prepareFormData())
    }

    function clearState() {
      Object.assign(state, JSON.parse(JSON.stringify(initialState)))

      v$.value.$reset()

      questionsStore.errors = {}
    }

    function refresh() {
      loadData()
    }

    function loadData() {
      categoriesStore.getAll()
      questionsStore.getOne(route.params.id, {
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
      updateQuestion,
      clearState,
      updateQuestionButtonClicked,
      questionsStore,
      refresh
    }
  }
}
</script>
