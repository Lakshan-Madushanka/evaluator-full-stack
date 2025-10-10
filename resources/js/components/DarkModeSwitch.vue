<template>
  <div>
    <i
      :class="`${icon} hover:cursor-pointer`"
      style="font-size: 1.3rem"
      aria-haspopup="true"
      aria-controls="overlay_menu"
      @click="toggleDarkModeMenu"
    />
    <MenuComponent ref="darkModeMenu" :model="modeItems" :popup="true" />
  </div>
</template>

<script setup lang="ts">
import { ref, onBeforeMount, watch } from 'vue'
import MenuComponent from 'primevue/menu'

import { useAppStore } from '@/stores/app'

const appStore = useAppStore()

const isDark = ref(false)

const icon = ref('pi pi-sun')

const darkModeMenu = ref()

const modeItems = [
  {
    label: '🌙 Dark',
    command: () => {
      toggleDarkMode('dark')
    }
  },
  {
    label: '☀️ Light',
    command: () => {
      toggleDarkMode('light')
    }
  },
  { separator: true },
  {
    label: '⚙️ System',
    command: () => {
      toggleDarkMode('system')
    }
  }
]

const toggleDarkModeMenu = (event) => {
  darkModeMenu.value.toggle(event)
}

// Load saved preference or system preference
onBeforeMount(() => {
  isDark.value = appStore.isDarkMode

  applyTheme()
})

watch(isDark, applyTheme)

function toggleDarkMode(mode) {
  switch (mode) {
    case 'dark':
      isDark.value = true
      break
    case 'light':
      isDark.value = false
      break
    case 'system':
      isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
      break
  }

  appStore.isDarkMode = isDark.value
}

function applyTheme() {
  const html = document.documentElement
  if (isDark.value) {
    html.classList.add('dark')
    localStorage.setItem('theme', 'dark')
    icon.value = 'pi pi-moon'
  } else {
    html.classList.remove('dark')
    localStorage.setItem('theme', 'light')
    icon.value = 'pi pi-sun'
  }
}
</script>
