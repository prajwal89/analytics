import { defineConfig } from 'vite'
import path from 'path'

export default defineConfig({
    build: {
        lib: {
            entry: path.resolve(__dirname, 'resources/js/an.js'),
            name: 'analytics',
            fileName: 'an'
        },
        outDir: 'public/dist',
    }
});
