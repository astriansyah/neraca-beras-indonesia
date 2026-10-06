import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/dashboard.js',
                'resources/js/pages/production.js',
                'resources/js/pages/consumption.js',
                'resources/js/pages/trade.js',
                'resources/js/pages/stock.js',
                'resources/js/pages/balance.js',
                'resources/js/pages/simulation.js',
                'resources/js/pages/data.js',
                'resources/js/pages/methodology.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
