<template>
  <div>
    <div>
      <p class="text-xl mb-4 font-bold flex items-center gap-4">
        <span>Database Check</span>
        <i
          v-if="setupStore.data.db.loading"
          class="pi pi-spin pi-spinner text-green-600 !text-2xl"
        ></i>
        <i
          v-else-if="setupStore.data.db.is_passed && setupStore.data.db.isLoaded"
          class="pi pi-check-circle text-green-600 !text-2xl"
        ></i>
        <i
          v-else-if="!setupStore.data.db.is_passed && setupStore.data.db.isLoaded"
          class="pi pi-times-circle text-red-600 !text-2xl"
        ></i>
      </p>

      <ProgressSpinner v-if="setupStore.data.db.loading" />

      <div v-else class="my-8">
        <p class="text-xl font-bold">Info</p>
        <div class="mt-4 space-y-4">
          <p class="flex justify-between w-[15rem]">
            <span>Connection</span>
            <strong>{{ setupStore.data.db.info.connection }}</strong>
          </p>
          <p class="flex justify-between w-[15rem]">
            <span>Name</span>
            <strong>{{ setupStore.data.db.info.name }}</strong>
          </p>
        </div>

        <div class="mt-8">
          <p class="text-lg font-bold">Config Info</p>
          <ul class="mt-4 space-y-4 list-none">
            <li
              v-for="(val, key) in setupStore.data.db.info.config"
              class="flex justify-between items-center w-[30rem]"
            >
              <span>{{ key }}</span>
              <Password v-if="key === 'password'" :model-value="val" :feedback="false" toggleMask />
              <span v-else-if="!val">-</span>
              <span v-else>{{ val }}</span>
            </li>
          </ul>
        </div>

        <Message class="mt-8" severity="info">
          Before continuing, please ensure that the information above is correct. If not, update it
          in the .env file. Click the following button to continue.
        </Message>

        <PrimeButton
          @click="checkConnection"
          label="Continue the setup"
          class="mt-8"
          :loading="setupStore.data.db.status.checkingConnection"
        />

        <div v-if="setupStore.data.db.loadedStatus.checkConnection" class="mt-8 space-y-8">
          <Message
            v-if="
              setupStore.data.db.info.connection === 'sqlite' &&
              !setupStore.data.db.connection.has_database_created
            "
            severity="error"
          >
            Could not create the database file. Please create the file
            <Badge severity="secondary">{{ setupStore.data.db.info.name }}</Badge> in the project's
            root directory and click above button to continue.
          </Message>

          <Message v-if="setupStore.data.db.connection.status === 'fail'" severity="error">
            <div class="flex flex-col gap-y-4 items-start">
              <p>Following error occurred during database connection.</p>
              <Badge severity="danger">
                {{ setupStore.data.db.connection.errors }}
              </Badge>
              <p>
                Please make sure the database connection is working properly, then click the button
                above to continue.
              </p>
            </div>
          </Message>

          <Message
            v-if="
              setupStore.data.db.info.connection === 'mysql' &&
              !setupStore.data.db.connection.has_database_created &&
              setupStore.data.db.connection.status === 'success'
            "
            severity="error"
          >
            Could not create the database. Please manually create the database
            <Badge severity="secondary">{{ setupStore.data.db.info.name }}</Badge> and click above
            button to continue.
          </Message>

          <div v-if="setupStore.data.db.status.migrating" class="flex gap-x-4 items-center">
            <span>Creating database schema </span>
            <i class="pi pi-spin pi-spinner"></i>
          </div>

          <Message
            v-if="setupStore.data.db.is_passed && setupStore.data.db.isLoaded"
            severity="success"
          >
            Database schema created successfully!. Please continue to the next step.
          </Message>

          <Message
            v-if="!setupStore.data.db.is_passed && setupStore.data.db.isLoaded"
            severity="error"
          >
            <div class="flex flex-col gap-y-4 items-start">
              <p>
                Could not create the database schema. Please run the command below from the terminal
                in the project root.
              </p>
              <Badge severity="secondary">php artisan migrate:fresh</Badge>
            </div>
          </Message>
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
import Password from 'primevue/password'
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
    if (!isPassed || setupStore.data.db.isLoaded) {
      return
    }
    loadDBInfo()
  },
  { immediate: true }
)

function loadDBInfo() {
  setupStore.getDBInfo()
}

async function checkConnection() {
  await setupStore.checkDBConnection()

  if (
    setupStore.data.db.connection.status === 'success' &&
    setupStore.data.db.connection.has_database_created
  ) {
    setupStore.migrateDB()
  }
}
</script>

<style scoped></style>
