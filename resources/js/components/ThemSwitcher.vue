<template>
  <div class="space-y-6">
    <div class="space-y-4">
      <p>Color Scheme</p>
      <div class="flex-col shadow p-2 bg-gray-100 justify-start items-start gap-2 inline-flex pr-4">
        <div class="self-stretch justify-start items-start gap-2 inline-flex flex-wrap">
          <button
            v-for="(primaryColor, name) of ColorSchemes"
            :key="name"
            type="button"
            :title="name"
            @click="updateColors('primary', name)"
            class="outline outline-2 outline-offset-1 outline-transparent cursor-pointer p-0 rounded-[50%] w-6 h-6"
            :style="{
              backgroundColor: `${primaryColor.semantic.primary[500]}`,
              outlineColor: `${selectedPrimaryColor === name ? 'var(--p-primary-color)' : ''}`,
              outlineOffset: `${name === selectedPrimaryColor ? '2px' : '0'}`
            }"
          ></button>
        </div>
      </div>
    </div>

    <div class="flex-col justify-start items-start gap-4 inline-flex w-full">
      <span class="font-medium dark:text-black">Preset</span>
      <div class="inline-flex items-start gap-2 rounded-[0.71rem] border border-[#00000003] w-full">
        <SelectButton
          v-model="selectedTheme"
          @update:modelValue="onPresetChange"
          :options="presets"
          :unselectable="false"
        >
          <template #option="slotProps">
            <span v-if="selectedTheme === slotProps.option" class="dark:text-gray-100 text-black">{{
              slotProps.option
            }}</span>
            <span v-else>{{ slotProps.option }}</span>
          </template>
        </SelectButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { $t, updatePreset } from '@primeuix/themes'
import Aura from '@primeuix/themes/aura'
import Lara from '@primeuix/themes/lara'
import Nora from '@primeuix/themes/nora'
import Material from '@primeuix/themes/material'
import SelectButton from 'primevue/selectbutton'
import ColorSchemes from '@/themes/colorSchemes'

import { useAppStore } from '@/stores/app'

import { uppercaseFirstLetter } from '@/helpers'

const appStore = useAppStore()

const emit = defineEmits(['update:preset', 'update:colorScheme'])

const presetList = {
  Aura,
  Lara,
  Nora,
  Material
}

const presets = ref(Object.keys(presetList))

const selectedPrimaryColor = ref(appStore.info.color_scheme)

const selectedTheme = ref(uppercaseFirstLetter(appStore.info.preset))

watch(selectedTheme, (newTheme) => {
  emit('update:preset', newTheme)
})

watch(selectedPrimaryColor, (newColorScheme) => {
  emit('update:colorScheme', newColorScheme)
})

const getPresetExt = () => {
  return ColorSchemes[selectedPrimaryColor.value]
}

const applyTheme = () => {
  updatePreset(getPresetExt())
}

const updateColors = (type: string, color) => {
  selectedPrimaryColor.value = color

  applyTheme()
}

// @ts-ignore
const onPresetChange = (value) => {
  // @ts-ignore
  selectedTheme.value = value

  // @ts-ignore
  const preset: string = presetList[value]

  $t().preset(preset).preset(getPresetExt()).use({ useDefaultOptions: true })
}
</script>
