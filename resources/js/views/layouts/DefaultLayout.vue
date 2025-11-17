<template>
  <SessionTimeoutMessage />
  <NavBar />

  <router-view
    class="min-h-[calc(100vh-3.65rem)] dark:bg-[size:20px_20px] dark:bg-slate-950 dark:[&>div]:inset-0 dark:[&>div]:bg-[linear-gradient(to_right,#4f4f4f2e_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f2e_1px,transparent_1px)] dark:[&>div]:bg-[size:14px_24px]"
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

const mainColor = ref(getMainColor(appStore.info.color_scheme))

const bgStyle = ref(getBgStyle(mainColor.value))

watch(
  () => appStore.isDarkMode,
  (newVal) => {
    if (newVal) {
      bgStyle.value = {}
    } else {
      bgStyle.value = getBgStyle(mainColor.value)
    }
  },
  { immediate: true }
)

watch(
  () => appStore.info.color_scheme,
  (newVal) => {
    mainColor.value = getMainColor(newVal)

    bgStyle.value = getBgStyle(mainColor.value)
  }
)

function getMainColor(color) {
  return (
    colorSchemes[color]?.['semantic']?.['primary']?.[600] ??
    colorSchemes['indigo']?.['semantic']?.['primary']?.[600]
  )
}

function getBgStyle(color) {
  return {
    background: `radial-gradient(125% 125% at 50% 10%, #fff 50%, ${color} 100%)`
  }
}
</script>
