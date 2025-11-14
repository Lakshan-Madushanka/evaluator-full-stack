<template>
  <div>
    <div>
      <div class="flex justify-between items-start">
        <p class="text-xl mb-6 font-bold flex items-center gap-4">
          <span>Create Super Admin Account</span>
          <i
            v-if="setupStore.data.account.loading"
            class="pi pi-spin pi-spinner text-green-600 !text-2xl"
          ></i>
          <i
            v-else-if="setupStore.data.account.is_passed"
            class="pi pi-check-circle text-green-600 !text-2xl"
          ></i>
          <i v-else class="pi pi-times-circle text-red-600 !text-2xl"></i>
        </p>

        <PrimeButton
          @click="checkAccountExists()"
          icon="pi pi-refresh"
          title="Refresh"
          :disabled="setupStore.data.account.loading"
        />
      </div>
      <ProgressSpinner v-if="setupStore.data.account.loading" />
      <div class="space-y-6">
        <div v-if="setupStore.data.account.status.checkingExistence">
          <span>Checking account </span>
          <i class="pi pi-spin pi-spinner" style="font-size: 1rem"></i>
        </div>
        <div v-if="setupStore.data.account.exists" class="space-y-8">
          <Message severity="success">Super admin account already exists 🗹</Message>
        </div>
      </div>

      <div v-if="!setupStore.data.account.exists" class="mt-8">
        <p class="mb-8 text-xl font-bold">Create Account</p>
        <div class="space-y-12">
          <div class="md:w-[calc(50%-1rem)] mb-8 md:mr-8">
            <FloatLabel>
              <InputText
                id="username"
                v-model="form.name"
                :class="['w-full', { 'p-invalid': v$.name.$invalid }]"
                type="text"
              />
              <label for="username">User Name</label>
            </FloatLabel>
            <!-- Client side errors -->
            <template v-if="v$.name.$invalid">
              <div class="mt-1">
                <p
                  v-for="(error, index) in v$.name.$errors"
                  :key="index"
                  class="text-red-500 text-sm"
                >
                  {{ error.$message.replace('Value', 'Name') }}
                </p>
              </div>
            </template>
            <!-- Server side errors -->
            <template v-if="setupStore.data.account.errors.name">
              <p
                v-for="(error, index) in setupStore.data.account.errors.name"
                :key="index"
                class="text-red-500"
              >
                {{ error }}
              </p>
            </template>
          </div>

          <!-- email -->
          <div class="md:w-[calc(50%-1rem)] mb-8">
            <FloatLabel>
              <InputText
                id="email"
                v-model="form.email"
                :class="['w-full', { 'p-invalid': v$.name.$invalid }]"
                type="email"
              />
              <label for="email">Email</label>
            </FloatLabel>
            <!-- Client side errors -->
            <template v-if="v$.email.$invalid">
              <div class="mt-1">
                <p
                  v-for="(error, index) in v$.email.$errors"
                  :key="index"
                  class="text-red-500 text-sm"
                >
                  {{ error.$message.replace('Value', 'Email') }}
                </p>
              </div>
            </template>
            <!-- Server side errors -->
            <template v-if="setupStore.data.account.errors.email">
              <p
                v-for="(error, index) in setupStore.data.account.errors.email"
                :key="index"
                class="text-red-500"
              >
                {{ error }}
              </p>
            </template>
          </div>

          <!-- password -->
          <div class="md:w-[calc(50%-1rem)] mb-6 md:mr-8">
            <FloatLabel class="w-full">
              <Password
                id="password"
                v-model="form.password"
                type="password"
                input-class="w-full"
                class="w-full"
                toggle-mask
                :class="['w-full', { 'p-invalid': v$.password.$invalid }]"
              >
                <template #header>
                  <h6>Pick a password</h6>
                </template>
                <template #footer="sp">
                  {{ sp.level }}
                  <Divider />
                  <p class="mt-2">Suggestions</p>
                  <ul class="pl-2 ml-2 mt-0" style="line-height: 1.5">
                    <li>At least one lowercase</li>
                    <li>At least one uppercase</li>
                    <li>At least one numeric</li>
                    <li>At least one special characters</li>
                    <li>Minimum 8 characters</li>
                  </ul>
                </template>
              </Password>
              <label for="password">Password</label>
            </FloatLabel>
            <!-- Client side errors -->
            <template v-if="v$.password.$invalid">
              <div class="mt-1">
                <p v-for="(error, index) in v$.password.$errors" :key="index" class="text-red-500">
                  {{ error.$message.replace('Value', 'Password') }}
                  {{ customRules.messages[error.$validator] }}
                </p>
              </div>
            </template>
            <!-- Server side errors -->
            <template v-if="setupStore.data.account.errors.password">
              <p
                v-for="(error, index) in setupStore.data.account.errors.password"
                :key="index"
                class="text-red-500"
              >
                {{ error }}
              </p>
            </template>
          </div>

          <!-- password confirmation -->
          <div class="md:w-[calc(50%-1rem)] mb-6">
            <FloatLabel class="w-full">
              <Password
                id="password"
                v-model="form.password_confirmation"
                type="password"
                input-class="w-full"
                class="w-full"
                toggle-mask
                :class="{ 'p-invalid': v$.password_confirmation.$invalid }"
              />
              <label for="password">Password Confirmation</label>
            </FloatLabel>

            <!-- Client side errors -->
            <template v-if="v$.password_confirmation.$invalid">
              <div class="mt-1">
                <p
                  v-for="(error, index) in v$.password_confirmation.$errors"
                  :key="index"
                  class="text-red-500"
                >
                  {{ error.$message.replace('Value', 'Password Confirmation') }}
                  {{ customRules.messages[error.$validator] }}
                </p>
              </div>
            </template>
          </div>
        </div>

        <PrimeButton
          class="mt-12"
          @click="createUser"
          label="create"
          :disabled="v$.$invalid && createUserButtonClicked"
          :loading="setupStore.data.account.status.creating"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, toRef, watch } from 'vue'

import { useSetupStore } from '@/stores/setup'
import ProgressSpinner from 'primevue/progressspinner'
import InputText from 'primevue/inputtext'
import Divider from 'primevue/divider'
import Password from 'primevue/password'
import FloatLabel from 'primevue/floatlabel'
import PrimeButton from 'primevue/button'

import { useVuelidate } from '@vuelidate/core'
import { email, required, requiredIf, sameAs } from '@vuelidate/validators'
import * as customRules from '@/validationRules'
import Message from 'primevue/message'

const props = defineProps({
  isPreviousStepsPassed: {
    type: Boolean,
    required: true
  }
})

const setupStore = useSetupStore()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const passwordRef = toRef(form, 'password')

const rules = {
  name: { required },
  email: { required, email },
  password: {
    required,
    password: customRules.password
  },
  password_confirmation: {
    requiredIfPassword: requiredIf(passwordRef),
    sameAsPassword: sameAs(passwordRef)
  }
}

const v$ = useVuelidate(rules, form, { $autoDirty: true, $lazy: true })

const createUserButtonClicked = ref(false)

watch(
  () => props.isPreviousStepsPassed,
  (isPassed) => {
    if (!isPassed || setupStore.data.account.isLoaded) {
      return
    }
    checkAccountExists()
  },
  { immediate: true }
)

function createUser() {
  createUserButtonClicked.value = true

  v$.value.$touch()

  if (v$.value.$invalid) {
    return
  }

  setupStore.createAccount(form)
}

function checkAccountExists() {
  setupStore.checkAccountExists()
}
</script>

<style scoped></style>
