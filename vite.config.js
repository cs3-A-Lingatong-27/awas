import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        // This is critical. It forces the creation of manifest.json
        // which resolves the 'ViteManifestNotFoundException'.
        manifest: 'manifest.json',
        outDir: 'public/build',
        rollupOptions: {
            onwarn(warning, warn) {
                // Ignore empty bundle warnings so the build doesn't fail
                if (warning.code === 'EMPTY_BUNDLE') return;
                warn(warning);
            },
        },
    },
});