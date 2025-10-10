<template>
  <FormLayout>
    <template #header> Create Answer </template>
    <template #content>
      <div class="md:flex md:flex-wrap">
        <!-- Content -->
        <div class="mb-8 w-full">
          <!-- <label class="mb-1 block" for="answer_content">Content</label> -->
          <p class="mb-4">Content</p>
          <TextEditor
            id="answer_content"
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
          <template v-if="answersStore.errors.text">
            <p
              v-for="(error, index) in answersStore.errors.text"
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
          :label="answersStore.status === 'creating' ? 'Creating' : 'Create'"
          icon="pi pi-plus"
          icon-pos="right"
          :disabled="v$.$invalid && createAnswerButtonClicked"
          :loading="answersStore.status === 'creating'"
          @click="createAnswer"
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
import { ref, reactive } from 'vue'

import { useAnswersStore } from '@/stores/answers'

import { useVuelidate } from '@vuelidate/core'
import { required, minLength } from '@vuelidate/validators'

import PrimeButton from 'primevue/button'
import FormLayout from '@/views/layouts/FormLayout.vue'
import TextEditor from '@/components/form/textEditors/DefaultTextEditor.vue'

export default {
  components: {
    FormLayout,
    PrimeButton,
    TextEditor
  },
  setup() {
    const answersStore = useAnswersStore()

    const initialState = {
      text: ''
    }

    const state = reactive({
      ...initialState
    })

    const createAnswerButtonClicked = ref(false)

    const rules = {
      text: { required, minLength: minLength(3) }
    }

    const v$ = useVuelidate(rules, state, { $autoDirty: true, $lazy: true })

    function createAnswer() {
      createAnswerButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      answersStore.createAnswer(state)
    }

    function clearState() {
      Object.assign(state, { ...initialState })

      v$.value.$reset()

      answersStore.errors = {}
    }

    return {
      state,
      v$,
      createAnswer,
      clearState,
      createAnswerButtonClicked,
      answersStore
    }
  }
}
</script>
