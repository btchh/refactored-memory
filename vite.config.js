import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            // Entry points for the application
            // 
            // JavaScript: app.js imports all feature modules from the new structure:
            // - features/auth/* (user/admin authentication)
            // - features/tracking/* (admin routing and user tracking)
            // - features/shared/* (multi-step forms, password validation)
            // - components/* (notifications)
            // - utils/* (map utilities, form helpers, validation, token management)
            //
            // CSS: app.css imports organized structure:
            // - base/* (reset, typography, variables)
            // - components/* (buttons, forms, cards, modals, notifications)
            // - features/* (auth, profile, dashboard, tracking)
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            // Optional: Add path aliases for cleaner imports in the future
            // '@': '/resources/js',
            // '@features': '/resources/js/features',
            // '@utils': '/resources/js/utils',
            // '@components': '/resources/js/components',
        },
    },
    css: {
        // CSS processing configuration
        // Vite handles CSS imports and bundling automatically
        // The @import statements in app.css will be resolved and bundled
        postcss: {
            // PostCSS plugins are configured via Tailwind CSS plugin
        },
    },
});
