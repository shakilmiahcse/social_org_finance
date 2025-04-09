import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

// Font Awesome Imports
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { fas } from '@fortawesome/free-solid-svg-icons';
import 'vue3-select/dist/vue3-select.css';

// Toastification import for Vue 3
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

// Add all solid icons to the library
library.add(fas);

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./pages/${name}.vue`,
      import.meta.glob<DefineComponent>('./pages/**/*.vue')
    ),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });

    // Register plugins
    app.use(plugin);
    app.use(ZiggyVue);
    app.use(Toast, {
      position: 'top-right',
      timeout: 3000,
      closeOnClick: true,
      pauseOnHover: true,
      draggable: true,
      showCloseButtonOnHover: false,
    });

    // Global component registration
    app.component('font-awesome-icon', FontAwesomeIcon);

    app.mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});

// Initialize light/dark theme on page load
initializeTheme();
