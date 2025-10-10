<template>
  <FormLayout v-if="answersStore.loading">
    <template #header>
      <p>
        Edit Answer
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
          Edit Answer
          <span class="italic text-green-400 mr-1">{{ route.params.id }}</span>
          <span class="lowercase text-sm">(id)</span>
        </p>
        <PrimeButton class="p-button-sm h-10" icon="pi pi-refresh" @click="refresh" />
      </div>
    </template>
    <template #content>
      <div class="md:flex md:flex-wrap">
        <!-- Content -->
        <div class="mb-8 w-full">
          <!-- <label class="mb-1 block" for="answer_content">Content</label> -->
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
          :label="answersStore.status === 'updating' ? 'Updating' : 'Update'"
          icon="pi pi-spinner"
          icon-pos="right"
          :disabled="v$.$invalid && updateAnswerButtonClicked"
          :loading="answersStore.status === 'updating'"
          @click="updateAnswer"
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

import { useAnswersStore } from '@/stores/answers'

import PrimeButton from 'primevue/button'

import { useVuelidate } from '@vuelidate/core'
import { required, minLength } from '@vuelidate/validators'

import FormLayout from '@/views/layouts/FormLayout.vue'
import FormSkeleton from '@/components/skeletons/FormSkeleton.vue'
import TextEditor from '@/components/form/textEditors/DefaultTextEditor.vue'

import { messages as validationErrorMessages } from '@/validationRules'

export default {
  components: {
    FormLayout,
    PrimeButton,
    FormSkeleton,
    TextEditor
  },
  setup() {
    const answersStore = useAnswersStore()

    const route = useRoute()

    const initialState = {
      text: ''
    }

    const state = reactive(JSON.parse(JSON.stringify(initialState)))

    const updateAnswerButtonClicked = ref(false)

    const rules = {
      text: { required, minLength: minLength(3) }
    }

    const v$ = useVuelidate(rules, state, { $autoDirty: true, $lazy: true })

    onMounted(() => {
      loadData()
    })

    watch(
      () => answersStore.answer,
      (newAnswer) => {
        if (newAnswer) {
          state.text = newAnswer.data.attributes.text
        }
      }
    )

    function updateAnswer() {
      updateAnswerButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      answersStore.editAnswer(route.params.id, state)
    }

    function clearState() {
      Object.assign(state, JSON.parse(JSON.stringify(initialState)))

      v$.value.$reset()

      answersStore.errors = {}
    }

    function refresh() {
      loadData()
    }

    function loadData() {
      answersStore.getOne(route.params.id)
    }

    return {
      state,
      route,
      v$,
      validationErrorMessages,
      updateAnswer,
      clearState,
      updateAnswerButtonClicked,
      answersStore,
      refresh
    }
  }
}
</script>
