import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/app-rtl.css',
                'resources/js/app.js',
                'resources/js/ajax-form.js',
                'resources/js/patient-gallery.js',
            ],
            refresh: true,
        }),
    ],
});
