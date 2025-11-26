<template>
  <div>
    <i
      class="pi pi-user hover:cursor-pointer"
      style="font-size: 1rem"
      aria-haspopup="true"
      aria-controls="overlay_menu"
      @click="toggle"
    >
      <span class="ml-2 text-sm hidden sm:inline">{{ formatText(authStore.user.name) }}</span>
    </i>
    <MenuComponent id="overlay_menu" ref="authMenu" :model="authItems" :popup="true" />

    <Dialog
      v-model:visible="showSettings"
      modal
      header=""
      :style="{ width: '70vw' }"
      :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
      <template #header>
        <header class="flex gap-2 items-center">
          <h1 class="text-2xl font-bold">Settings</h1>
          <i class="pi pi-cog" style="font-size: 1.5rem"></i>
        </header>
      </template>
      <SettingsComponent />
    </Dialog>
  </div>
</template>

<script>
import MenuComponent from 'primevue/menu'
import Dialog from 'primevue/dialog'
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { formatText } from '@/helpers'
import { useRouter } from 'vue-router'
import SettingsComponent from '@/components/SettingsComponent.vue'

export default {
  components: {
    SettingsComponent,
    Dialog,
    MenuComponent
  },

  setup() {
    const authStore = useAuthStore()

    const router = useRouter()

    const authMenu = ref()

    const showSettings = ref(false)

    const authItems = [
      {
        label: 'Profile',
        icon: 'pi pi-fw pi-user-edit',
        command: () => {
          router.push({ name: 'profile' })
        }
      },
      {
        label: 'Dashboard',
        icon: 'pi pi-fw pi-th-large',
        command: () => {
          router.push({ name: 'admin.dashboard' })
        },
        to: { name: 'admin.dashboard' }
      },
      {
        label: 'Settings',
        icon: 'pi pi-fw pi-cog',
        visible: () => authStore.user?.role === 'SUPER_ADMIN',
        command: () => {
          showSettings.value = true
        }
      },
      { separator: true },
      {
        label: 'Sign Out',
        icon: 'pi pi-fw pi-sign-out',
        command: () => {
          authStore.logOut()
        }
      }
    ]

    const toggle = (event) => {
      authMenu.value.toggle(event)
    }

    return {
      authStore,
      authItems,
      authMenu,
      toggle,
      formatText,
      showSettings
    }
  }
}
</script>
