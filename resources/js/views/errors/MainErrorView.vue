<template>
  <div class="flex items-center justify-center p-5 bg-white dark:bg-transparent w-full pt-8">
    <div class="text-center border-1">
      <!-- Handling icon -->
      <div
        v-if="query.type === 'client' || parseInt(query.status) < 500"
        class="inline-flex rounded-full bg-yellow-100 p-4"
      >
        <div class="rounded-full stroke-yellow-600 bg-yellow-200 p-4">
          <svg class="w-16 h-16" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M6 8H6.01M6 16H6.01M6 12H18C20.2091 12 22 10.2091 22 8C22 5.79086 20.2091 4 18 4H6C3.79086 4 2 5.79086 2 8C2 10.2091 3.79086 12 6 12ZM6 12C3.79086 12 2 13.7909 2 16C2 18.2091 3.79086 20 6 20H14"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            ></path>
            <path
              d="M17 16L22 21M22 16L17 21"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            ></path>
          </svg>
        </div>
      </div>
      <div
        v-else-if="parseInt(query.status) === 503"
        class="inline-flex rounded-full bg-sky-100 p-4"
      >
        <div class="rounded-full stroke-sky-600 bg-sky-200 p-4">
          <svg class="w-16 h-16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M6 6L10.5 10.5M6 6H3L2 3L3 2L6 3V6ZM19.259 2.74101L16.6314 5.36863C16.2354 5.76465 16.0373 5.96265 15.9632 6.19098C15.8979 6.39183 15.8979 6.60817 15.9632 6.80902C16.0373 7.03735 16.2354 7.23535 16.6314 7.63137L16.8686 7.86863C17.2646 8.26465 17.4627 8.46265 17.691 8.53684C17.8918 8.6021 18.1082 8.6021 18.309 8.53684C18.5373 8.46265 18.7354 8.26465 19.1314 7.86863L21.5893 5.41072C21.854 6.05488 22 6.76039 22 7.5C22 10.5376 19.5376 13 16.5 13C16.1338 13 15.7759 12.9642 15.4298 12.8959C14.9436 12.8001 14.7005 12.7521 14.5532 12.7668C14.3965 12.7824 14.3193 12.8059 14.1805 12.8802C14.0499 12.9501 13.919 13.081 13.657 13.343L6.5 20.5C5.67157 21.3284 4.32843 21.3284 3.5 20.5C2.67157 19.6716 2.67157 18.3284 3.5 17.5L10.657 10.343C10.919 10.081 11.0499 9.95005 11.1198 9.81949C11.1941 9.68068 11.2176 9.60347 11.2332 9.44681C11.2479 9.29945 11.1999 9.05638 11.1041 8.57024C11.0358 8.22406 11 7.86621 11 7.5C11 4.46243 13.4624 2 16.5 2C17.5055 2 18.448 2.26982 19.259 2.74101ZM12.0001 14.9999L17.5 20.4999C18.3284 21.3283 19.6716 21.3283 20.5 20.4999C21.3284 19.6715 21.3284 18.3283 20.5 17.4999L15.9753 12.9753C15.655 12.945 15.3427 12.8872 15.0408 12.8043C14.6517 12.6975 14.2249 12.7751 13.9397 13.0603L12.0001 14.9999Z"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            ></path>
          </svg>
        </div>
      </div>

      <div v-else class="inline-flex rounded-full bg-red-100 p-4">
        <div class="rounded-full stroke-red-600 bg-red-200 p-4">
          <svg class="w-16 h-16" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M6 8H6.01M6 16H6.01M6 12H18C20.2091 12 22 10.2091 22 8C22 5.79086 20.2091 4 18 4H6C3.79086 4 2 5.79086 2 8C2 10.2091 3.79086 12 6 12ZM6 12C3.79086 12 2 13.7909 2 16C2 18.2091 3.79086 20 6 20H14"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            ></path>
            <path
              d="M17 16L22 21M22 16L17 21"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            ></path>
          </svg>
        </div>
      </div>
      <!-- Handling messages -->

      <!-- Handling client side errors -->
      <template v-if="query.type === 'client'">
        <template v-if="parseInt(query.status) === 404 && query.type === 'client'">
          <h1 class="mt-5 text-[36px] text-slate-800 dark:text-white font-bold lg:text-[50px]">
            404 - Page not found
          </h1>
          <p class="text-slate-600 dark:text-white mt-5 lg:text-lg">
            The page you are looking for doesn't exist or <br />has been removed.
          </p>
        </template>
        <template v-else-if="query.status === 'ECONNABORTED'">
          <h1 class="mt-5 text-[36px] text-slate-800 dark:text-white font-bold lg:text-[50px]">
            Request timeout
          </h1>
          <p class="text-slate-600 dark:text-white mt-5 lg:text-lg">
            {{ uppercaseFirstLetter(query.message) }} <br />try again !
          </p>
        </template>

        <template v-else>
          <h1 class="mt-5 text-[36px] text-slate-800 dark:text-white font-bold lg:text-[50px]">
            Network Error
          </h1>
          <p class="text-slate-600 dark:text-white mt-5 lg:text-lg">
            Please check your internet connecton and try again !
          </p>
        </template>
      </template>

      <!-- Handling server side errors -->
      <template v-if="query.type === 'server'">
        <template v-if="parseInt(query.status) === 503">
          <h1 class="mt-5 text-[36px] text-slate-800 dark:text-white font-bold lg:text-[50px]">
            We are under maintenance
          </h1>
          <p class="text-slate-600 dark:text-white mt-5 lg:text-lg">Please try agian later !</p>
        </template>
        <template v-else-if="parseInt(query.status) === 429">
          <h1 class="mt-5 text-[36px] text-slate-800 dark:text-white font-bold lg:text-[50px]">
            429 - Too many attempts
          </h1>
          <p class="text-slate-600 dark:text-white mt-5 lg:text-lg">Please try agian after</p>
          <vue-countdown
            v-slot="{ minutes, seconds }"
            :time="getRemainingTime()"
            class="flex justify-center"
          >
            <div class="flex space-x-4 mt-4">
              <Badge severity="info" size="xlarge">{{ minutes }} minutes</Badge>
              <Badge severity="info" size="xlarge">{{ seconds }} seconds </Badge>
            </div>
          </vue-countdown>
        </template>
        <template v-else>
          <h1 class="mt-5 text-[36px] text-slate-800 dark:text-white font-bold lg:text-[50px]">
            {{ query.status }}
            {{ query.statusText ? ' - ' + query.statusText : '' }}
          </h1>
          <p class="text-slate-600 dark:text-white mt-5 lg:text-lg">
            {{ query.message }}
          </p>
        </template>
      </template>

      <div class="flex m-6 justify-around">
        <Button label="Home" icon="pi pi-home" @click="() => router.push({ name: 'home' })" />
        <Button label="Back" icon="pi pi-arrow-left" @click="() => router.back()" />
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from 'primevue/button'
import Badge from 'primevue/badge'

import { useRoute, useRouter } from 'vue-router'
import { uppercaseFirstLetter } from '@/helpers'
import VueCountdown from '@chenfengyuan/vue-countdown'

const route = useRoute()
const router = useRouter()

const query = route.query

function getRemainingTime() {
  return parseInt(query.retryAfter) * 1000
}
</script>
