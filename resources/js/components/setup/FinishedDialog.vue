<template>
  <Dialog
    v-model:visible="show"
    modal
    header="Setup Complete"
    :style="{ width: '50rem' }"
    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    :draggable="false"
  >
    <div class="flex flex-col gap-8 w-full justify-center items-center space-4 text-xl font-bold">
      <i class="pi pi-check-circle text-green-500 !text-[4rem]" />
      <Message severity="success">
        <span class="flex flex-col w-full justify-center items-center gap-2">
          <span class="text-2xl">Setup has completed successfully. </span>
          <span class="text-xl">Enjoy the evaluator!</span>
        </span>
      </Message>
    </div>
    <PrimeButton @click="refreshPage" class="mt-6" label="Refresh" />
  </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue'

import Dialog from 'primevue/dialog'
import PrimeButton from 'primevue/button'
import Message from 'primevue/message'

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
