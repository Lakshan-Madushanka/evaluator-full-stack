<template>
  <FormLayout v-if="categoriesStore.loading">
    <template #header> Edit Category </template>
    <template #content>
      <FormSkeleton />
    </template>
  </FormLayout>

  <FormLayout v-else>
    <template #header>
      <div class="flex space-x-4 items-center">
        <p>Edit Category</p>
        <PrimeButton class="h-10" icon="pi pi-refresh" @click="refresh" />
      </div>
    </template>
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
          :label="categoriesStore.status === 'updating' ? 'Updating' : 'Update'"
          icon="pi pi-spinner"
          icon-pos="right"
          :disabled="v$.$invalid && updateCategoryButtonClicked"
          :loading="categoriesStore.status === 'updating'"
          @click="updateCategory"
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
import { ref, reactive, onMounted, watch } from 'vue'

import { useCategoriesStore } from '@/stores/categories'

import { useRoute } from 'vue-router'

import PrimeButton from 'primevue/button'
import InputText from 'primevue/inputtext'

import { useVuelidate } from '@vuelidate/core'
import { required } from '@vuelidate/validators'

import FormLayout from '@/views/layouts/FormLayout.vue'
import FormSkeleton from '@/components/skeletons/FormSkeleton.vue'

export default {
  components: {
    FormLayout,
    InputText,
    PrimeButton,

    FormSkeleton
  },
  setup() {
    const categoriesStore = useCategoriesStore()
    const route = useRoute()

    onMounted(() => categoriesStore.getOne(route.params.id))

    const state = reactive({
      name: categoriesStore.category && categoriesStore.category.data.attributes.name
    })

    const updateCategoryButtonClicked = ref(false)

    const rules = {
      name: { required }
    }

    const v$ = useVuelidate(rules, state, { $autoDirty: true, $lazy: true })

    watch(
      () => categoriesStore.category,
      (newCategory) => {
        if (newCategory) {
          state.name = newCategory.data.attributes.name
        }
      }
    )

    function refresh() {
      categoriesStore.getOne(route.params.id)
    }

    function updateCategory() {
      updateCategoryButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      categoriesStore.editCategory(route.params.id, state)
    }

    function clearState() {
      state.name = ''

      v$.value.$reset()

      categoriesStore.errors = {}
    }

    return {
      state,
      v$,
      refresh,
      updateCategory,
      clearState,
      updateCategoryButtonClicked,
      categoriesStore
    }
  }
}
</script>
