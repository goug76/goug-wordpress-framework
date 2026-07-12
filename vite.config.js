import { defineConfig } from "vite";
import autoprefixer from "autoprefixer";
import path from "path";

const themePath = __dirname; // Root of the child theme

export default defineConfig({
  css: {
    postcss: {
      plugins: [autoprefixer()],
    },
  },
  server: {
    proxy: {
      "/": {
        target: "http://gouglab.local", // Your Flywheel Local site
        changeOrigin: true,
        secure: false, // Allow self-signed SSL
        ws: true,
      },
    },
    watch: {
      usePolling: true,
    },
    https: false, // Prevent Vite from enforcing HTTPS
  },
  build: {
    outDir: path.resolve(themePath, "dist"), // Ensure output goes to /dist/
    emptyOutDir: true,
    rollupOptions: {
      input: {
        theme_js: path.resolve(themePath, "src/js/script.js"),
        theme_css: path.resolve(themePath, "src/scss/style.scss"),
        admin_css: path.resolve(themePath, "src/scss/admin_style.scss"),
        admin_js: path.resolve(themePath, "src/js/admin_script.js"),
      },
      output: {
        entryFileNames: "[name].js", // Ensures correct JS naming
        assetFileNames: "[name].css", // Ensures correct CSS naming
        format: "es",
      },
    },
  },
  publicDir: false, // This ensures Vite doesn't expect an index.html
});
