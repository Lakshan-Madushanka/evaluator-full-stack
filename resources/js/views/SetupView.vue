<template>
  <div class="mt-6 flex justify-center">
    <div class="max-w-7xl w-full">
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
              <Step value="3">
                <span>Permissions</span>
                &nbsp;
                <i v-if="isFilePermissionPassed()" class="pi pi-check text-green-600 !text-lg" />
                <i v-if="isFilePermissionFailed()" class="pi pi-times text-red-600 !text-lg" />
              </Step>
              <Step value="4">
                <span>Env Check</span>
                &nbsp;
                <i v-if="isEnvPassed()" class="pi pi-check text-green-600 !text-lg" />
                <i v-if="isEnvFailed()" class="pi pi-times text-red-600 !text-lg" />
              </Step>
              <Step value="5">
                <span>DB Check</span>
                &nbsp;
                <i v-if="isDBPassed()" class="pi pi-check text-green-600 !text-lg" />
                <i v-if="isDBFailed()" class="pi pi-times text-red-600 !text-lg" />
              </Step>
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
              <StepPanel v-slot="{ active, activateCallback }" value="3">
                <div class="mt-4">
                  <div v-if="hasCompletedPreviousSteps(3) && active">
                    <FilePermissionsChecker
                      :is-previous-steps-passed="hasCompletedPreviousSteps(3)"
                    />
                  </div>
                  <Message v-else severity="error"
                    >Please complete previous steps to continue.</Message
                  >
                </div>
                <div class="flex pt-6 mt-4 justify-between">
                  <PrimeButton
                    label="Back"
                    severity="secondary"
                    icon="pi pi-arrow-left"
                    @click="activateCallback('2')"
                  />
                  <PrimeButton
                    label="Next"
                    icon="pi pi-arrow-right"
                    iconPos="right"
                    @click="activateCallback('4')"
                  />
                </div>
              </StepPanel>
              <StepPanel v-slot="{ active, activateCallback }" value="4">
                <div class="mt-4">
                  <div v-if="hasCompletedPreviousSteps(4) && active">
                    <EnvChecker :is-previous-steps-passed="hasCompletedPreviousSteps(4)" />
                  </div>
                  <Message v-else severity="error"
                    >Please complete previous steps to continue.</Message
                  >
                </div>
                <div class="flex pt-6 mt-4 justify-between">
                  <PrimeButton
                    label="Back"
                    severity="secondary"
                    icon="pi pi-arrow-left"
                    @click="activateCallback('3')"
                  />
                  <PrimeButton
                    label="Next"
                    icon="pi pi-arrow-right"
                    iconPos="right"
                    @click="activateCallback('4')"
                  />
                </div>
              </StepPanel>
              <StepPanel v-slot="{ active, activateCallback }" value="5">
                <div class="mt-4">
                  <div v-if="hasCompletedPreviousSteps(5) && active">
                    <DBChecker :is-previous-steps-passed="hasCompletedPreviousSteps(5)" />
                  </div>
                  <Message v-else severity="error"
                    >Please complete previous steps to continue.</Message
                  >
                </div>
                <div class="flex pt-6 mt-4 justify-between">
                  <PrimeButton
                    label="Back"
                    severity="secondary"
                    icon="pi pi-arrow-left"
                    @click="activateCallback('4')"
                  />
                  <PrimeButton
                    label="Next"
                    icon="pi pi-arrow-right"
                    iconPos="right"
                    @click="activateCallback('6')"
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
import FilePermissionsChecker from '@/components/setup/FilePermissionsChecker.vue'
import EnvChecker from '@/components/setup/EnvChecker.vue'
import DBChecker from '@/components/setup/DBChecker.vue'

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

function isFilePermissionPassed() {
  return setupStore.data.filePermissions.is_passed && setupStore.data.filePermissions.isLoaded
}

function isFilePermissionFailed() {
  return !setupStore.data.filePermissions.is_passed && setupStore.data.filePermissions.isLoaded
}

function isEnvPassed() {
  return setupStore.data.env.is_passed && setupStore.data.env.isLoaded
}

function isEnvFailed() {
  return !setupStore.data.env.is_passed && setupStore.data.env.isLoaded
}

function isDBPassed() {
  return setupStore.data.db.is_passed && setupStore.data.db.isLoaded
}

function isDBFailed() {
  return !setupStore.data.db.is_passed && setupStore.data.db.isLoaded
}

function hasCompletedPreviousSteps(step) {
  switch (step) {
    case 3:
      return isRequirementsPassed()
    case 4:
      return isRequirementsPassed() && isFilePermissionPassed()
    case 5:
      return isRequirementsPassed() && isFilePermissionPassed() && isEnvPassed()
  }
}
</script>
