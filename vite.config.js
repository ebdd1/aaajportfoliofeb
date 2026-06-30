import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        // Disable HMR untuk production - ini penting untuk tunnel
        hmr: false,
        // Force HTTPS untuk semua asset
        https: false,
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: [
                { paths: ['resources/**'] },
            ],
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
    // Build configuration
    build: {
        // Set public path untuk production
        publicPath: 'build',
    },
});