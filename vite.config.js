import { defineConfig } from "vite";
import autoprefixer from "autoprefixer";
import path from "node:path";
import { fileURLToPath } from "node:url";

const currentFile = fileURLToPath(import.meta.url);
const themePath = path.dirname(currentFile);

export default defineConfig({
  root: themePath,

  css: {
    postcss: {
      plugins: [autoprefixer()],
    },
  },

  server: {
    proxy: {
      "/": {
        target: "http://gouglab.local",
        changeOrigin: true,
        secure: false,
        ws: true,
      },
    },

    watch: {
      usePolling: true,
    },

    https: false,
  },

  build: {
    outDir: path.resolve(themePath, "assets"),

    /*
     * Preserve source-controlled images and other static assets.
     * The package build script removes only compiled CSS and JS.
     */
    emptyOutDir: false,

    rollupOptions: {
      input: {
        theme_js: path.resolve(
          themePath,
          "src/js/script.js"
        ),
        theme_css: path.resolve(
          themePath,
          "src/scss/style.scss"
        ),
        admin_css: path.resolve(
          themePath,
          "src/scss/admin_style.scss"
        ),
        admin_js: path.resolve(
          themePath,
          "src/js/admin_script.js"
        ),
      },

      output: {
        entryFileNames: "js/[name].js",
        chunkFileNames: "js/chunks/[name]-[hash].js",

        assetFileNames: (assetInfo) => {
          const fileName = assetInfo.name ?? "";

          if ( fileName.endsWith(".css") ) {
            return "css/[name][extname]";
          }

          if (
            /\.(png|jpe?g|gif|svg|webp|avif|ico)$/i.test(
              fileName
            )
          ) {
            return "images/[name]-[hash][extname]";
          }

          if (
            /\.(woff2?|eot|ttf|otf)$/i.test(
              fileName
            )
          ) {
            return "fonts/[name]-[hash][extname]";
          }

          return "misc/[name]-[hash][extname]";
        },

        format: "es",
      },
    },
  },

  /*
   * GOUG Framework is integrated into WordPress rather than built
   * as a standalone HTML application.
   */
  publicDir: false,
});