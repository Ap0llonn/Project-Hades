<script setup>
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import {
    AlertCircle,
    Clock,
    Copy,
    CreditCard,
    Eye,
    EyeOff,
    FileText,
    Key,
    MoreVertical,
    Plus,
    Search,
    Shield,
    Star,
    Users,
    Zap,
} from 'lucide-vue-next';

const searchQuery = ref('');
const selectedCategory = ref('all');
const visiblePasswords = ref(new Set());
const showAddModal = ref(false);

const passwords = [
    {
        id: '1',
        name: 'Facebook',
        username: 'john.doe@email.com',
        password: 'Fb@2024SecurePass!',
        url: 'facebook.com',
        category: 'login',
        favorite: true,
        lastUsed: '2 hours ago',
        strength: 'strong',
    },
    {
        id: '2',
        name: 'Gmail',
        username: 'john.doe@gmail.com',
        password: 'Gmail#Secure2024',
        url: 'mail.google.com',
        category: 'login',
        favorite: true,
        lastUsed: '5 hours ago',
        strength: 'strong',
    },
    {
        id: '3',
        name: 'Netflix',
        username: 'john.doe@email.com',
        password: 'NetflixPass123',
        url: 'netflix.com',
        category: 'login',
        favorite: false,
        lastUsed: '1 day ago',
        strength: 'medium',
    },
    {
        id: '4',
        name: 'LinkedIn',
        username: 'john.doe',
        password: 'LinkedInSecure!2024',
        url: 'linkedin.com',
        category: 'login',
        favorite: false,
        lastUsed: '2 days ago',
        strength: 'strong',
    },
    {
        id: '5',
        name: 'Amazon',
        username: 'john.doe@email.com',
        password: 'amazon123',
        url: 'amazon.com',
        category: 'login',
        favorite: false,
        lastUsed: '3 days ago',
        strength: 'weak',
    },
    {
        id: '6',
        name: 'Visa Credit Card',
        username: '**** **** **** 4532',
        password: '•••',
        url: '',
        category: 'card',
        favorite: false,
        lastUsed: '1 week ago',
        strength: 'strong',
    },
];

const filteredPasswords = computed(() =>
    passwords.filter((pwd) => {
        const query = searchQuery.value.toLowerCase();
        const matchesSearch =
            pwd.name.toLowerCase().includes(query) ||
            pwd.username.toLowerCase().includes(query) ||
            pwd.url.toLowerCase().includes(query);
        const matchesCategory =
            selectedCategory.value === 'all' ||
            (selectedCategory.value === 'favorites' && pwd.favorite) ||
            selectedCategory.value === pwd.category;

        return matchesSearch && matchesCategory;
    }),
);

const favoriteCount = computed(() => passwords.filter((p) => p.favorite).length);
const loginCount = computed(() => passwords.filter((p) => p.category === 'login').length);
const cardCount = computed(() => passwords.filter((p) => p.category === 'card').length);
const noteCount = computed(() => passwords.filter((p) => p.category === 'note').length);
const weakPasswords = computed(() => passwords.filter((p) => p.strength === 'weak').length);
const securityScore = 78;
const reusedPasswords = 2;
const breachedPasswords = 0;

const categoryTitle = computed(() => {
    if (selectedCategory.value === 'all') {
        return 'All Items';
    }
    if (selectedCategory.value === 'favorites') {
        return 'Favorites';
    }
    if (selectedCategory.value === 'login') {
        return 'Logins';
    }
    if (selectedCategory.value === 'card') {
        return 'Cards';
    }

    return 'Secure Notes';
});

const togglePasswordVisibility = (id) => {
    const next = new Set(visiblePasswords.value);
    if (next.has(id)) {
        next.delete(id);
    } else {
        next.add(id);
    }
    visiblePasswords.value = next;
};

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
    } catch {
        // Silent failure for unsupported clipboard contexts.
    }
};
</script>

