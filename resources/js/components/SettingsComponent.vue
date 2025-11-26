<template>
  <div class="space-y-12" v-if="authStore.user?.role === 'SUPER_ADMIN'">
    <!--    <header class="flex gap-2 items-center">-->
    <!--      <h1 class="text-3xl font-bold">Settings</h1>-->
    <!--      <i class="pi pi-cog" style="font-size: 2rem"></i>-->
    <!--    </header>-->
    <ConfirmDialog></ConfirmDialog>

    <div class="grid lg:grid-cols-2 justify-between gap-y-6 gap-x-24 max-w-[60rem]">
      <div class="space-y-6">
        <div class="flex flex-col gap-4">
          <label class="flex gap-x-4 items-center" for="baseUrl"
            ><span>Site URL / Domain</span
            ><i
              v-tooltip.bottom="
                'Changing this to a invalid domain can cause site inaccessible! Please make sure the domain is correct before changing it.'
              "
              class="pi pi-info-circle text-red-500"
          /></label>
          <InputText id="baseUrl" type="text" v-model="form.base_url" placeholder="Site URL" />
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
        <ThemeSwitcher
          @update:color-scheme="form.color_scheme = $event"
          @update:preset="form.preset = $event"
        />
      </div>
    </div>

    <PrimeButton @click="save" label="Save" :loading="appStore.status === 'saving'" />
  </div>
</template>

<script setup>
import { nextTick, reactive, ref, watch } from 'vue'

import PrimeButton from 'primevue/button'
import InputText from 'primevue/inputtext'
import ConfirmDialog from 'primevue/confirmdialog'

import { useAppStore } from '@/stores/app'
import { useAuthStore } from '@/stores/auth'

import ThemeSwitcher from '@/components/ThemeSwitcher.vue'
import { lowercaseFirstLetter } from '@/helpers'

import { useConfirm } from 'primevue/useconfirm'

const confirm = useConfirm()

const appStore = useAppStore()
const authStore = useAuthStore()

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

  if (form.base_url !== appStore.info.base_url) {
    confirm.require({
      message:
        'Changing the Site URL may cause site accessible if not set correctly. Are you sure you want to proceed?',
      header: 'Confirm URL Change',
      icon: 'pi pi-exclamation-triangle',
      acceptLabel: 'Yes, Change it',
      rejectLabel: 'No, Cancel',
      acceptProps: {
        severity: 'danger'
      },
      accept: () => {
        appStore.storeSettings(form)
      }
    })
  } else {
    appStore.storeSettings(form)
  }
}
</script>
