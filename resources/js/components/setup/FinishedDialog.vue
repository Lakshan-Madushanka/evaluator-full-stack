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
    <template #header>
      <div class="flex justify-between items-start w-full">
        <span class="text-xl font-bold">Setup Complete</span>
        <PrimeButton
          v-if="setupStore.status === 'incompleted'"
          @click="runFinalizeCommands()"
          icon="pi pi-refresh"
          title="Refresh"
          :disabled="setupStore.data.account.loading"
        />
      </div>
    </template>
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

    <div v-if="setupStore.status === 'incompleted'" class="space-y-8">
      <p
        v-if="setupStore.data.symlink.status === 'creating'"
        class="text-xl flex items-center gap-4"
      >
        <span>Creating storage link</span> <i class="pi pi-spin pi-cog" style="font-size: 2rem"></i>
      </p>
      <Message v-if="setupStore.data.symlink.status === 'created'" severity="success">
        <span class="text-lg">Storage link created successfully! </span>
      </Message>
      <Message v-if="setupStore.data.symlink.status === 'error'" severity="error">
        <span class="flex flex-col space-y-4 items-start">
          <span>
            Error occurred while creating storage link!. Please run the following command in the
            terminal from the project's (site's) root directory.
          </span>
          <Badge severity="info">php artisan storage:link</Badge>
          <span class="flex items-center gap-2">
            <i class="pi pi-exclamation-circle !text-2xl" />
            <span class="text-lg">Uploaded images won't display without storage link.</span>
          </span>
        </span>
      </Message>

      <p
        v-if="setupStore.data.optimize.status === 'optimizing'"
        class="text-xl flex items-center gap-4"
      >
        <span>Optimizing</span> <i class="pi pi-spin pi-cog" style="font-size: 2rem"></i>
      </p>
      <Message v-if="setupStore.data.optimize.status === 'optimized'" severity="success">
        <span class="text-lg">App optimized successfully! </span>
      </Message>
      <Message v-if="setupStore.data.optimize.status === 'error'" severity="error">
        <span class="flex flex-col space-y-4 items-start">
          <span>
            Error occurred while optimizing app!. Please run the following command in the terminal
            from the project's (site's) root directory.
          </span>
          <Badge severity="info">php artisan optimize</Badge>
          <span class="flex items-center gap-2">
            <i class="pi pi-exclamation-circle !text-2xl" />
            <span class="text-lg"
              >It is crucial to run this command to improve site's overall performance.</span
            >
          </span>
        </span>
      </Message>

      <div
        v-if="
          !setupStore.data.env.keyStatus &&
          (setupStore.data.optimize.status === 'optimized' ||
            setupStore.data.optimize.status === 'error')
        "
        class="space-y-6"
      >
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

    <div v-if="setupStore.data.env.keyStatus === 'error'" class="space-y-6 mt-8">
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
  async (isVisible) => {
    if (isVisible) {
      show.value = true
      if (setupStore.status !== 'completed') {
        runFinalizeCommands()
      }
    }
  }
)

watch(show, (showShow) => {
  if (!showShow) {
    refreshPage()
  }
})

async function runFinalizeCommands() {
  await setupStore.createSymlink()
  setupStore.optimize()
}

function refreshPage() {
  window.location.href = '/'
}
</script>
