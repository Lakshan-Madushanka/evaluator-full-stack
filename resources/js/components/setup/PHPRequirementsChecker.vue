<template>
  <div>
    <div>
      <p class="text-xl mb-4 font-bold flex items-center gap-4">
        <span>PHP Version</span>
        <i
          v-if="setupStore.data.php.version.loading"
          class="pi pi-spin pi-spinner text-green-600 !text-2xl"
        ></i>
        <i
          v-else-if="setupStore.data.php.version.supported"
          class="pi pi-check-circle text-green-600 !text-2xl"
        ></i>
        <i v-else class="pi pi-times-circle text-red-600 !text-2xl"></i>
      </p>
      <ul class="list list-none space-y-2 text-bold">
        <li class="w-[15rem] justify-between flex">
          <span>Platform Version</span>
          <span v-if="!setupStore.data.php.version.loading">{{
            setupStore.data.php.version.current
          }}</span>
          <i v-else class="pi pi-spin pi-spinner"></i>
        </li>
        <li class="w-[15rem] justify-between flex">
          <span>Minimum Version</span>
          <span v-if="!setupStore.data.php.version.loading">
            {{ setupStore.data.php.version.minimum }}
          </span>
          <i v-else class="pi pi-spin pi-spinner"></i>
        </li>
        <li class="w-[15rem] justify-between flex">
          <span>Support</span>
          <span v-if="!setupStore.data.php.version.loading">
            <i
              v-if="setupStore.data.php.version.supported"
              class="pi pi-check-circle !text-xl text-green-600"
            ></i>
            <i v-else class="pi pi-times-circle !text-xl text-red-600"></i>
          </span>
          <i v-else class="pi pi-spin pi-spinner"></i>
        </li>
      </ul>
    </div>

    <Divider class="!my-8" />

    <div v-if="setupStore.data.php.version.supported" class="mt-8">
      <p class="text-xl mb-4 font-bold flex items-center gap-4">
        <span>PHP Extensions</span>
        <i
          v-if="setupStore.data.php.extensions.loading"
          class="pi pi-spin pi-spinner text-green-600 !text-2xl"
        ></i>
        <i
          v-else-if="setupStore.data.php.extensions.is_passed"
          class="pi pi-check-circle text-green-600 !text-2xl"
        ></i>
        <i v-else class="pi pi-times-circle text-red-600 !text-2xl"></i>
      </p>

      <ProgressSpinner v-if="setupStore.data.php.extensions.loading" />
      <table v-else class="table-auto w-full text-left min-w-max text-slate-800">
        <thead>
          <tr class="text-slate-500 border-b border-slate-300 bg-slate-50">
            <th class="p-4">Extension</th>
            <th class="p-4">Installed</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(status, extension) in setupStore.data.php.extensions.list">
            <td class="p-4">{{ extension }}</td>
            <td v-if="status" class="p-4">
              <i class="pi pi-check-circle !text-xl text-green-600"></i>
            </td>
            <td v-else class="p-4">
              <i class="pi pi-times-circle !text-xl text-red-600"></i>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

import { useSetupStore } from '@/stores/setup'

import Divider from 'primevue/divider'
import ProgressSpinner from 'primevue/progressspinner'

const setupStore = useSetupStore()

onMounted(async () => {
  if (setupStore.data.php.isLoaded) {
    return
  }

  await setupStore.checkPHPVersion()

  if (setupStore.data.php.version.supported) {
    await setupStore.checkPHPExtensions()
  }

  setupStore.data.php.isLoaded = true
})
</script>

<style scoped></style>
