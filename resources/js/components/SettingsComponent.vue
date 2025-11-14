<template>
  <div class="space-y-12">
    <!--    <header class="flex gap-2 items-center">-->
    <!--      <h1 class="text-3xl font-bold">Settings</h1>-->
    <!--      <i class="pi pi-cog" style="font-size: 2rem"></i>-->
    <!--    </header>-->

    <div class="grid lg:grid-cols-2 justify-between gap-y-6 gap-x-24 max-w-[60rem]">
      <div class="space-y-6">
        <div class="flex flex-col gap-4">
          <label for="baseUrl">Site URL / Domain</label>
          <InputText type="text" v-model="form.base_url" placeholder="Site URL" />
          <!-- Server side errors -->
          <template v-if="appStore.errors.base_url">
            <p v-for="(error, index) in appStore.errors.base_url" :key="index" class="text-red-500">
              {{ error }}
            </p>
          </template>
        </div>
        <div class="flex flex-col gap-4">
          <label for="baseUrl">API URL</label>
          <InputText type="text" v-model="apiUrl" placeholder="API URL" readonly disabled />
        </div>
        <div class="flex flex-col gap-4">
          <label for="baseUrl">API V1 URL</label>
          <InputText type="text" v-model="apiV1Url" placeholder="API V1 URL" readonly disabled />
        </div>
      </div>

      <div>
        <ThemSwitcher
          @update:color-scheme="form.color_scheme = $event"
          @update:preset="form.preset = $event"
        />
      </div>
    </div>

    <PrimeButton @click="save" label="Save" :loading="appStore.status === 'saving'" />
  </div>
</template>

<script setup>
import PrimeButton from 'primevue/button'
import InputText from 'primevue/inputtext'
import { nextTick, reactive, ref, watch } from 'vue'
import { useAppStore } from '@/stores/app'
import ThemSwitcher from '@/components/ThemSwitcher.vue'
import { lowercaseFirstLetter } from '@/helpers'

const appStore = useAppStore()

const apiUrl = ref(appStore.info.api_url)
const apiV1Url = ref(appStore.info.api_v1_url)

const form = reactive({
  base_url: appStore.info.base_url,
  preset: appStore.info.preset,
  color_scheme: appStore.info.color_scheme
})

watch(
  () => form.base_url,
  (val) => {
    if (val === '') {
      apiV1Url.value = ''
      return
    }

    nextTick(() => {
      form.base_url = sanitizeUrl(val)
      apiUrl.value = form.base_url + '/api'
      apiV1Url.value = form.base_url + '/api/v1'
    })
  }
)

function sanitizeUrl(url) {
  if (form.base_url.endsWith('/')) {
    return url.slice(0, -1)
  }

  return url
}

function save() {
  form.preset = lowercaseFirstLetter(form.preset)
  appStore.storeSettings(form)
}
</script>
