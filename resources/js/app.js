import './bootstrap';
import { createInertiaApp } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { createApp, h } from 'vue';

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

        AOS.init({
            duration: 700,
            easing: 'ease-out-cubic',
            offset: 80,
            once: true,
        });

        router.on('finish', () => {
            AOS.refreshHard();
        });
    },
    progress: {
        color: '#111827',
    },
});
