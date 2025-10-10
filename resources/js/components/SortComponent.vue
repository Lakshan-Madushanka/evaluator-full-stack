<template>
  <i
    :class="[
      'hover:cursor-pointer',
      'pi',
      {
        'pi-sort-alt': !direction,
        'pi-sort-alpha-up': direction === 'asc',
        'pi-sort-alpha-down-alt': direction === 'desc'
      }
    ]"
    @click="onDirectionChange"
  ></i>
</template>

<script>
import { ref, watch } from 'vue'

export default {
  props: {
    dir: {
      type: String,
      required: false,
      default: ''
    }
  },
  emits: ['directionChange'],
  setup(props, { emit }) {
    const direction = ref(null)

    watch(
      () => props.dir,
      function (dir) {
        if (dir === '') {
          dir = null
        }
        direction.value = dir
      }
    )

    function onDirectionChange() {
      const dir = direction.value

      switch (dir) {
        case null:
          direction.value = 'asc'
          break
        case 'asc':
          direction.value = 'desc'
          break
        default:
          direction.value = null
      }
      emit('directionChange', direction.value)
    }
    return { onDirectionChange, direction }
  }
}
</script>
