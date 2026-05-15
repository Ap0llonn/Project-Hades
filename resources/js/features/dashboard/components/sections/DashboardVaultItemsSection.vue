<script setup>
import { computed, ref } from 'vue';
import {
    AlertCircle,
    Clock,
    Copy,
    CreditCard,
    Eye,
    EyeOff,
    FileText,
    IdCard,
    Key,
    Search,
    Star,
    Trash2,
} from 'lucide-vue-next';

const props = defineProps({
    categoryTitle: {
        type: String,
        required: true,
    },
    filteredPasswords: {
        type: Array,
        required: true,
    },
    passwords: {
        type: Array,
        required: true,
    },
    visiblePasswords: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits([
    'toggle-password-visibility',
    'copy-password',
    'toggle-favorite',
    'delete-item',
]);

const unavailableWebsiteLogoIds = ref(new Set());

const extractHostname = (urlValue) => {
    if (typeof urlValue !== 'string') {
        return null;
    }

    const value = urlValue.trim();
    if (!value) {
        return null;
    }

    const normalizedUrl = /^https?:\/\//i.test(value) ? value : `https://${value}`;

    try {
        const { hostname } = new URL(normalizedUrl);
        return hostname || null;
    } catch {
        return null;
    }
};

const loginLogoUrlByItemId = computed(() => {
    const logoMap = {};

    props.filteredPasswords.forEach((item) => {
        if (item.category !== 'login') {
            return;
        }

        const itemId = String(item.id);
        if (unavailableWebsiteLogoIds.value.has(itemId)) {
            return;
        }

        const hostname = extractHostname(item.url);
        if (!hostname) {
            return;
        }

        logoMap[itemId] = `https://www.google.com/s2/favicons?domain=${encodeURIComponent(hostname)}&sz=64`;
    });

    return logoMap;
});

const markWebsiteLogoAsUnavailable = (itemId) => {
    const next = new Set(unavailableWebsiteLogoIds.value);
    next.add(String(itemId));
    unavailableWebsiteLogoIds.value = next;
};
</script>

<template>
    <section class="overflow-hidden rounded-2xl border border-outline-variant bg-surface">
        <div class="border-b border-outline-variant p-6">
            <h2 class="text-xl font-semibold tracking-tight text-on-surface">{{ props.categoryTitle }}</h2>
            <p class="mt-1 text-sm text-on-surface-variant">{{ props.filteredPasswords.length }} items</p>
        </div>

        <div class="divide-y divide-outline-variant">
            <div v-if="props.filteredPasswords.length === 0" class="p-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-surface-container-low">
                    <Search class="h-8 w-8 text-on-surface-variant" />
                </div>
                <p class="mb-2 font-medium text-on-surface-variant">No items found</p>
                <p class="text-sm text-on-surface-variant">Try adjusting your search or filters</p>
            </div>

            <div v-for="pwd in props.filteredPasswords" :key="pwd.id" class="group p-6 transition-colors hover:bg-surface-container-low">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex min-w-0 flex-1 items-center gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed">
                            <img
                                v-if="pwd.category === 'login' && loginLogoUrlByItemId[String(pwd.id)]"
                                :src="loginLogoUrlByItemId[String(pwd.id)]"
                                :alt="`${pwd.name} logo`"
                                class="h-6 w-6 rounded-sm object-contain"
                                loading="lazy"
                                @error="markWebsiteLogoAsUnavailable(pwd.id)"
                            >
                            <Key v-else-if="pwd.category === 'login'" class="h-6 w-6 text-blue-600" />
                            <CreditCard v-else-if="pwd.category === 'card'" class="h-6 w-6 text-blue-600" />
                            <IdCard v-else-if="pwd.category === 'identity'" class="h-6 w-6 text-blue-600" />
                            <FileText v-else class="h-6 w-6 text-blue-600" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="mb-1 flex items-center gap-2">
                                <h3 class="truncate font-semibold text-on-surface">{{ pwd.name }}</h3>
                                <Star v-if="pwd.favorite" class="h-4 w-4 flex-shrink-0 fill-yellow-500 text-yellow-500" />
                                <div
                                    v-if="pwd.strength === 'weak'"
                                    class="flex items-center gap-1 rounded bg-error-container px-2 py-0.5 text-xs font-medium text-red-600"
                                >
                                    <AlertCircle class="h-3 w-3" />
                                    Weak
                                </div>
                            </div>
                            <div class="flex items-center gap-4 text-sm text-on-surface-variant">
                                <span class="truncate">{{ pwd.username }}</span>
                                <template v-if="pwd.url">
                                    <span class="text-outline-variant">|</span>
                                    <span class="truncate">{{ pwd.url }}</span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 opacity-100 transition-opacity md:opacity-0 md:group-hover:opacity-100">
                        <button
                            class="rounded-lg p-2 transition-colors hover:bg-surface-container"
                            :title="props.visiblePasswords.has(pwd.id) ? 'Hide password' : 'Show password'"
                            @click="emit('toggle-password-visibility', pwd.id)"
                        >
                            <EyeOff v-if="props.visiblePasswords.has(pwd.id)" class="h-5 w-5 text-on-surface-variant" />
                            <Eye v-else class="h-5 w-5 text-on-surface-variant" />
                        </button>
                        <button
                            class="rounded-lg p-2 transition-colors hover:bg-surface-container"
                            title="Copy password"
                            @click="emit('copy-password', pwd.password)"
                        >
                            <Copy class="h-5 w-5 text-on-surface-variant" />
                        </button>
                        <button
                            class="rounded-lg p-2 transition-colors hover:bg-surface-container"
                            :title="pwd.favorite ? 'Remove favorite' : 'Add favorite'"
                            @click="emit('toggle-favorite', pwd)"
                        >
                            <Star
                                class="h-5 w-5"
                                :class="pwd.favorite ? 'fill-yellow-500 text-yellow-500' : 'text-on-surface-variant'"
                            />
                        </button>
                        <button
                            class="rounded-lg p-2 transition-colors hover:bg-surface-container"
                            title="Delete item"
                            @click="emit('delete-item', pwd.id)"
                        >
                            <Trash2 class="h-5 w-5 text-on-surface-variant" />
                        </button>
                    </div>
                </div>

                <Transition
                    enter-active-class="transition-all duration-200 ease-out"
                    enter-from-class="max-h-0 opacity-0"
                    enter-to-class="max-h-32 opacity-100"
                    leave-active-class="transition-all duration-150 ease-in"
                    leave-from-class="max-h-32 opacity-100"
                    leave-to-class="max-h-0 opacity-0"
                >
                    <div
                        v-if="props.visiblePasswords.has(pwd.id)"
                        class="mt-4 ml-16 overflow-hidden rounded-lg bg-surface-container-low p-4"
                    >
                        <p class="mb-1 text-sm font-medium text-on-surface-variant">Password</p>
                        <p class="font-mono text-on-surface">{{ pwd.password }}</p>
                    </div>
                </Transition>
            </div>
        </div>
    </section>

    <section class="mt-8 overflow-hidden rounded-2xl border border-outline-variant bg-surface">
        <div class="border-b border-outline-variant p-6">
            <h2 class="text-xl font-semibold tracking-tight text-on-surface">Recent Activity</h2>
        </div>
        <div class="space-y-4 p-6">
            <div v-for="pwd in props.passwords.slice(0, 5)" :key="`recent-${pwd.id}`" class="flex items-center gap-4">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-secondary-container">
                    <Clock class="h-5 w-5 text-blue-600" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-medium text-on-surface">{{ pwd.name }}</p>
                    <p class="text-sm text-on-surface-variant">Last used {{ pwd.lastUsed }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
