<template>
  <FormLayout v-if="teamsStore.loading">
    <template #header> Edit Team </template>
    <template #content>
      <FormSkeleton />
    </template>
  </FormLayout>

  <FormLayout v-else>
    <template #header>
      <div class="flex space-x-4 items-center">
        <p>Edit Team</p>
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
            <label for="username">Team Name</label>
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
          <template v-if="teamsStore.errors.name">
            <p v-for="(error, index) in teamsStore.errors.name" :key="index" class="text-red-500">
              {{ error }}
            </p>
          </template>
        </div>
      </div>

      <div class="flex justify-between md:justify-start !mt-[3rem] md:!mt-[1rem] space-x-8">
        <PrimeButton
          class=""
          :label="teamsStore.status === 'updating' ? 'Updating' : 'Update'"
          icon="pi pi-spinner"
          icon-pos="right"
          :disabled="v$.$invalid && updateTeamButtonClicked"
          :loading="teamsStore.status === 'updating'"
          @click="updateTeam"
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

import { useTeamsStore } from '@/stores/teams'

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
    const teamsStore = useTeamsStore()
    const route = useRoute()

    onMounted(() => teamsStore.getOne(route.params.id))

    const state = reactive({
      name: teamsStore.team && teamsStore.team.data.attributes.name
    })

    const updateTeamButtonClicked = ref(false)

    const rules = {
      name: { required }
    }

    const v$ = useVuelidate(rules, state, { $autoDirty: true, $lazy: true })

    watch(
      () => teamsStore.team,
      (newTeam) => {
        if (newTeam) {
          state.name = newTeam.data.attributes.name
        }
      }
    )

    function refresh() {
      teamsStore.getOne(route.params.id)
    }

    function updateTeam() {
      updateTeamButtonClicked.value = true

      v$.value.$touch()

      if (v$.value.$invalid) {
        return
      }

      teamsStore.editTeam(route.params.id, state)
    }

    function clearState() {
      state.name = ''

      v$.value.$reset()

      teamsStore.errors = {}
    }

    return {
      state,
      v$,
      refresh,
      updateTeam,
      clearState,
      updateTeamButtonClicked,
      teamsStore
    }
  }
}
</script>
