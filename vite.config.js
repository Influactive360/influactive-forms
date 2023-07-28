import { defineConfig } from 'vite'

export default defineConfig({
  build: {
    rollupOptions: {
      input: {
        frontEnd: 'front-end/form.js',
        backEndForm: 'back-end/post-type/form/form.js',
        backEndLayout: 'back-end/post-type/layout/layout.js',
        backEndTab: 'back-end/post-type/tab/tab.js',
      },
      output: {
        entryFileNames: '[name].bundled.js',
      },
    },
  },
})
