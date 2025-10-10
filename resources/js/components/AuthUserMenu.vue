<template>
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
</template>

<script>
import MenuComponent from 'primevue/menu'
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { formatText } from '@/helpers'
import { useRouter } from 'vue-router'

export default {
  components: {
    MenuComponent
  },

  setup() {
    const authStore = useAuthStore()

    const router = useRouter()

    const authMenu = ref()

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
      formatText
    }
  }
}
</script>
