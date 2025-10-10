<template>
  <div class="bg-gray-100 dark:bg-black">
    <slot name="header"></slot>
    <div class="border-2 dark:border-black shadow-xl whitespace-nowrap">
      <slot name="table"></slot>
    </div>
    <slot name="footer"></slot>
  </div>
</template>
