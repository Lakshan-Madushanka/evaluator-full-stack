<template>
  <BackgroundOne class="!h-[calc(100vh)]" />
  <div class="h-[calc(100vh)] xl:w-2/3 flex justify-center items-center m-auto">
    <div
      class="w-2/3 sm:bg-[var(--p-primary-color)] text-center px-4 pt-8 text-white space-y-1 sm:space-y-4 shadow-2xl relative"
    >
      <p class="hidden sm:block lg:text-2xl">👍Hi,</p>
      <p class="hidden sm:block lg:text-xl font-bold">
        {{
          appStore.status === 'loggingOut'
            ? 'Please wait until we log out you'
            : 'Please wait until app get initialized !'
        }}
      </p>
      <SpinnerOne class="!mt-[-16px] sm:!mt-[-2px]" />
    </div>
  </div>
</template>

<script setup>
import SpinnerOne from './spinners/SpinnerOne.vue'
import BackgroundOne from './backgrounds/BackgroundOne.vue'
import { useAppStore } from '../stores/app'

const appStore = useAppStore()
</script>
