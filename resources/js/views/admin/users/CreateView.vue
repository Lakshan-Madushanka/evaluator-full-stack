<template>
  <FormLayout>
    <template #header> Create User </template>
    <template #content>
      <div class="md:flex md:flex-wrap">
        <!-- name -->
        <div class="md:w-[calc(50%-1rem)] mb-8 md:mr-8">
          <span class="p-float-label">
            <InputText
              id="username"
              v-model="state.name"
              :class="['w-full', { 'p-invalid': v$.name.$invalid }]"
              type="text"
            />
            <label for="username">User Name</label>
          </span>
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
          <template v-if="usersStore.errors.name">
            <p v-for="(error, index) in usersStore.errors.name" :key="index" class="text-red-500">
              {{ error }}
            </p>
          </template>
        </div>

        <!-- email -->
        <div class="md:w-[calc(50%-1rem)] mb-8">
          <span class="p-float-label">
            <InputText
              id="email"
              v-model="state.email"
              :class="['w-full', { 'p-invalid': v$.name.$invalid }]"
              type="email"
            />
            <label for="email">Email</label>
          </span>
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
          <template v-if="usersStore.errors.email">
            <p v-for="(error, index) in usersStore.errors.email" :key="index" class="text-red-500">
              {{ error }}
            </p>
          </template>
        </div>

        <!-- Role -->
        <div class="w-full mb-8">
          <Dropdown
            v-model="state.role"
            :options="roles"
            option-label="name"
            option-value="value"
            placeholder="Select a role"
            :class="['w-full md:w-[calc(50%-1rem)]', { 'p-invalid': v$.role.$invalid }]"
          />
          <!-- Client side errors -->
          <template v-if="v$.role.$invalid">
            <div class="mt-1">
              <p
                v-for="(error, index) in v$.role.$errors"
                :key="index"
                class="text-red-500 text-sm"
              >
                {{ error.$message.replace('Value', 'Role') }}
                {{ customRules.messages[error.$validator] }}
              </p>
            </div>
          </template>
          <!-- Server side errors -->
          <template v-if="usersStore.errors.role">
            <p v-for="(error, index) in usersStore.errors.role" :key="index" class="text-red-500">
              {{ error }}
            </p>
          </template>
        </div>

        <!-- password -->
        <div v-if="state.role === 2" class="md:w-[calc(50%-1rem)] mb-6 md:mr-8">
          <span class="p-float-label w-full">
            <Password
              id="password"
              v-model="state.password"
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
          </span>
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
          <template v-if="usersStore.errors.password">
            <p
              v-for="(error, index) in usersStore.errors.password"
              :key="index"
              class="text-red-500"
            >
              {{ error }}
            </p>
          </template>
        </div>

        <!-- password confirmation -->
        <div v-if="state.role === 2" class="md:w-[calc(50%-1rem)] mb-6">
          <span class="p-float-label w-full">
            <Password
              id="password"
              v-model="state.password_confirmation"
              type="password"
              input-class="w-full"
              class="w-full"
              toggle-mask
              :class="{ 'p-invalid': v$.password_confirmation.$invalid }"
            />
            <label for="password">Password Confirmation</label>
          </span>

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

      <div class="flex justify-between md:justify-start !mt-[3rem] md:!mt-[1rem] space-x-8">
        <PrimeButton
          class=""
          :label="usersStore.status === 'creating' ? 'Creating' : 'Create'"
          icon="pi pi-user-plus"
          icon-pos="right"
          :disabled="v$.$invalid && createUserButtonClicked"
          :loading="usersStore.status === 'creating'"
          @click="createUser"
        />
        <PrimeButton
          class="p-button-warning"
          label="Clear"
          icon="pi pi-times"
          icon-pos="right"
          @click="clearState"
        />
      </div>
    </template>
  </FormLayout>
</template>

<script>
import { ref, reactive, toRef } from 'vue'

import { useUsersStore } from '@/stores/users'

import PrimeButton from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Divider from 'primevue/divider'
import Password from 'primevue/password'

import { useVuelidate } from '@vuelidate/core'
import { email, required, requiredIf, sameAs } from '@vuelidate/validators'

import FormLayout from '@/views/layouts/FormLayout.vue'

import * as customRules from '@/validationRules'

export default {
  components: {
    FormLayout,
    InputText,
    PrimeButton,
    Dropdown,
    Divider,
    Password
  },
  setup() {
    const usersStore = useUsersStore()

    const state = reactive({
      name: '',
      email: '',
      role: '',
      password: '',
      password_confirmation: ''
    })

    const createUserButtonClicked = ref(false)

    const roles = [
      { name: 'Admin', value: 2 },
      { name: 'Regular', value: 3 }
    ]

    const passwordRef = toRef(state, 'password')

    const rules = {
      name: { required },
      email: { required, email },
      role: { required, exists: customRules.exists([2, 3]) },
      password: {
        requiredIfAdmin: requiredIf(() => state.role === 2),
        password: customRules.password
      },
      password_confirmation: {
        requiredIfPassword: requiredIf(passwordRef),
        sameAsPassword: sameAs(passwordRef)
      }
    }

    const v$ = useVuelidate(rules, state, { $autoDirty: true, $lazy: true })

    function createUser() {
      createUserButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      usersStore.createUser(state)
    }

    function clearState() {
      state.name = ''
      state.email = ''
      state.role = ''
      state.password = ''

      v$.value.$reset()

      usersStore.errors = {}
    }

    return {
      roles,
      state,
      v$,
      createUser,
      clearState,
      customRules,
      createUserButtonClicked,
      usersStore
    }
  }
}
</script>
