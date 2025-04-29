import vue from '@vitejs/plugin-vue';
import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from 'tailwindcss';
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
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
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer],
        },
    },
    optimizeDeps: {
        include: [
            'html2canvas',
            'share-api-polyfill',
            '@fortawesome/fontawesome-svg-core',
            '@fortawesome/vue-fontawesome'
        ],
        esbuildOptions: {
            define: {
                global: 'globalThis' // Add this for html2canvas
            }
        }
    },
    build: {
        commonjsOptions: {
            include: [/html2canvas/, /node_modules/] // Add this
        },
        rollupOptions: {
            output: {
                manualChunks: {
                    html2canvas: ['html2canvas'],
                    share: ['share-api-polyfill']
                }
            }
        }
    }
});
