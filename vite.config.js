import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),



    ],
    build: {
        outDir: 'dist' // Ensure the output directory is set to 'dist'
    },
    server: {
        port: process.env.PORT || 3000, // Sử dụng cổng từ biến môi trường hoặc cổng 3000
    },
});
