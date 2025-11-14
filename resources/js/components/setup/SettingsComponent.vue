<template>
  <div class="space-y-6">
    <FinishedDialog :visible="showFinishedDialog" />
    <div class="flex justify-between items-start">
      <p class="text-xl mb-4 font-bold flex items-center gap-4">
        <span>Settings</span>
        <i class="pi pi-check-circle text-green-600 !text-2xl"></i>
      </p>

      <div class="space-x-2 flex items-center">
        <div v-if="setupStore.data.account.exists" class="flex justify-end">
          <PrimeButton
            @click="showFinishedDialog = true"
            label="Finish Setup 🗹"
            severity="success"
          />
        </div>
        <PrimeButton
          @click="appStore.setAppInfo(true)"
          icon="pi pi-refresh"
          title="Refresh"
          :loading="appStore.isLoading"
          adisabled="appStore.isLoading"
        />
      </div>
    </div>

    <SettingsComponent />
  </div>
</template>

<script setup>
import { ref } from 'vue'

import { useSetupStore } from '@/stores/setup'
import { useAppStore } from '@/stores/app'

import PrimeButton from 'primevue/button'

import SettingsComponent from '@/components/SettingsComponent.vue'
import FinishedDialog from '@/components/setup/FinishedDialog.vue'

const setupStore = useSetupStore()
const appStore = useAppStore()

const showFinishedDialog = ref(false)
</script>
