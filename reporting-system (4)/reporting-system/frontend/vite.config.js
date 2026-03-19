import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',
    port: 5173,
    /**
     * When you open the UI directly on :5173 (React dev server), API calls to /api
     * would otherwise hit the dev server itself and 404. Proxy /api to the nginx
     * service so the same frontend works at both http://localhost and http://localhost:5173.
     */
    proxy: {
      '/api': {
        target: 'http://nginx',
        changeOrigin: true,
      },
    },
  },
})
