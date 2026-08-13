import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
        {
            name: 'vercel-dist-output',
            closeBundle() {
                if (fs.existsSync('public')) {
                    fs.cpSync('public', 'dist', { recursive: true });
                }
            },
        },
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

