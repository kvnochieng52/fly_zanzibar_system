import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { router } from '@inertiajs/vue3';

// We've removed AdminLTE JavaScript completely
// Instead, we're implementing the necessary functionality directly in Vue

const appName = import.meta.env.VITE_APP_NAME || 'Zanzibar Fly System';

// Add debug logging for navigation
router.on('start', (event) => {
  console.log('Inertia navigation starting:', event.detail.visit.url)
})

router.on('finish', (event) => {
  console.log('Inertia navigation finished:', event.detail.visit.url)
})

router.on('error', (event) => {
  console.error('Inertia navigation error:', event.detail)
})

router.on('invalid', (event) => {
  console.error('Inertia invalid response:', event.detail)
})

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#008cee',
        showSpinner: true,
        delay: 250,
        includeCSS: true
    }
});
