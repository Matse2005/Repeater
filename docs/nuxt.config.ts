import { defineNuxtConfig } from "nuxt/config";

export default defineNuxtConfig({
  extends: [
    '@vercel/analytics/nuxt/module'
  ],
  site: {
    name: 'Magento Repeater',
  },
  github: {
    branch: 'dev-main',
    rootDir: 'docs'
  },
  vite: {
    optimizeDeps: {
      include: [
        '@vue/devtools-core',
        '@vue/devtools-kit',
      ]
    }
  }
})
