<template>
  <div class="shadow-md bg-white dark:bg-black p-4">
    <div class="text-2xl font-bold uppercase px-4 text-center sm:text-left">
      <slot name="header"></slot>
    </div>
    <div class="px-2 sm:px-8 py-12 p-input-filled space-y-8">
      <slot name="content"></slot>
    </div>
    <slot name="footer"></slot>
  </div>
</template>
