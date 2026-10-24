import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/components/user-sidebar.css',
                'resources/js/user-sidebar.js',
                'resources/css/pages/user-analytics.css',
                'resources/js/user-analytics.js',
                'resources/css/pages/user-settings.css',
                'resources/js/user-settings.js',
                'resources/css/pages/user-lost-mode.css',
                'resources/js/user-lost-mode.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
