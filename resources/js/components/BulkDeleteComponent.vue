<template>
  <PrimeDialog
    v-model:visible="display"
    class="w-[90%] lg:w-[60%]"
    modal
    @after-hide="afterDialogHidden"
  >
    <template #header>
      <div class="flex justify-center items-center w-full">
        <p class="text-2xl font-bold text-red-600 text-center">
          <i class="pi pi-exclamation-triangle !text-2xl mr-2"></i> Delete Confirmation
        </p>
      </div>
    </template>
    <div class="w-full flex justify-center items-center flex-col">
      <div>
        <p class="text-lg font-bold text-center">
          Your cannot recover this record(s) after deletion.
        </p>
      </div>
      <div class="mt-4 w-full flex flex-col justify-center items-center">
        <p class="mb-1 text-center">Please write below text to confirm deletion</p>
        <p class="font-bold mb-4">{{ props.value }}</p>
        <InputText v-model="confirmText" class="w-2/3" />
      </div>
      <div class="mt-6 w-full flex justify-center">
        <PrimeButton
          class="!mr-4"
          label="Cancel"
          icon="pi pi-times"
          icon-pos="right"
          @click="display = false"
        />
        <PrimeButton
          class="p-button-danger"
          label="Delete record(s)"
          icon="pi pi-trash"
          icon-pos="right"
          :disabled="confirmText !== props.value"
          :loading="props.status === 'deleting'"
          @click="bulkDeleteConfirmed"
        />
      </div>
    </div>
  </PrimeDialog>
</template>

<script>
import { ref, watch } from 'vue'

import PrimeDialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import PrimeButton from 'primevue/button'

export default {
  components: { PrimeDialog, InputText, PrimeButton },
  props: {
    displayComponent: { type: Boolean, default: false },
    value: { type: String, required: true },
    status: { type: String, default: '' }
  },
  emits: ['onDialogHidden', 'bulkDeleteConfirmed'],
  setup(props, { emit }) {
    const display = ref(false)
    const confirmText = ref('')

    watch(
      () => props.displayComponent,
      (displayComponent) => {
        display.value = displayComponent
      }
    )

    function afterDialogHidden() {
      emit('onDialogHidden', display.value)
    }

    function bulkDeleteConfirmed() {
      emit('bulkDeleteConfirmed')
      confirmText.value = ''
    }

    return {
      props,
      display,
      confirmText,
      afterDialogHidden,
      bulkDeleteConfirmed
    }
  }
}
</script>
