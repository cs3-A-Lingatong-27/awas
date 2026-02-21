import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // Now that you've created the file manually, 
            // Vite can find it without any issues.
            input: ['resources/css/app.css'],
            refresh: true,
        }),
    ],
});