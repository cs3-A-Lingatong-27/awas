import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // We keep these two as the standard entry points.
            // Ensure resources/css/app.css and resources/js/app.js exist as BLANK files.
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        // This ensures the manifest.json is ALWAYS created, 
        // which prevents the "Unable to locate file in Vite manifest" 
        // error that causes your tests to fail (Exit Code 2).
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            // This prevents the build from failing even if the files are empty
            onwarn(warning, warn) {
                if (warning.code === 'EMPTY_BUNDLE') return;
                warn(warning);
            },
        },
    },
});