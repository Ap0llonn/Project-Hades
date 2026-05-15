<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import ThemeModeDropdown from '../../../shared/components/ThemeModeDropdown.vue';
import {
    CreditCard,
    FileText,
    Home,
    Key,
    KeyRound,
    Lock,
    LogOut,
    Settings,
    ShieldCheck,
    Star,
    Users,
    ShieldHalf
} from 'lucide-vue-next';

defineProps({
    selectedCategory: {
        type: String,
        required: true,
    }
});

const emit = defineEmits(['update:selectedCategory']);
const page = usePage();
const isSettingsPage = computed(() => page.url.startsWith('/settings'));

const selectCategory = (category) => {
    if (isSettingsPage.value) {
        router.visit(route('dashboard'));
        return;
    }

    emit('update:selectedCategory', category);
};
</script>

<template>
    <div class="min-h-screen bg-background text-on-surface transition-colors" style="font-family: 'DM Sans', sans-serif">
        <aside class="fixed left-0 top-0 hidden h-full w-64 flex-col border-r border-outline-variant bg-surface lg:flex">
            <div class="flex items-center border-b border-outline-variant p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600">
                        <ShieldHalf class="h-5 w-5 text-white" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-on-surface">VaultGuardian</span>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left transition-colors"
                    :class="selectedCategory === 'all' ? 'bg-secondary-container text-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface'"
                    @click="selectCategory('all')"
                >
                    <Home class="h-5 w-5" />
                    <span class="font-medium">All Items</span>
                    <span class="ml-auto text-sm">{{ totalCount }}</span>
                </button>

                <div class="px-4 pb-2 pt-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-on-surface-variant">Features</p>
                </div>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left transition-colors"
                    :class="selectedCategory === 'security-center' ? 'bg-secondary-container text-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface'"
                    @click="selectCategory('security-center')"
                >
                    <ShieldCheck class="h-5 w-5" />
                    <span class="font-medium">Security Center</span>
                    <span class="ml-auto text-sm">{{ securityAlertCount }}</span>
                </button>

                <div class="px-4 pb-2 pt-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-on-surface-variant">Actions</p>
                </div>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left transition-colors"
                    :class="selectedCategory === 'password-generator' ? 'bg-secondary-container text-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface'"
                    @click="selectCategory('password-generator')"
                >
                    <KeyRound class="h-5 w-5" />
                    <span class="font-medium">Password Generator</span>
                </button>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left transition-colors"
                    :class="selectedCategory === 'import-export' ? 'bg-secondary-container text-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface'"
                    @click="selectCategory('import-export')"
                >
                    <FileText class="h-5 w-5" />
                    <span class="font-medium">Import / Export</span>
                </button>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left transition-colors"
                    :class="selectedCategory === 'password-sharing' ? 'bg-secondary-container text-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface'"
                    @click="selectCategory('password-sharing')"
                >
                    <Users class="h-5 w-5" />
                    <span class="font-medium">Password Sharing</span>
                </button>
            </nav>

            <div class="space-y-1 border-t border-outline-variant p-4">
                <div class="pb-2">
                    <ThemeModeDropdown full-width />
                </div>
                <Link
                    :href="route('settings')"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 transition-colors"
                    :class="isSettingsPage ? 'bg-secondary-container text-primary' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface'"
                >
                    <Settings class="h-5 w-5" />
                    <span class="font-medium">Settings</span>
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-on-surface"
                >
                    <LogOut class="h-5 w-5" />
                    <span class="font-medium">Log Out</span>
                </Link>
            </div>
        </aside>

        <main class="min-h-screen lg:ml-64">
            <slot />
        </main>
    </div>
</template>
