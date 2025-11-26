<template>
  <div>
    <PrimeDialog
      v-model:visible="showEvaluation"
      modal
      position="top"
      :draggable="false"
      class="xl:w-1/2"
      @hide="onModalHide"
    >
      <template #header>
        <p
          v-if="candidatesQuestionnairesStore.status === 'evaluating'"
          class="text-2xl font-bold w-full text-center"
        >
          Evaluating
        </p>
        <p v-else class="text-2xl font-bold w-full text-center">Evaluation</p>
      </template>
      <div v-if="candidatesQuestionnairesStore.status === 'evaluated'">
        <div class="flex justify-center">
          <Knob v-model="evaluation.marks_percentage" value-template="{value}%" />
        </div>

        <div class="flex justify-center">
          <ul class="w-[26rem]">
            <li
              class="w-full border-b-2 flex justify-between border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50"
            >
              <span>Submission type</span>
              <Tag v-if="props.submissionType === 'USER_SUBMISSION'">
                {{ props.submissionType }}</Tag
              >
              <Tag v-else severity="danger"> {{ props.submissionType }}</Tag>
            </li>
            <li
              class="w-full border-b-2 flex justify-between border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50"
            >
              <span>Total points</span>
              <Tag
                >{{ evaluation.total_points_earned }} / {{ evaluation.total_points_allocated }}</Tag
              >
            </li>
            <li
              class="w-full border-b-2 flex justify-between border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50"
            >
              <span>No of answered questions</span>
              <Tag>{{ evaluation.no_of_answered_questions }}</Tag>
            </li>
            <li
              class="w-full border-b-2 flex justify-between border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50"
            >
              <span>No of skipped questions</span>
              <Tag v-if="getSkippedQuestionsCount() === 0">{{ getSkippedQuestionsCount() }}</Tag>
              <Tag v-else severity="warn">{{ getSkippedQuestionsCount() }}</Tag>
            </li>
            <li
              class="w-full border-b-2 flex justify-between border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50"
            >
              <span>No of correct answers</span>
              <Tag>{{ evaluation.no_of_correct_answers }}</Tag>
            </li>
            <li
              class="w-full border-b-2 flex justify-between border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50"
            >
              <span>Time taken</span>
              <Tag>{{
                evaluation.time_taken < 1 ? '<1min' : formatMinutes(evaluation.time_taken)
              }}</Tag>
            </li>
          </ul>
        </div>
      </div>
      <div v-else class="flex justify-center">
        <ProgressSpinner />
      </div>
    </PrimeDialog>
  </div>
</template>

<script>
import { computed, ref } from 'vue'

import PrimeDialog from 'primevue/dialog'
import Knob from 'primevue/knob'
import ProgressSpinner from 'primevue/progressspinner'
import Tag from 'primevue/tag'

import { useCandidatesQuestionnairesStore } from '@/stores/candidates/questionnaires'

import { formatMinutes } from '@/helpers'

export default {
  components: { PrimeDialog, Knob, ProgressSpinner, Tag },

  props: {
    submissionType: {
      type: String,
      required: true
    },
    totalQuestions: {
      type: Number,
      required: true
    }
  },
  setup(props) {
    const candidatesQuestionnairesStore = useCandidatesQuestionnairesStore()

    const showEvaluation = ref(true)

    function onModalHide() {
      window.location.href = '/'
    }

    function getSkippedQuestionsCount() {
      return (
        props.totalQuestions -
        candidatesQuestionnairesStore.evaluation.attributes.no_of_answered_questions
      )
    }

    return {
      props,
      showEvaluation,
      candidatesQuestionnairesStore,
      evaluation: computed(() => candidatesQuestionnairesStore.evaluation?.attributes),
      onModalHide,
      formatMinutes,
      getSkippedQuestionsCount
    }
  }
}
</script>
