<template>
  <div class="mt-6 flex justify-center">
    <div class="max-w-4xl w-full">
      <Card>
        <template #title>Setup Wizard</template>
        <template #content>
          <Stepper class="w-full mt-6" value="1">
            <StepList>
              <Step value="1">Welcome</Step>
              <Step value="2">
                <span>Requirements</span>
                &nbsp;
                <i v-if="isRequirementsPassed()" class="pi pi-check text-green-600 !text-lg" />
                <i v-if="isRequirementsFailed()" class="pi pi-times text-red-600 !text-lg" />
              </Step>
              <Step value="3"> Permissions </Step>
            </StepList>
            <StepPanels>
              <StepPanel v-slot="{ activateCallback }" value="1">
                <div class="flex items-center justify-center flex-col mt-4">
                  <p class="text-2xl">Welcome to Evaluator setup wizard 🙏🏻</p>
                </div>
                <div class="flex pt-6 justify-end">
                  <PrimeButton
                    label="Next"
                    icon="pi pi-arrow-right"
                    iconPos="right"
                    @click="activateCallback('2')"
                  />
                </div>
              </StepPanel>
              <StepPanel v-slot="{ active, activateCallback }" value="2">
                <div class="mt-4">
                  <PHPRequirementsChecker v-if="active" />
                </div>
                <div class="flex pt-6 mt-4 justify-between">
                  <PrimeButton
                    label="Back"
                    severity="secondary"
                    icon="pi pi-arrow-left"
                    @click="activateCallback('1')"
                  />
                  <PrimeButton
                    label="Next"
                    icon="pi pi-arrow-right"
                    iconPos="right"
                    @click="activateCallback('3')"
                  />
                </div>
              </StepPanel>
              <StepPanel v-slot="{ activateCallback }" value="3">
                <div class="mt-4">
                  <div
                    v-if="hasCompletedPreviousSteps(3)"
                    class="border-2 border-dashed border-surface-200 dark:border-surface-700 rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium"
                  >
                    Content III
                  </div>
                  <Message v-else severity="error"
                    >Please complete previous steps to continue.</Message
                  >
                </div>
                <div class="pt-6">
                  <PrimeButton
                    label="Back"
                    severity="secondary"
                    icon="pi pi-arrow-left"
                    @click="activateCallback('2')"
                  />
                </div>
              </StepPanel>
            </StepPanels>
          </Stepper>
        </template>
      </Card>
    </div>
  </div>
</template>

<script setup>
import PrimeButton from 'primevue/button'
import Card from 'primevue/card'
import Stepper from 'primevue/stepper'
import StepList from 'primevue/steplist'
import StepPanels from 'primevue/steppanels'
import Step from 'primevue/step'
import StepPanel from 'primevue/steppanel'
import Message from 'primevue/message'

import PHPRequirementsChecker from '@/components/setup/PHPRequirementsChecker.vue'

import { useSetupStore } from '@/stores/setup'

const setupStore = useSetupStore()

function isRequirementsPassed() {
  return (
    setupStore.data.php.version.supported &&
    setupStore.data.php.extensions.is_passed &&
    setupStore.data.php.isLoaded
  )
}

function isRequirementsFailed() {
  return !isRequirementsPassed() && setupStore.data.php.isLoaded
}

function hasCompletedPreviousSteps(step) {
  switch (step) {
    case 3:
      return isRequirementsPassed()
  }
}
</script>
