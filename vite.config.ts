// import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';
import { defineConfig } from 'vite';

// Check if vendor directory exists
const vendorZiggyPath = path.resolve(__dirname, './vendor/tightenco/ziggy');
const hasVendorZiggy = fs.existsSync(vendorZiggyPath);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        // wayfinder({
        //     formVariants: true,
        // }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // Custom plugin to handle missing Ziggy vendor directory
        {
            name: 'ziggy-fallback',
            resolveId(id) {
                if (id === 'ziggy-js' && !hasVendorZiggy) {
                    // Return a virtual module ID
                    return '\0ziggy-fallback';
                }
            },
            load(id) {
                if (id === '\0ziggy-fallback') {
                    // Provide a minimal fallback implementation
                    return `
                        export const ZiggyVue = {
                            install(app) {
                                const route = (name, params) => {
                                    console.warn('Ziggy not available during build. Route helper will be available at runtime.');
                                    return name;
                                };
                                app.config.globalProperties.route = route;
                                app.provide('route', route);
                            }
                        };
                    `;
                }
            },
        },
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            ...(hasVendorZiggy && { 'ziggy-js': vendorZiggyPath }),
        },
    },
});
