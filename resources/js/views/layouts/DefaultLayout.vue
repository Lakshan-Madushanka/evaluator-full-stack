<template>
  <SessionTimeoutMessage />
  <NavBar />

  <router-view
    class="min-h-[calc(100vh-3.7rem)] dark:bg-[size:20px_20px] dark:bg-slate-950 dark:[&>div]:inset-0 dark:[&>div]:bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] dark:[&>div]:bg-[size:14px_24px]"
    :style="bgStyle"
  ></router-view>
</template>

<script setup>
import NavBar from '@/navBars/DefaultNavBar.vue'
import SessionTimeoutMessage from '@/components/SessionTimeoutMessage.vue'

import colorSchemes from '@/themes/colorSchemes'
import { ref, watch } from 'vue'

import { useAppStore } from '@/stores/app'

const appStore = useAppStore()

const mainColor =
  colorSchemes[import.meta.env.VITE_COLOR_SCHEME]?.['semantic']?.['primary']?.[600] ??
  colorSchemes['indigo']?.['semantic']?.['primary']?.[600]

const bgStyle = ref({
  background: `radial-gradient(125% 125% at 50% 10%, #fff 40%, ${mainColor} 100%)`
})

watch(
  () => appStore.isDarkMode,
  (newVal) => {
    if (newVal) {
      bgStyle.value = {}
    } else {
      bgStyle.value = {
        background: `radial-gradient(125% 125% at 50% 10%, #fff 40%, ${mainColor} 100%)`
      }
    }
  },
  { immediate: true }
)

// onBeforeMount(() => {
//   if (isDarkMode) {
//     bgStyle = ''
//   }
// })
</script>
