<template>
  <Dialog
    v-model:visible="show"
    modal
    header="Setup Complete"
    :style="{ width: '50rem' }"
    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
  >
    <Message>
      <div class="flex items-center space-x-4 text-xl font-bold">
        <span>Setup has completed successfully. Enjoy the evaluator!</span>
        <i class="pi pi-check-circle text-green-500 !text-2xl" />
      </div>
    </Message>
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
