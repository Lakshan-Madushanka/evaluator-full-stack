<template>
  <div class="md:flex md:flex-wrap">
    <div v-for="n in 8" :key="n" class="md:w-[calc(50%-2rem)] mb-8 md:mr-8">
      <Skeleton class="" height="3rem"></Skeleton>
    </div>
  </div>
  <div class="flex justify-between md:justify-start !mt-[3rem] md:!mt-[1rem] space-x-8">
    <Skeleton class="" width="25%" height="2rem"></Skeleton>
    <Skeleton class="" width="25%" height="2rem"></Skeleton>
  </div>
</template>

<script>
import Skeleton from 'primevue/skeleton'

export default {
  components: { Skeleton },
  setup() {}
}
</script>
