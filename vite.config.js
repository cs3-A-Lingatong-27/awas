import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // We include both because your Blade layouts (guest.blade.php, app.blade.php)
            // are likely calling @vite(['resources/css/app.css', 'resources/js/app.js'])
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        chunkSizeWarningLimit: 1600,
    },
});