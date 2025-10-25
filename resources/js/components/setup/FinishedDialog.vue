<template>
  <Dialog
    v-model:visible="show"
    modal
    header="Setup Complete"
    :style="{ width: '50rem' }"
    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    :draggable="false"
    :closable="false"
    :closeOnEscape="false"
  >
    <div
      v-if="setupStore.data.env.keyStatus === 'generated' || setupStore.status === 'completed'"
      class="flex flex-col gap-8 w-full justify-center items-center space-4 text-xl font-bold"
    >
      <i class="pi pi-check-circle text-green-500 !text-[4rem]" />
      <Message severity="success">
        <span class="flex flex-col w-full justify-center items-center gap-2">
          <span class="text-2xl">Setup has completed successfully. </span>
          <span class="text-xl">Enjoy the evaluator!</span>
        </span>
      </Message>
      <PrimeButton @click="refreshPage" label="Refresh" />
    </div>

    <div v-if="setupStore.status === 'incompleted'">
      <div v-if="!setupStore.data.env.keyStatus" class="space-y-6">
        <p class="text-xl">Setup is almost completed</p>
        <Message class="info">
          <span class="text-lg"
            >Please click following button to generate app key and finalize the setup.
          </span>
        </Message>
        <PrimeButton
          @click="setupStore.generateKey"
          label="Generate key & Finalize"
          :loading="setupStore.data.env.keyStatus === 'migrating'"
        />
      </div>
    </div>

    <div v-if="setupStore.data.env.keyStatus === 'error'" class="space-y-6">
      <Message severity="error">
        <span class="flex flex-col space-y-4 items-start">
          <span>
            Could not generate the app key. Please run the following command in the terminal from
            the project's root directory.
          </span>
          <Badge severity="info">php artisan key:generate</Badge>
          <span class="flex items-center gap-2">
            <i class="pi pi-exclamation-circle !text-2xl" />
            <span class="text-lg"
              >It is crucial to run this command to ensure overall site security.</span
            >
          </span>
        </span>
      </Message>
      <PrimeButton @click="refreshPage" label="Refresh" />
    </div>
  </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue'

import Badge from 'primevue/badge'
import Dialog from 'primevue/dialog'
import PrimeButton from 'primevue/button'
import Message from 'primevue/message'

import { useSetupStore } from '@/stores/setup'

const setupStore = useSetupStore()

const props = defineProps({
  visible: {
    type: Boolean,
    required: true
  }
})

const show = ref(false)

watch(
  () => props.visible,
  function (isVisible) {
    if (isVisible) {
      show.value = true
    }
  }
)

watch(show, (showShow) => {
  if (!showShow) {
    refreshPage()
  }
})

function refreshPage() {
  window.location.href = '/'
}
</script>
