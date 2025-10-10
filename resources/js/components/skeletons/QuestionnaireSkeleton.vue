<template>
  <header class="bg-white dark:bg-black mb-8">
    <div class="p-8"><Skeleton height="4rem" /></div>
  </header>
  <div class="grid xl:grid grid-cols-[18%_82%]">
    <div class="!bg-white dark:!bg-black p-2">
      <div class="m-4">
        <Skeleton height="16rem" />
      </div>
    </div>
    <div class="mx-2 bg-white dark:bg-black flex flex-col justify-center items-center">
      <Skeleton v-for="n in 10" :key="n" class="m-4" height="16rem" width="95%" />
    </div>
  </div>
</template>

<script>
import Skeleton from 'primevue/skeleton'

export default {
  components: { Skeleton },
  setup() {}
}
</script>
