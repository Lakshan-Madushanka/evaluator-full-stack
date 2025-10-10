// composables/useInView.js
import { ref, onMounted, onBeforeUnmount } from 'vue'

export default function useIntersectionObserver(target, options = {}) {
  const isVisible = ref(false)
  let observer = null

  onMounted(() => {
    if (!target.value) return

    observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          isVisible.value = true
          observer.disconnect()
        }
      },
      { threshold: 0.1, ...options }
    )

    observer.observe(target.value)
  })

  onBeforeUnmount(() => {
    observer && observer.disconnect()
  })

  return { isVisible }
}
