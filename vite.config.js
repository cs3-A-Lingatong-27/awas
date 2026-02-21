import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';

// This ensures a dummy file exists so Vite doesn't crash during build
const dummyPath = path.resolve(__dirname, 'resources/js/build-placeholder.js');
if (!fs.existsSync(path.dirname(dummyPath))) {
    fs.mkdirSync(path.dirname(dummyPath), { recursive: true });
}
if (!fs.existsSync(dummyPath)) {
    fs.writeFileSync(dummyPath, '// Placeholder for Vite build');
}

export default defineConfig({
    plugins: [
        laravel({
            // Vite must point to a JS or CSS file, NOT a .blade.php file.
            // We created a placeholder file above to satisfy the build requirement.
            input: ['resources/js/build-placeholder.js'], 
            refresh: true,
        }),
    ],
});