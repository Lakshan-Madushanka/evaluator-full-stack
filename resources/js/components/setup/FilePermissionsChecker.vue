<template>
  <div>
    <div class="flex justify-between items-start">
      <p class="text-xl mb-4 font-bold flex items-center gap-4">
        <span>File Permissions</span>
        <i
          v-if="setupStore.data.filePermissions.loading"
          class="pi pi-spin pi-spinner text-green-600 !text-2xl"
        ></i>
        <i
          v-else-if="setupStore.data.filePermissions.is_passed"
          class="pi pi-check-circle text-green-600 !text-2xl"
        ></i>
        <i v-else class="pi pi-times-circle text-red-600 !text-2xl"></i>
      </p>
      <PrimeButton
        @click="loadPermissions()"
        icon="pi pi-refresh"
        title="Refresh"
        :disabled="setupStore.data.filePermissions.loading"
      />
    </div>
    <ProgressSpinner v-if="setupStore.data.filePermissions.loading" />
    <table v-else class="table-auto w-full text-left min-w-max text-slate-800 dark:text-white">
      <thead>
        <tr
          class="text-slate-500 dark:text-white dark:border-slate-950 border-b border-slate-300 bg-slate-50 dark:bg-slate-900"
        >
          <th class="p-4">Path</th>
          <th class="p-4">Current Permission</th>
          <th class="p-4">Required Permission</th>
          <th class="p-4">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="$permission in setupStore.data.filePermissions.list">
          <td class="p-4">{{ $permission['path'] }}</td>
          <td class="p-4">{{ $permission['current_permission'] }}</td>
          <td class="p-4">{{ $permission['required_permission'] }}</td>

          <td v-if="$permission['isSet']" class="p-4">
            <i class="pi pi-check-circle !text-xl text-green-600"></i>
          </td>
          <td v-else class="p-4">
            <i class="pi pi-times-circle !text-xl text-red-600"></i>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { watch } from 'vue'

import { useSetupStore } from '@/stores/setup'
import ProgressSpinner from 'primevue/progressspinner'
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
    if (!isPassed || setupStore.data.filePermissions.isLoaded) {
      return
    }
    loadPermissions()
  },
  { immediate: true }
)

function loadPermissions() {
  setupStore.checkFilePermissions()
}
</script>

<style scoped></style>
