import './bootstrap';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';

const storedTheme = localStorage.getItem('theme');
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
const useDarkTheme = storedTheme ? storedTheme === 'dark' : prefersDark;
document.documentElement.classList.toggle('dark', useDarkTheme);

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./features/${name}.vue`,
            import.meta.glob('./features/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#111827',
    },
});
