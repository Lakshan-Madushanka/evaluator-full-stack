<template>
  <FormLayout>
    <template #header> Create Category </template>
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
            <label for="username">Category Name</label>
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
          <template v-if="categoriesStore.errors.name">
            <p
              v-for="(error, index) in categoriesStore.errors.name"
              :key="index"
              class="text-red-500"
            >
              {{ error }}
            </p>
          </template>
        </div>
      </div>

      <div class="flex justify-between md:justify-start !mt-[3rem] md:!mt-[1rem] space-x-8">
        <PrimeButton
          class=""
          :label="categoriesStore.status === 'creating' ? 'Creating' : 'Create'"
          icon="pi pi-plus"
          icon-pos="right"
          :disabled="v$.$invalid && createCategoryButtonClicked"
          :loading="categoriesStore.status === 'creating'"
          @click="createCategory"
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
import { ref, reactive } from 'vue'

import { useCategoriesStore } from '@/stores/categories'

import PrimeButton from 'primevue/button'
import InputText from 'primevue/inputtext'

import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators'

import FormLayout from '@/views/layouts/FormLayout.vue'

export default {
  components: {
    FormLayout,
    InputText,
    PrimeButton
  },
  setup() {
    const categoriesStore = useCategoriesStore()

    const state = reactive({
      name: ''
    })

    const createCategoryButtonClicked = ref(false)

    const rules = {
      name: { required }
    }

    const v$ = useVuelidate(rules, state, { $autoDirty: true, $lazy: true })

    function createCategory() {
      createCategoryButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      categoriesStore.createCategory(state)
    }

    function clearState() {
      state.name = ''

      v$.value.$reset()

      categoriesStore.errors = {}
    }

    return {
      state,
      v$,
      createCategory,
      clearState,
      createCategoryButtonClicked,
      categoriesStore
    }
  }
}
</script>
