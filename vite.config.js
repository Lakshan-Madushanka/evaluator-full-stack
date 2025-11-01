import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/main.js'],
      refresh: true,
      buildDirectory: 'app'
    }),
    vue()
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url))
    }
  }
})
