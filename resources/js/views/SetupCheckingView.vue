<template>
  <div>
    <FinishedDialog :visible="showFinishDialog" />
    <Dialog
      v-model:visible="visible"
      modal
      :style="{ width: '50rem', height: '50%' }"
      :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
      :closable="false"
      :draggable="false"
      pt:content:class="h-1/2"
    >
      <div class="w-full h-full gap-x-4 flex justify-center items-center">
        <p class="text-2xl font-bold">Setup Checking</p>
        <i class="pi pi-spin pi-cog" style="font-size: 2rem"></i>
      </div>
    </Dialog>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'

import { useSetupStore } from '@/stores/setup'

import Dialog from 'primevue/dialog'
import FinishedDialog from '@/components/setup/FinishedDialog.vue'

const setupStore = useSetupStore()

const router = useRouter()

const visible = true

const showFinishDialog = ref(false)

onMounted(() => {
  setupStore.checkStatus()
})

watch(
  () => setupStore.status,
  (newStatus) => {
    if (newStatus === 'incompleted') {
      router.push({ name: 'setup' })
      return
    }

    if (newStatus === 'completed') {
      showFinishDialog.value = true
    }
  }
)
</script>
