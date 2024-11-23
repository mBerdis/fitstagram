import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    base: '/~xberdi01/build/',

    build: {
        outDir: 'build',  // Change the output directory to 'build'
        assetsDir: 'assets',  // Static assets (JS, CSS, images) will go into 'assets'

        // Ensure no hashes are added to file names
        rollupOptions: {
          output: {
            entryFileNames: 'assets/js/[name].js',  // JS files without hashes
            chunkFileNames: 'assets/js/[name].js',  // Dynamic chunks without hashes
            assetFileNames: 'assets/[name][extname]',  // Static assets without hashes
          }
        }
      },

    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
