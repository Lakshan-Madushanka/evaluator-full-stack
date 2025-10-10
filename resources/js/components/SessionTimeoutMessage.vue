<template>
  <Message v-if="appStore.status === 'SESSION_EXPIRED'" class="!m-0" severity="warn"
    >Your session has expired. Please login again !</Message
  >
</template>

<script setup>
import { useAppStore } from '@/stores/app'
import Message from 'primevue/message'

const appStore = useAppStore()
</script>
