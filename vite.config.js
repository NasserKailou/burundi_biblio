import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/catalogue.js',
                'resources/js/reader.js',
                'resources/js/fiche-manuel.js',
            ],
            refresh: true,
        }),
    ],
});
