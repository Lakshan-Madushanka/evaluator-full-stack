<template>
  <section class="awsom-login-section py-8 sm:py-16 px-4">
    <div class="sign-in">
      <div class="content">
        <h2>Sign In</h2>
        <template v-if="authStore.errors.email">
          <p v-for="(error, index) in authStore.errors.email" :key="index" class="text-red-500">
            {{ error }}
          </p>
        </template>
        <div class="form">
          <div class="input-box">
            <input v-model="state.email" type="email" required @keyup.enter="login" />
            <i>Email</i>
            <template v-if="v$.email.$invalid">
              <div class="mt-1">
                <p v-for="(error, index) in v$.email.$errors" :key="index" class="text-red-500">
                  {{ error.$message.replace('Value', 'Email') }}
                </p>
              </div>
            </template>
          </div>
          <div class="input-box">
            <input v-model="state.password" type="password" required @keyup.enter="login" />
            <i>Password</i>
            <template v-if="v$.password.$invalid">
              <div class="mt-1">
                <p v-for="(error, index) in v$.password.$errors" :key="index" class="text-red-500">
                  {{ error.$message.replace('Value', 'Password') }}
                </p>
              </div>
            </template>
          </div>

          <div>
            <label for="remember_me" class="mr-2 text-white"> Remember me</label>
            <Checkbox v-model="state.remember" input-id="remember_me" :binary="true" />
          </div>

          <div class="input-box mt-6 relative" @click="login">
            <input
              type="submit"
              value="Login"
              :disabled="authStore.loading"
              :class="{ '!bg-lime-700 !cursor-not-allowed': authStore.loading }"
            />
            <i
              v-if="authStore.loading"
              class="pi pi-spin pi-spinner absolute top-[4px] !text-white"
              style="font-size: 1.5rem"
            ></i>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { useAuthStore } from '@/stores/auth'
import { useVuelidate } from '@vuelidate/core'
import { email, required } from '@vuelidate/validators'
import { reactive } from 'vue'
import Checkbox from 'primevue/checkbox'

export default {
  components: { Checkbox },
  setup() {
    const authStore = useAuthStore()

    const state = reactive({
      email: 'super-admin@company.com',
      password: 'superAdmin123',
      remember: false
    })

    const rules = {
      email: { required, email },
      password: { required }
    }

    const v$ = useVuelidate(rules, state, { $autoDirty: true, $lazy: true })

    function login() {
      v$.value.$touch()

      if (!v$.value.$invalid && !authStore.loading) {
        authStore.login(state)
      }
    }

    return {
      authStore,
      state,
      v$,
      login
    }
  }
}
</script>
<style scoped>
section {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 2px;
  overflow: hidden;
}

section .sign-in {
  background: #222;
  display: flex;
  width: 400px;
  justify-content: center;
  align-items: center;
  padding: 40px;
  z-index: 100;
  border-radius: 4px;
  box-shadow: 0 15px 35px rgb(0, 0, 0, 0.5);
}

section .sign-in .content {
  position: relative;
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 40px;
}

section .sign-in .content h2 {
  text-transform: uppercase;
  font-size: 2em;
  color: #0f0;
}

section .sign-in .content .form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 25px;
}

section .sign-in .content .form .input-box {
  position: relative;
  width: 100%;
}

section .sign-in .content .form .input-box input {
  position: relative;
  width: 100%;
  border: none;
  outline: none;
  padding: 25px 10px 7.5px;
  border-radius: 7.5px;
  color: #fff;
  background: #333;
  font-weight: 500;
  font-size: 1em;
}

section .sign-in .content .form .input-box i {
  position: absolute;
  left: 0;
  padding: 10px 15px;
  font-style: normal;
  color: #aaa;
  transition: 0.5s;
  pointer-events: none;
}

section .sign-in .content .form .input-box input:focus ~ i,
section .sign-in .content .form .input-box input:valid ~ i {
  transform: translateY(-7.5px);
  font-size: 0.8em;
  color: #fff;
}

section .sign-in .content .form .links {
  position: relative;
  width: 100%;
  display: flex;
  justify-content: space-between;
}

section .sign-in .content .form .links a {
  color: #fff;
  text-decoration: none;
}

section .sign-in .content .form .links a:nth-child(2) {
  color: #0f0;
  font-weight: 600;
}

section .sign-in .content .form input[type='submit'] {
  padding: 10px;
  background: #0f0;
  color: #111;
  font-weight: 600;
  font-size: 1.25em;
  letter-spacing: 0.05em;
  cursor: pointer;
}

section .sign-in .content .form input[type='submit']:hover {
  background: rgb(7, 200, 7);
}
</style>
