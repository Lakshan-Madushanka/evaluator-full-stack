<template>
  <div>
    <div class="flex justify-between items-start">
      <p class="text-xl mb-4 font-bold flex items-center gap-4">
        <span>Env(.env) Check</span>
        <i
          v-if="setupStore.data.env.loading"
          class="pi pi-spin pi-spinner text-green-600 !text-2xl"
        ></i>
        <i
          v-else-if="setupStore.data.env.is_passed"
          class="pi pi-check-circle text-green-600 !text-2xl"
        ></i>
        <i v-else class="pi pi-times-circle text-red-600 !text-2xl"></i>
      </p>

      <PrimeButton
        @click="loadEnv()"
        icon="pi pi-refresh"
        title="Refresh"
        :disabled="setupStore.data.env.loading"
      />
    </div>
    <ProgressSpinner v-if="setupStore.data.env.loading" />

    <div v-else class="my-8">
      <Message v-if="!setupStore.data.env.is_exists" severity="error">
        The <Badge severity="secondary">.env</Badge> file does not exist. Please create and set it
        up before continuing.
      </Message>

      <div v-else class="space-y-6">
        <div>
          <p class="text-xl font-bold mb-4">App Info</p>
          <Message class="mb-6">
            These values are used throughout the application. Please review and update them if they
            are incorrect.
          </Message>
          <table class="table-auto w-full text-left min-w-max text-slate-800 dark:text-white">
            <thead>
              <tr
                class="text-slate-500 dark:text-white dark:border-slate-950 border-b border-slate-300 bg-slate-50 dark:bg-slate-900"
              >
                <th class="p-4">Key</th>
                <th class="p-4">Value</th>
                <th class="p-4">Meaning</th>
                <th class="p-4">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="app in setupStore.data.env.app">
                <td class="p-4">{{ app['key'] }}</td>
                <td class="p-4">{{ app['value'] }}</td>
                <td class="p-4">{{ app['refer'] }}</td>

                <td v-if="app['key'] === 'APP_ENV' && app['value'] !== 'production'" class="p-4">
                  <i
                    v-tooltip="'Please set your APP_ENV to production'"
                    class="pi pi-info-circle !text-xl text-yellow-600 hover:cursor-pointer"
                  ></i>
                </td>

                <td v-else-if="app['key'] === 'APP_DEBUG' && app['value']" class="p-4">
                  <i
                    v-tooltip="'Please set your APP_DEBUG to false'"
                    class="pi pi-info-circle !text-xl text-yellow-600 hover:cursor-pointer"
                  ></i>
                </td>

                <td v-else class="p-4">
                  <i class="pi pi-check-circle !text-xl text-green-600"></i>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { watch } from 'vue'

import { useSetupStore } from '@/stores/setup'

import Badge from 'primevue/badge'
import ProgressSpinner from 'primevue/progressspinner'
import Message from 'primevue/message'
import PrimeButton from 'primevue/button'

const props = defineProps({
  isPreviousStepsPassed: {
    type: Boolean,
    required: true
  }
})

const setupStore = useSetupStore()

watch(
  () => props.isPreviousStepsPassed,
  (isPassed) => {
    if (!isPassed || setupStore.data.env.isLoaded) {
      return
    }
    loadEnv()
  },
  { immediate: true }
)

function loadEnv() {
  setupStore.checkEnv()
}
</script>

<style scoped></style>