<template>
    <Head title="Dashboard | VaultGuardian" />

    <DashboardLayout
        :selected-category="selectedCategory"
        :total-count="passwords.length"
        :favorite-count="favoriteCount"
        :login-count="loginCount"
        :card-count="cardCount"
        :note-count="noteCount"
        @update:selected-category="selectedCategory = $event"
    >
        <header class="sticky top-0 z-10 border-b border-outline-variant bg-surface">
                <div class="px-6 py-5 md:px-8">
                    <div class="mb-5 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <h1 class="text-3xl font-semibold tracking-tight text-on-surface">My Vault</h1>
                            <p class="text-on-surface-variant">Manage your passwords and secure information</p>
                        </div>
                        <button
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white shadow-sm transition-all hover:bg-blue-700"
                            @click="showAddModal = true"
                        >
                            <Plus class="h-5 w-5" />
                            Add Item
                        </button>
                    </div>

                    <div class="relative">
                        <Search class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-on-surface-variant" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search passwords, usernames, or websites..."
                            class="w-full rounded-lg border border-outline-variant bg-surface py-3 pl-12 pr-4 text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                            style="font-family: 'DM Sans', sans-serif"
                        >
                    </div>
                </div>
            </header>

            <div class="p-6 md:p-8">
                <section class="mb-8">
                    <div class="rounded-2xl border border-outline-variant bg-gradient-to-br from-surface-container to-surface-container-high p-8">
                        <div class="mb-6 flex items-start justify-between">
                            <div>
                                <h2 class="mb-2 text-2xl font-semibold tracking-tight text-on-surface">Security Health Score</h2>
                                <p class="text-on-surface-variant">Your overall password security rating</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-primary bg-surface">
                                    <span class="text-2xl font-bold text-blue-600">{{ securityScore }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="rounded-xl border border-outline-variant bg-surface p-5">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-error-container">
                                        <AlertCircle class="h-5 w-5 text-red-600" />
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-on-surface">{{ weakPasswords }}</p>
                                        <p class="text-sm text-on-surface-variant">Weak passwords</p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-outline-variant bg-surface p-5">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-container">
                                        <Copy class="h-5 w-5 text-primary" />
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-on-surface">{{ reusedPasswords }}</p>
                                        <p class="text-sm text-on-surface-variant">Reused passwords</p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-outline-variant bg-surface p-5">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-tertiary-fixed">
                                        <Shield class="h-5 w-5 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-on-surface">{{ breachedPasswords }}</p>
                                        <p class="text-sm text-on-surface-variant">Breached accounts</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <button class="group flex items-center gap-4 rounded-xl border border-outline-variant bg-surface p-6 text-left transition-all hover:border-primary">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed transition-transform group-hover:scale-110">
                            <Key class="h-6 w-6 text-blue-600" />
                        </div>
                        <div>
                            <p class="mb-1 font-semibold text-on-surface">Generate Password</p>
                            <p class="text-sm text-on-surface-variant">Create a strong password</p>
                        </div>
                    </button>

                    <button class="group flex items-center gap-4 rounded-xl border border-outline-variant bg-surface p-6 text-left transition-all hover:border-primary">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed transition-transform group-hover:scale-110">
                            <Zap class="h-6 w-6 text-indigo-600" />
                        </div>
                        <div>
                            <p class="mb-1 font-semibold text-on-surface">Import Passwords</p>
                            <p class="text-sm text-on-surface-variant">From browser or file</p>
                        </div>
                    </button>

                    <button class="group flex items-center gap-4 rounded-xl border border-outline-variant bg-surface p-6 text-left transition-all hover:border-primary">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed transition-transform group-hover:scale-110">
                            <Users class="h-6 w-6 text-green-600" />
                        </div>
                        <div>
                            <p class="mb-1 font-semibold text-on-surface">Share Vault</p>
                            <p class="text-sm text-on-surface-variant">With family or team</p>
                        </div>
                    </button>
                </section>

                <section class="overflow-hidden rounded-2xl border border-outline-variant bg-surface">
                    <div class="border-b border-outline-variant p-6">
                        <h2 class="text-xl font-semibold tracking-tight text-on-surface">{{ categoryTitle }}</h2>
                        <p class="mt-1 text-sm text-on-surface-variant">{{ filteredPasswords.length }} items</p>
                    </div>

                    <div class="divide-y divide-outline-variant">
                        <div v-if="filteredPasswords.length === 0" class="p-12 text-center">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-surface-container-low">
                                <Search class="h-8 w-8 text-on-surface-variant" />
                            </div>
                            <p class="mb-2 font-medium text-on-surface-variant">No items found</p>
                            <p class="text-sm text-on-surface-variant">Try adjusting your search or filters</p>
                        </div>

                        <div v-for="pwd in filteredPasswords" :key="pwd.id" class="group p-6 transition-colors hover:bg-surface-container-low">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex min-w-0 flex-1 items-center gap-4">
                                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed">
                                        <Key v-if="pwd.category === 'login'" class="h-6 w-6 text-blue-600" />
                                        <CreditCard v-else-if="pwd.category === 'card'" class="h-6 w-6 text-blue-600" />
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
                                                <span class="text-outline-variant">•</span>
                                                <span class="truncate">{{ pwd.url }}</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 opacity-100 transition-opacity md:opacity-0 md:group-hover:opacity-100">
                                    <button
                                        class="rounded-lg p-2 transition-colors hover:bg-surface-container"
                                        :title="visiblePasswords.has(pwd.id) ? 'Hide password' : 'Show password'"
                                        @click="togglePasswordVisibility(pwd.id)"
                                    >
                                        <EyeOff v-if="visiblePasswords.has(pwd.id)" class="h-5 w-5 text-on-surface-variant" />
                                        <Eye v-else class="h-5 w-5 text-on-surface-variant" />
                                    </button>
                                    <button
                                        class="rounded-lg p-2 transition-colors hover:bg-surface-container"
                                        title="Copy password"
                                        @click="copyToClipboard(pwd.password)"
                                    >
                                        <Copy class="h-5 w-5 text-on-surface-variant" />
                                    </button>
                                    <button class="rounded-lg p-2 transition-colors hover:bg-surface-container">
                                        <MoreVertical class="h-5 w-5 text-on-surface-variant" />
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
                                    v-if="visiblePasswords.has(pwd.id)"
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
                        <div v-for="pwd in passwords.slice(0, 5)" :key="`recent-${pwd.id}`" class="flex items-center gap-4">
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
            </div>

        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showAddModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                @click.self="showAddModal = false"
            >
                <div class="w-full max-w-md rounded-2xl border border-outline-variant bg-surface p-8">
                    <h2 class="mb-6 text-2xl font-semibold tracking-tight text-on-surface">Add New Item</h2>
                    <div class="space-y-4">
                        <button class="flex w-full items-center gap-4 rounded-xl border border-outline-variant p-4 text-left transition-all hover:border-primary">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-container">
                                <Key class="h-6 w-6 text-blue-600" />
                            </div>
                            <div>
                                <p class="font-semibold text-on-surface">Login</p>
                                <p class="text-sm text-on-surface-variant">Username and password</p>
                            </div>
                        </button>
                        <button class="flex w-full items-center gap-4 rounded-xl border border-outline-variant p-4 text-left transition-all hover:border-primary">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-tertiary-fixed">
                                <CreditCard class="h-6 w-6 text-indigo-600" />
                            </div>
                            <div>
                                <p class="font-semibold text-on-surface">Credit Card</p>
                                <p class="text-sm text-on-surface-variant">Card details</p>
                            </div>
                        </button>
                        <button class="flex w-full items-center gap-4 rounded-xl border border-outline-variant p-4 text-left transition-all hover:border-primary">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-tertiary-fixed">
                                <FileText class="h-6 w-6 text-green-600" />
                            </div>
                            <div>
                                <p class="font-semibold text-on-surface">Secure Note</p>
                                <p class="text-sm text-on-surface-variant">Text information</p>
                            </div>
                        </button>
                    </div>
                    <button
                        class="mt-6 w-full rounded-lg border border-outline-variant px-6 py-3 font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-low"
                        @click="showAddModal = false"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </Transition>
    </DashboardLayout>
</template>
