<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { ChevronDown, Monitor, Moon, Sun } from 'lucide-vue-next';

const props = defineProps({
    fullWidth: {
        type: Boolean,
        default: false,
    },
});

const container = ref(null);
const isOpen = ref(false);
const selectedTheme = ref('system');
const isDark = ref(false);
let themeObserver = null;

const options = [
    { value: 'light', label: 'Light', icon: Sun },
    { value: 'dark', label: 'Dark', icon: Moon },
    { value: 'system', label: 'System', icon: Monitor },
];

const selectedOption = computed(
    () => options.find((option) => option.value === selectedTheme.value) ?? options[2],
);

const containerClass = computed(() => (props.fullWidth ? 'relative w-full' : 'relative'));

const triggerLayoutClass = computed(() =>
    props.fullWidth
        ? 'w-full justify-between'
        : '',
);

const menuLayoutClass = computed(() =>
    props.fullWidth
        ? 'left-0 w-full'
        : 'right-0 w-36',
);

const triggerClass = computed(() =>
    isDark.value
        ? 'border-slate-400 bg-slate-900 text-slate-100 hover:bg-slate-800'
        : 'border-slate-300 bg-white text-slate-800 hover:bg-slate-50',
);

const menuClass = computed(() =>
    isDark.value
        ? 'border-slate-500 bg-slate-900'
        : 'border-slate-300 bg-white',
);

const optionClass = (isSelected) => {
    if (isDark.value) {
        return isSelected
            ? 'bg-slate-700 text-white font-semibold'
            : 'text-slate-200 hover:bg-slate-800';
    }

    return isSelected
        ? 'bg-slate-200 text-slate-900 font-semibold'
        : 'text-slate-700 hover:bg-slate-100';
};

const syncThemeState = () => {
    isDark.value = document.documentElement.classList.contains('dark');
};

const loadTheme = () => {
    if (typeof window.getAppTheme === 'function') {
        selectedTheme.value = window.getAppTheme();
    }
};

const setTheme = (theme) => {
    if (typeof window.setAppTheme === 'function') {
        window.setAppTheme(theme);
    }

    selectedTheme.value = theme;
    isOpen.value = false;
};

const handleDocumentClick = (event) => {
    if (!container.value || container.value.contains(event.target)) {
        return;
    }

    isOpen.value = false;
};

onMounted(() => {
    syncThemeState();
    loadTheme();
    document.addEventListener('click', handleDocumentClick);
    themeObserver = new MutationObserver(syncThemeState);
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
    if (themeObserver) {
        themeObserver.disconnect();
    }
});
</script>

<template>
    <div ref="container" :class="containerClass">
        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-medium transition-colors"
            :class="[triggerClass, triggerLayoutClass]"
            @click="isOpen = !isOpen"
        >
            <component :is="selectedOption.icon" class="h-4 w-4" />
            <span>{{ selectedOption.label }}</span>
            <ChevronDown class="h-4 w-4 transition-transform" :class="isOpen ? 'rotate-180' : ''" />
        </button>

        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0"
        >
            <div
                v-if="isOpen"
                class="absolute z-50 mt-2 rounded-lg border p-1 shadow-lg"
                :class="[menuClass, menuLayoutClass]"
            >
                <button
                    v-for="option in options"
                    :key="option.value"
                    type="button"
                    class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm transition-colors"
                    :class="optionClass(selectedTheme === option.value)"
                    @click="setTheme(option.value)"
                >
                    <component :is="option.icon" class="h-4 w-4" />
                    <span>{{ option.label }}</span>
                </button>
            </div>
        </Transition>
    </div>
</template>
