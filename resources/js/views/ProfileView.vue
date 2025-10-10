<template>
  <div class="flex flex-col justify-center items-center p-4 md:px-[10rem] sm:p-12">
    <div class="mb-8">
      <i class="pi pi-user-edit" style="font-size: 6rem"></i>
    </div>
    <div class="w-full lg:w-4/5 space-y-10">
      <div class="relative">
        <span class="p-float-label w-full">
          <InputText
            id="name"
            v-model="profile.name"
            type="name"
            class="w-full"
            :readonly="authStore.user.role === 'ADMIN'"
            :class="{ 'p-invalid': v$.name.$invalid }"
          />

          <label for="name">Name</label>
        </span>
        <i
          v-if="authStore.user.role === 'ADMIN'"
          v-tooltip="'Admin can only update password'"
          style="font-size: 1.2rem"
          class="pi pi-exclamation-circle absolute top-3 right-[-2rem] hover:cursor-pointer"
        ></i>

        <!-- Client side errors -->
        <template v-if="v$.name.$invalid">
          <div class="mt-1">
            <p v-for="(error, index) in v$.name.$errors" :key="index" class="text-red-500">
              {{ error.$message.replace('Value', 'Name') }}
            </p>
          </div>
        </template>

        <!-- Server side errors -->
        <template v-if="authStore.errors.name">
          <p v-for="(error, index) in authStore.errors.name" :key="index" class="text-red-500">
            {{ error }}
          </p>
        </template>
      </div>

      <div class="relative">
        <span class="p-float-label w-full">
          <InputText
            id="email"
            v-model="profile.email"
            type="email"
            class="w-full"
            :readonly="authStore.user.role === 'ADMIN'"
            :class="{ 'p-invalid': v$.email.$invalid }"
          />
          <label for="email">Email</label>
        </span>
        <i
          v-if="authStore.user.role === 'ADMIN'"
          v-tooltip="'Admin can only update password'"
          style="font-size: 1.2rem"
          class="pi pi-exclamation-circle absolute top-3 right-[-2rem] hover:cursor-pointer"
        ></i>

        <!-- Client side errors -->
        <template v-if="v$.email.$invalid">
          <div class="mt-1">
            <p v-for="(error, index) in v$.email.$errors" :key="index" class="text-red-500">
              {{ error.$message.replace('Value', 'Email') }}
            </p>
          </div>
        </template>

        <!-- Server side errors -->
        <template v-if="authStore.errors.email">
          <p v-for="(error, index) in authStore.errors.email" :key="index" class="text-red-500">
            {{ error }}
          </p>
        </template>
      </div>

      <div>
        <span class="p-float-label w-full">
          <Password
            id="password"
            v-model="profile.password"
            type="password"
            input-class="w-full"
            class="w-full"
            toggle-mask
            :class="{ 'p-invalid': v$.password.$invalid }"
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
        <template v-if="v$.password.$invalid">
          <div class="mt-1">
            <p v-for="(error, index) in v$.password.$errors" :key="index" class="text-red-500">
              {{ error.$message.replace('Value', 'Password') }}
              {{ customRules.massages[error.$validator] }}
            </p>
          </div>
        </template>
      </div>

      <div v-if="profile.password !== ''">
        <span class="p-float-label w-full">
          <Password
            id="password"
            v-model="profile.passwordConfirmation"
            type="password"
            input-class="w-full"
            class="w-full"
            toggle-mask
            :class="{ 'p-invalid': v$.passwordConfirmation.$invalid }"
          />
          <label for="password">Password Confirmation</label>
        </span>

        <!-- Client side errors -->
        <template v-if="v$.passwordConfirmation.$invalid">
          <div class="mt-1">
            <p
              v-for="(error, index) in v$.passwordConfirmation.$errors"
              :key="index"
              class="text-red-500"
            >
              {{ error.$message.replace('value', 'Password Confirmation') }}
              {{ customRules.massages[error.$validator] }}
            </p>
          </div>
        </template>

        <!-- Server side errors -->
        <template v-if="authStore.errors.password">
          <p v-for="(error, index) in authStore.errors.password" :key="index" class="text-red-500">
            {{ error }}
          </p>
        </template>
      </div>

      <PrimeButton
        :label="authStore.status === 'updating' ? 'Updating' : 'Update'"
        :class="['w-full', { '!cursor-not-allowed': v$.$invalid && updateButtonClicked }]"
        :loading="authStore.status === 'updating'"
        :disabled="v$.$invalid && updateButtonClicked"
        @click="update"
      />
    </div>
  </div>
</template>

<script>
import { ref, toRef } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { email, required, requiredIf, sameAs } from '@vuelidate/validators'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import PrimeButton from 'primevue/button'
import Divider from 'primevue/divider'
import { useAuthStore } from '../stores/auth'
import { reactive } from 'vue'
import * as customRules from '../validationRules'
export default {
  components: { InputText, Password, PrimeButton, Divider },
  setup() {
    const authStore = useAuthStore()

    const updateButtonClicked = ref(false)

    const profile = reactive({
      name: authStore.user.name,
      email: authStore.user.email,
      password: '',
      passwordConfirmation: ''
    })

    const passwordRef = toRef(profile, 'password')

    const rules = {
      name: { required },
      email: { required, email },
      password: {
        password: customRules.password
      },
      passwordConfirmation: {
        requiredIfPassword: requiredIf(passwordRef),
        sameAsPassword: sameAs(passwordRef)
      }
    }

    const v$ = useVuelidate(rules, profile, { $autoDirty: true, $lazy: true })

    function getProfileData() {
      if (passwordRef.value === '') {
        return { name: profile.name, email: profile.email }
      }
      return {
        name: profile.name,
        email: profile.email,
        password: profile.password,
        password_confirmation: profile.passwordConfirmation
      }
    }

    function update() {
      updateButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      authStore.update(getProfileData())
    }

    return { authStore, profile, v$, customRules, update, updateButtonClicked }
  }
}
</script>
