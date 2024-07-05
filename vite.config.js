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
        manifest: true, // Kích hoạt tạo manifest
        outDir: 'public/build', // Đảm bảo thư mục xuất dữ liệu là 'public/build'
    },
    server: {
        port: process.env.PORT || 3000, // Sử dụng cổng từ biến môi trường hoặc cổng 3000
        strictPort: true,
        host: '0.0.0.0',   // Lắng nghe trên tất cả các địa chỉ IP

    },
});
