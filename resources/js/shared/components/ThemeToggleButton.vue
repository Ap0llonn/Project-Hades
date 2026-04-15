<script setup>
import { onMounted, ref } from 'vue';
import { Moon, Sun } from 'lucide-vue-next';

const isDark = ref(false);
const THEME_TRANSITION_CLASS = 'theme-transition';
const THEME_TRANSITION_MS = 260;

function applyTheme(nextIsDark) {
    isDark.value = nextIsDark;
    document.documentElement.classList.toggle('dark', nextIsDark);
    localStorage.setItem('theme', nextIsDark ? 'dark' : 'light');
}

function toggleTheme() {
    document.documentElement.classList.add(THEME_TRANSITION_CLASS);
    applyTheme(!isDark.value);

    window.setTimeout(() => {
        document.documentElement.classList.remove(THEME_TRANSITION_CLASS);
    }, THEME_TRANSITION_MS);
}

onMounted(() => {
    const storedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const shouldUseDark = storedTheme ? storedTheme === 'dark' : prefersDark;
    applyTheme(shouldUseDark);
});
</script>

<template>
    <button
        type="button"
        class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 transition-colors hover:border-blue-500 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-blue-400 dark:hover:text-blue-300"
        :aria-label="isDark ? 'Switch to light theme' : 'Switch to dark theme'"
        @click="toggleTheme"
    >
        <Sun v-if="isDark" class="h-5 w-5" />
        <Moon v-else class="h-5 w-5" />
    </button>
</template>
