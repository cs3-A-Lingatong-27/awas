import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // Ensure you have created the file at resources/css/app.css
            // This allows Vite to generate the manifest.json file,
            // which prevents 'Run Tests' from failing with Exit Code 2.
            input: ['resources/css/app.css'],
            refresh: true,
        }),
    ],
    build: {
        // Ensuring the build doesn't trigger git-related errors in CI
        chunkSizeWarningLimit: 1600,
    }
});