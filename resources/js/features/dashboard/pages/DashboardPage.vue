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
        <header class="sticky top-0 z-10 border-b border-gray-200 bg-white">
                <div class="px-6 py-5 md:px-8">
                    <div class="mb-5 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <h1 class="text-3xl font-semibold tracking-tight text-gray-900">My Vault</h1>
                            <p class="text-gray-600">Manage your passwords and secure information</p>
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
                        <Search class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search passwords, usernames, or websites..."
                            class="w-full rounded-lg border border-gray-300 py-3 pl-12 pr-4 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                            style="font-family: 'DM Sans', sans-serif"
                        >
                    </div>
                </div>
            </header>

            <div class="p-6 md:p-8">
                <section class="mb-8">
                    <div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100/50 p-8">
                        <div class="mb-6 flex items-start justify-between">
                            <div>
                                <h2 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900">Security Health Score</h2>
                                <p class="text-gray-600">Your overall password security rating</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-blue-600 bg-white">
                                    <span class="text-2xl font-bold text-blue-600">{{ securityScore }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50">
                                        <AlertCircle class="h-5 w-5 text-red-600" />
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">{{ weakPasswords }}</p>
                                        <p class="text-sm text-gray-600">Weak passwords</p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50">
                                        <Copy class="h-5 w-5 text-yellow-600" />
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">{{ reusedPasswords }}</p>
                                        <p class="text-sm text-gray-600">Reused passwords</p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white p-5">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50">
                                        <Shield class="h-5 w-5 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">{{ breachedPasswords }}</p>
                                        <p class="text-sm text-gray-600">Breached accounts</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <button class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-6 text-left transition-all hover:border-blue-300">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-blue-100 to-blue-50 transition-transform group-hover:scale-110">
                            <Key class="h-6 w-6 text-blue-600" />
                        </div>
                        <div>
                            <p class="mb-1 font-semibold text-gray-900">Generate Password</p>
                            <p class="text-sm text-gray-600">Create a strong password</p>
                        </div>
                    </button>

                    <button class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-6 text-left transition-all hover:border-blue-300">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-100 to-indigo-50 transition-transform group-hover:scale-110">
                            <Zap class="h-6 w-6 text-indigo-600" />
                        </div>
                        <div>
                            <p class="mb-1 font-semibold text-gray-900">Import Passwords</p>
                            <p class="text-sm text-gray-600">From browser or file</p>
                        </div>
                    </button>

                    <button class="group flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-6 text-left transition-all hover:border-blue-300">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-green-100 to-green-50 transition-transform group-hover:scale-110">
                            <Users class="h-6 w-6 text-green-600" />
                        </div>
                        <div>
                            <p class="mb-1 font-semibold text-gray-900">Share Vault</p>
                            <p class="text-sm text-gray-600">With family or team</p>
                        </div>
                    </button>
                </section>

                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-xl font-semibold tracking-tight text-gray-900">{{ categoryTitle }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ filteredPasswords.length }} items</p>
                    </div>

                    <div class="divide-y divide-gray-200">
                        <div v-if="filteredPasswords.length === 0" class="p-12 text-center">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                <Search class="h-8 w-8 text-gray-400" />
                            </div>
                            <p class="mb-2 font-medium text-gray-600">No items found</p>
                            <p class="text-sm text-gray-500">Try adjusting your search or filters</p>
                        </div>

                        <div v-for="pwd in filteredPasswords" :key="pwd.id" class="group p-6 transition-colors hover:bg-gray-50">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex min-w-0 flex-1 items-center gap-4">
                                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-100 to-blue-50">
                                        <Key v-if="pwd.category === 'login'" class="h-6 w-6 text-blue-600" />
                                        <CreditCard v-else-if="pwd.category === 'card'" class="h-6 w-6 text-blue-600" />
                                        <FileText v-else class="h-6 w-6 text-blue-600" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="mb-1 flex items-center gap-2">
                                            <h3 class="truncate font-semibold text-gray-900">{{ pwd.name }}</h3>
                                            <Star v-if="pwd.favorite" class="h-4 w-4 flex-shrink-0 fill-yellow-500 text-yellow-500" />
                                            <div
                                                v-if="pwd.strength === 'weak'"
                                                class="flex items-center gap-1 rounded bg-red-50 px-2 py-0.5 text-xs font-medium text-red-600"
                                            >
                                                <AlertCircle class="h-3 w-3" />
                                                Weak
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            <span class="truncate">{{ pwd.username }}</span>
                                            <template v-if="pwd.url">
                                                <span class="text-gray-300">•</span>
                                                <span class="truncate">{{ pwd.url }}</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 opacity-100 transition-opacity md:opacity-0 md:group-hover:opacity-100">
                                    <button
                                        class="rounded-lg p-2 transition-colors hover:bg-gray-200"
                                        :title="visiblePasswords.has(pwd.id) ? 'Hide password' : 'Show password'"
                                        @click="togglePasswordVisibility(pwd.id)"
                                    >
                                        <EyeOff v-if="visiblePasswords.has(pwd.id)" class="h-5 w-5 text-gray-600" />
                                        <Eye v-else class="h-5 w-5 text-gray-600" />
                                    </button>
                                    <button
                                        class="rounded-lg p-2 transition-colors hover:bg-gray-200"
                                        title="Copy password"
                                        @click="copyToClipboard(pwd.password)"
                                    >
                                        <Copy class="h-5 w-5 text-gray-600" />
                                    </button>
                                    <button class="rounded-lg p-2 transition-colors hover:bg-gray-200">
                                        <MoreVertical class="h-5 w-5 text-gray-600" />
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
                                    class="mt-4 ml-16 overflow-hidden rounded-lg bg-gray-100 p-4"
                                >
                                    <p class="mb-1 text-sm font-medium text-gray-600">Password</p>
                                    <p class="font-mono text-gray-900">{{ pwd.password }}</p>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </section>

                <section class="mt-8 overflow-hidden rounded-2xl border border-gray-200 bg-white">
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-xl font-semibold tracking-tight text-gray-900">Recent Activity</h2>
                    </div>
                    <div class="space-y-4 p-6">
                        <div v-for="pwd in passwords.slice(0, 5)" :key="`recent-${pwd.id}`" class="flex items-center gap-4">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50">
                                <Clock class="h-5 w-5 text-blue-600" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-gray-900">{{ pwd.name }}</p>
                                <p class="text-sm text-gray-600">Last used {{ pwd.lastUsed }}</p>
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
                <div class="w-full max-w-md rounded-2xl bg-white p-8">
                    <h2 class="mb-6 text-2xl font-semibold tracking-tight text-gray-900">Add New Item</h2>
                    <div class="space-y-4">
                        <button class="flex w-full items-center gap-4 rounded-xl border border-gray-200 p-4 text-left transition-all hover:border-blue-300">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50">
                                <Key class="h-6 w-6 text-blue-600" />
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Login</p>
                                <p class="text-sm text-gray-600">Username and password</p>
                            </div>
                        </button>
                        <button class="flex w-full items-center gap-4 rounded-xl border border-gray-200 p-4 text-left transition-all hover:border-blue-300">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-50">
                                <CreditCard class="h-6 w-6 text-indigo-600" />
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Credit Card</p>
                                <p class="text-sm text-gray-600">Card details</p>
                            </div>
                        </button>
                        <button class="flex w-full items-center gap-4 rounded-xl border border-gray-200 p-4 text-left transition-all hover:border-blue-300">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-50">
                                <FileText class="h-6 w-6 text-green-600" />
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Secure Note</p>
                                <p class="text-sm text-gray-600">Text information</p>
                            </div>
                        </button>
                    </div>
                    <button
                        class="mt-6 w-full rounded-lg border border-gray-300 px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-50"
                        @click="showAddModal = false"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </Transition>
    </DashboardLayout>
</template>
