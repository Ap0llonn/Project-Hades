<script setup>
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    CreditCard,
    FileText,
    Home,
    Key,
    Lock,
    LogOut,
    Settings,
    Star,
} from 'lucide-vue-next';

defineProps({
    selectedCategory: {
        type: String,
        required: true,
    },
    totalCount: {
        type: Number,
        required: true,
    },
    favoriteCount: {
        type: Number,
        required: true,
    },
    loginCount: {
        type: Number,
        required: true,
    },
    cardCount: {
        type: Number,
        required: true,
    },
    noteCount: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['update:selectedCategory']);

const selectCategory = (category) => {
    emit('update:selectedCategory', category);
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-900 transition-colors" style="font-family: 'DM Sans', sans-serif">
        <aside class="fixed left-0 top-0 hidden h-full w-64 flex-col border-r border-gray-200 bg-white lg:flex">
            <div class="flex items-center justify-between border-b border-gray-200 p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600">
                        <Lock class="h-5 w-5 text-white" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-gray-900">VaultGuardian</span>
                </div>
            </div>

            <nav class="flex-1 space-y-1 p-4">
                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left transition-colors"
                    :class="selectedCategory === 'all' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                    @click="selectCategory('all')"
                >
                    <Home class="h-5 w-5" />
                    <span class="font-medium">All Items</span>
                    <span class="ml-auto text-sm">{{ totalCount }}</span>
                </button>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left transition-colors"
                    :class="selectedCategory === 'favorites' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                    @click="selectCategory('favorites')"
                >
                    <Star class="h-5 w-5" />
                    <span class="font-medium">Favorites</span>
                    <span class="ml-auto text-sm">{{ favoriteCount }}</span>
                </button>

                <div class="px-4 pb-2 pt-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Categories</p>
                </div>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left transition-colors"
                    :class="selectedCategory === 'login' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                    @click="selectCategory('login')"
                >
                    <Key class="h-5 w-5" />
                    <span class="font-medium">Logins</span>
                    <span class="ml-auto text-sm">{{ loginCount }}</span>
                </button>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left transition-colors"
                    :class="selectedCategory === 'card' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                    @click="selectCategory('card')"
                >
                    <CreditCard class="h-5 w-5" />
                    <span class="font-medium">Cards</span>
                    <span class="ml-auto text-sm">{{ cardCount }}</span>
                </button>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left transition-colors"
                    :class="selectedCategory === 'note' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50'"
                    @click="selectCategory('note')"
                >
                    <FileText class="h-5 w-5" />
                    <span class="font-medium">Secure Notes</span>
                    <span class="ml-auto text-sm">{{ noteCount }}</span>
                </button>
            </nav>

            <div class="space-y-1 border-t border-gray-200 p-4">
                <button type="button" class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-gray-700 transition-colors hover:bg-gray-50">
                    <Settings class="h-5 w-5" />
                    <span class="font-medium">Settings</span>
                </button>
                <Link
                    :href="route('login')"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-gray-700 transition-colors hover:bg-gray-50"
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
