<template>
  <Toast />
  <DarkModeSwitch class="hidden" />
  <template v-if="!appStore.initialized || appStore.status === 'loggingOut'">
    <AppInitializingComponent />
  </template>

  <template v-else>
    <RouterView />
  </template>
</template>

<script>
import { watch } from 'vue'
import { useAppStore } from './stores/app'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

import AppInitializingComponent from './components/AppInitializingComponent.vue'
import DarkModeSwitch from './components/DarkModeSwitch.vue'

export default {
  components: { AppInitializingComponent, Toast, DarkModeSwitch },
  setup() {
    const appStore = useAppStore()

    const toast = useToast()

    watch(appStore.toast, (toastState) => {
      toast.add({
        severity: toastState.severity,
        summary: toastState.summary,
        detail: toastState.detail,
        life: toastState.life ? toastState.life : 5000
      })
    })

    return { appStore }
  }
}
</script>

<style>
a.router-link-exact-active,
.router-link-active-exact {
  color: rgb(7, 210, 40);
}

a.router-link-exact-active:hover,
.router-link-active-exact:hover {
  background-color: transparent;
}

.p-datatable-header {
  background: white !important;
}

.dark .p-datatable-header {
  background: black !important;
}
</style>
