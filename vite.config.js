import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/pages/admin-pricing.js',
                'resources/js/pages/admin-booking.js',
                'resources/js/pages/admin-analytics.js',
                'resources/js/pages/admin-users.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
