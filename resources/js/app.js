import './bootstrap';
import { createInertiaApp } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { createApp, Fragment, h } from 'vue';
import ToastViewport from './shared/components/ToastViewport.vue';

const THEME_STORAGE_KEY = 'pm-theme';
const THEME_TRANSITION_CLASS = 'theme-transition';
const rootElement = document.documentElement;
const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

const getStoredTheme = () => {
    const theme = localStorage.getItem(THEME_STORAGE_KEY);
    return theme === 'dark' || theme === 'light' ? theme : null;
};

const getSystemTheme = () => (mediaQuery.matches ? 'dark' : 'light');

const applyTheme = (theme, withTransition = false) => {
    if (withTransition) {
        rootElement.classList.add(THEME_TRANSITION_CLASS);
    }

    rootElement.classList.toggle('dark', theme === 'dark');
    rootElement.style.colorScheme = theme === 'dark' ? 'dark' : 'light';

    if (withTransition) {
        window.setTimeout(() => {
            rootElement.classList.remove(THEME_TRANSITION_CLASS);
        }, 550);
    }
};

const setTheme = (theme, withTransition = true) => {
    if (theme !== 'dark' && theme !== 'light' && theme !== 'system') {
        return;
    }

    if (theme === 'system') {
        localStorage.removeItem(THEME_STORAGE_KEY);
        applyTheme(getSystemTheme(), withTransition);
        return;
    }

    localStorage.setItem(THEME_STORAGE_KEY, theme);
    applyTheme(theme, withTransition);
};

applyTheme(getStoredTheme() ?? getSystemTheme());

mediaQuery.addEventListener('change', (event) => {
    if (getStoredTheme() !== null) {
        return;
    }

    applyTheme(event.matches ? 'dark' : 'light', true);
});

window.setAppTheme = setTheme;
window.getAppTheme = () => getStoredTheme() ?? 'system';

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./features/${name}.vue`,
            import.meta.glob('./features/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({
            render: () =>
                h(Fragment, null, [
                    h(App, props),
                    h(ToastViewport),
                ]),
        })
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
