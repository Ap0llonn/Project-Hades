<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import { useModal } from '../../../shared/modal';
import { openCardItemModal } from '../components/item-modals/cardItemModalForm';
import { openIdentityItemModal } from '../components/item-modals/identityItemModalForm';
import { openLoginItemModal } from '../components/item-modals/loginItemModalForm';
import { openNoteItemModal } from '../components/item-modals/noteItemModalForm';
import {
    AlertCircle,
    Archive,
    ChevronDown,
    Clock,
    Copy,
    CreditCard,
    Eye,
    EyeOff,
    FileText,
    Folder,
    FolderPlus,
    Globe,
    HelpCircle,
    IdCard,
    Key,
    MoreVertical,
    Plus,
    Search,
    Shield,
    Star,
    Trash2,
    User,
    Users,
    Zap,
} from 'lucide-vue-next';

const searchQuery = ref('');
const selectedCategory = ref('all');
const visiblePasswords = ref(new Set());
const showAddDropdown = ref(false);
const addMenuRef = ref(null);
const generatorLength = ref(20);
const generatedPassword = ref('');
const allItemsVaultFilter = ref('all-vaults');
const allItemsTypeFilter = ref('all-items');
const allItemsFolderFilter = ref('no-folder');
const allItemsLifecycleFilter = ref('active');
const modal = useModal();

const vaultCategories = ['all', 'favorites', 'login', 'card', 'note', 'identity'];

const generatePassword = (length) => {
    const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{};:,.?/|';
    let output = '';

    for (let index = 0; index < length; index += 1) {
        const randomIndex = Math.floor(Math.random() * charset.length);
        output += charset[randomIndex];
    }

    return output;
};

const regenerateGeneratedPassword = () => {
    generatedPassword.value = generatePassword(generatorLength.value);
};

const passwords = ref([
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
        status: 'active',
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
        status: 'active',
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
        status: 'active',
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
        status: 'active',
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
        status: 'active',
    },
    {
        id: '6',
        name: 'Visa Credit Card',
        username: '**** **** **** 4532',
        password: '***',
        url: '',
        category: 'card',
        favorite: false,
        lastUsed: '1 week ago',
        strength: 'strong',
        status: 'active',
    },
]);

const filteredPasswords = computed(() =>
    passwords.value.filter((pwd) => {
        const query = searchQuery.value.toLowerCase();
        const matchesSearch =
            pwd.name.toLowerCase().includes(query) ||
            pwd.username.toLowerCase().includes(query) ||
            pwd.url.toLowerCase().includes(query);
        const matchesCategory =
            selectedCategory.value === 'all' ||
            (selectedCategory.value === 'favorites' && pwd.favorite) ||
            selectedCategory.value === pwd.category;
        const matchesAllItemsType =
            allItemsTypeFilter.value === 'all-items' ||
            (allItemsTypeFilter.value === 'favorites' && pwd.favorite) ||
            allItemsTypeFilter.value === pwd.category;
        const matchesAllItemsLifecycle =
            allItemsLifecycleFilter.value === 'all' || (pwd.status ?? 'active') === allItemsLifecycleFilter.value;
        const matchesAllItemsFilters =
            selectedCategory.value !== 'all' || (matchesAllItemsType && matchesAllItemsLifecycle);

        return matchesSearch && matchesCategory && matchesAllItemsFilters;
    }),
);

const favoriteCount = computed(() => passwords.value.filter((p) => p.favorite).length);
const loginCount = computed(() => passwords.value.filter((p) => p.category === 'login').length);
const cardCount = computed(() => passwords.value.filter((p) => p.category === 'card').length);
const noteCount = computed(() => passwords.value.filter((p) => p.category === 'note').length);
const weakPasswords = computed(() => passwords.value.filter((p) => p.strength === 'weak').length);
const securityScore = 78;
const reusedPasswords = 2;
const breachedPasswords = 0;
const securityAlertCount = computed(() => weakPasswords.value + reusedPasswords + breachedPasswords);
const isVaultCategory = computed(() => vaultCategories.includes(selectedCategory.value));
const isSecurityCenter = computed(() => selectedCategory.value === 'security-center');
const isPasswordGenerator = computed(() => selectedCategory.value === 'password-generator');
const isImportExport = computed(() => selectedCategory.value === 'import-export');
const isPasswordSharing = computed(() => selectedCategory.value === 'password-sharing');

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
    if (selectedCategory.value === 'identity') {
        return 'Identity';
    }
    if (selectedCategory.value === 'security-center') {
        return 'Security Center';
    }
    if (selectedCategory.value === 'password-generator') {
        return 'Password Generator';
    }
    if (selectedCategory.value === 'import-export') {
        return 'Import / Export';
    }
    if (selectedCategory.value === 'password-sharing') {
        return 'Password Sharing';
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

const closeAddModal = () => {
    modal.dismiss();
};

const toggleAddDropdown = () => {
    showAddDropdown.value = !showAddDropdown.value;
};

const openAddItemModal = (type) => {
    showAddDropdown.value = false;
    if (type === 'login') {
        openLoginItemModal(modal, saveNewItem);
        return;
    }
    if (type === 'card') {
        openCardItemModal(modal, saveNewItem);
        return;
    }
    if (type === 'identity') {
        openIdentityItemModal(modal, saveNewItem);
        return;
    }

    openNoteItemModal(modal, saveNewItem);
};

const closeAddDropdown = () => {
    showAddDropdown.value = false;
};

const handleClickOutsideAddMenu = (event) => {
    const target = event.target;
    if (!(target instanceof Node)) {
        return;
    }

    if (addMenuRef.value && !addMenuRef.value.contains(target)) {
        closeAddDropdown();
    }
};

const passwordStrength = (password) => {
    if (password.length < 8) {
        return 'weak';
    }

    const hasLower = /[a-z]/.test(password);
    const hasUpper = /[A-Z]/.test(password);
    const hasDigit = /\d/.test(password);
    const hasSymbol = /[^A-Za-z0-9]/.test(password);

    return hasLower && hasUpper && hasDigit && hasSymbol ? 'strong' : 'medium';
};

const saveNewItem = (item) => {
    const itemName = (item.name ?? '').trim();
    const itemType = item.type ?? 'note';
    const cardLastFour = (item.cardNumber ?? '').replace(/\s+/g, '').slice(-4);
    const identityLabel = (item.email ?? '').trim() || (item.fullName ?? '').trim() || 'Identity';

    const newItem = {
        id: `${Date.now()}`,
        name: itemName,
        username:
            itemType === 'login'
                ? (item.username ?? '').trim()
                : itemType === 'card'
                  ? `**** **** **** ${cardLastFour || '0000'}`
                  : itemType === 'identity'
                    ? identityLabel
                    : 'Secure note',
        password: itemType === 'login' ? (item.password ?? '') : '***',
        url: itemType === 'login' ? (item.url ?? '').trim() : '',
        category: itemType,
        favorite: Boolean(item.favorite),
        note: (item.note ?? '').trim(),
        requiresMasterPasswordForNote: Boolean(item.requireMasterPassword),
        lastUsed: 'just now',
        strength: itemType === 'login' ? passwordStrength(item.password ?? '') : 'strong',
        status: 'active',
    };

    passwords.value.unshift(newItem);
    selectedCategory.value = itemType;
    closeAddModal();
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutsideAddMenu);
});

onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleClickOutsideAddMenu);
});

regenerateGeneratedPassword();
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
        :security-alert-count="securityAlertCount"
        @update:selected-category="selectedCategory = $event"
    >
        <header class="sticky top-0 z-10 border-b border-outline-variant bg-surface">
                <div class="px-6 py-5 md:px-8">
                    <div class="mb-5 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <div>
                            <h1 class="text-3xl font-semibold tracking-tight text-on-surface">My Vault</h1>
                            <p class="text-on-surface-variant">Manage your passwords and secure information</p>
                        </div>
                        <div ref="addMenuRef" class="relative">
                            <button
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white shadow-sm transition-all hover:bg-blue-700"
                                @click="toggleAddDropdown"
                            >
                                <Plus class="h-5 w-5" />
                                Add Item
                                <ChevronDown class="h-4 w-4" />
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
                                    v-if="showAddDropdown"
                                    class="absolute right-0 top-full z-40 mt-2 w-64 overflow-hidden rounded-xl border border-outline-variant bg-surface shadow-lg"
                                >
                                    <button
                                        type="button"
                                        class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-surface-container-low"
                                        @click="openAddItemModal('login')"
                                    >
                                        <Key class="h-4 w-4 text-primary" />
                                        <span class="font-medium text-on-surface">Login</span>
                                    </button>
                                    <button
                                        type="button"
                                        class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-surface-container-low"
                                        @click="openAddItemModal('card')"
                                    >
                                        <CreditCard class="h-4 w-4 text-primary" />
                                        <span class="font-medium text-on-surface">Credit Card</span>
                                    </button>
                                    <button
                                        type="button"
                                        class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-surface-container-low"
                                        @click="openAddItemModal('note')"
                                    >
                                        <FileText class="h-4 w-4 text-primary" />
                                        <span class="font-medium text-on-surface">Note</span>
                                    </button>
                                    <button
                                        type="button"
                                        class="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-surface-container-low"
                                        @click="openAddItemModal('identity')"
                                    >
                                        <IdCard class="h-4 w-4 text-primary" />
                                        <span class="font-medium text-on-surface">Identity</span>
                                    </button>
                                </div>
                            </Transition>
                        </div>
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
                <div :class="selectedCategory === 'all' ? 'grid grid-cols-1 gap-8 lg:grid-cols-[320px_minmax(0,1fr)] lg:items-start' : ''">
                    <aside
                        v-if="selectedCategory === 'all'"
                        class="self-start lg:sticky lg:top-28 lg:max-h-[calc(100vh-8rem)] lg:overflow-y-auto"
                    >
                        <div class="overflow-hidden rounded-xl border border-outline-variant bg-surface">
                            <div class="flex items-center justify-between border-b border-outline-variant px-5 py-4">
                                <h2 class="text-sm font-semibold uppercase tracking-wide text-on-surface">Filters</h2>
                                <button type="button" class="rounded p-1 text-on-surface-variant transition-colors hover:bg-surface-container-low hover:text-on-surface" title="Filter help">
                                    <HelpCircle class="h-4 w-4" />
                                </button>
                            </div>

                            <div class="space-y-5 p-5">
                                <div class="relative">
                                    <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-on-surface-variant" />
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Search in vault"
                                        class="w-full rounded-lg border border-outline-variant bg-surface py-2.5 pl-10 pr-3 text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                    >
                                </div>

                                <div>
                                    <p class="mb-2 text-lg font-semibold text-primary">All Vaults</p>
                                    <div class="space-y-1 text-on-surface">
                                        <button
                                            type="button"
                                            class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low"
                                            @click="allItemsVaultFilter = 'my-vault'"
                                        >
                                            <User class="h-4 w-4" />
                                            <span>My Vault</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low"
                                        >
                                            <FolderPlus class="h-4 w-4" />
                                            <span>New Organization</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="border-t border-outline-variant pt-4">
                                    <p class="mb-2 text-lg font-semibold text-primary">All Items</p>
                                    <div class="space-y-1 text-on-surface">
                                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="allItemsTypeFilter = 'favorites'">
                                            <Star class="h-4 w-4" />
                                            <span>Favorites</span>
                                        </button>
                                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="allItemsTypeFilter = 'login'">
                                            <Globe class="h-4 w-4" />
                                            <span>Login</span>
                                        </button>
                                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="allItemsTypeFilter = 'card'">
                                            <CreditCard class="h-4 w-4" />
                                            <span>Payment Card</span>
                                        </button>
                                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="allItemsTypeFilter = 'identity'">
                                            <IdCard class="h-4 w-4" />
                                            <span>Identity</span>
                                        </button>
                                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="allItemsTypeFilter = 'note'">
                                            <FileText class="h-4 w-4" />
                                            <span>Note</span>
                                        </button>
                                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="allItemsTypeFilter = 'ssh'">
                                            <Key class="h-4 w-4" />
                                            <span>SSH Key</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="border-t border-outline-variant pt-4">
                                    <p class="mb-2 text-lg font-semibold text-on-surface-variant">Folders</p>
                                    <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="allItemsFolderFilter = 'no-folder'">
                                        <Folder class="h-4 w-4" />
                                        <span>No Folder</span>
                                    </button>
                                </div>

                                <div class="border-t border-outline-variant pt-4">
                                    <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="allItemsLifecycleFilter = 'archived'">
                                        <Archive class="h-4 w-4" />
                                        <span>Archive</span>
                                    </button>
                                    <button type="button" class="mt-1 flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="allItemsLifecycleFilter = 'trash'">
                                        <Trash2 class="h-4 w-4" />
                                        <span>Trash</span>
                                    </button>
                                    <button type="button" class="mt-3 rounded-md border border-outline-variant px-3 py-1.5 text-sm font-medium text-on-surface-variant transition-colors hover:bg-surface-container-low hover:text-on-surface" @click="allItemsTypeFilter = 'all-items'; allItemsLifecycleFilter = 'active'; allItemsFolderFilter = 'no-folder'; allItemsVaultFilter = 'all-vaults'">
                                        Reset filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <div :class="selectedCategory === 'all' ? 'min-w-0' : ''">
                <section v-if="isVaultCategory || isSecurityCenter" class="mb-8">
                    <div class="rounded-2xl border border-outline-variant bg-gradient-to-br from-surface-container to-surface-container-high p-8">
                        <div class="mb-6 flex items-start justify-between">
                            <div>
                                <h2 class="mb-2 text-2xl font-semibold tracking-tight text-on-surface">Security Health Score</h2>
                                <p class="text-on-surface-variant">Your overall password security rating</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-primary bg-surface">
                                    <span class="text-2xl font-bold text-primary">{{ securityScore }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="rounded-xl border border-outline-variant bg-surface p-5">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-error-container">
                                        <AlertCircle class="h-5 w-5 text-on-surface" />
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
                                        <Shield class="h-5 w-5 text-primary" />
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

                <section v-if="isSecurityCenter" class="mb-8 overflow-hidden rounded-2xl border border-outline-variant bg-surface">
                    <div class="border-b border-outline-variant p-6">
                        <h2 class="text-xl font-semibold tracking-tight text-on-surface">{{ categoryTitle }}</h2>
                        <p class="mt-1 text-sm text-on-surface-variant">Actionable recommendations to improve your vault security</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
                        <article class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                            <p class="text-sm font-medium text-on-surface-variant">Weak Passwords</p>
                            <p class="mt-2 text-3xl font-semibold text-on-surface">{{ weakPasswords }}</p>
                            <p class="mt-2 text-sm text-on-surface-variant">Update weak credentials with strong, unique passwords.</p>
                        </article>
                        <article class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                            <p class="text-sm font-medium text-on-surface-variant">Reused Passwords</p>
                            <p class="mt-2 text-3xl font-semibold text-on-surface">{{ reusedPasswords }}</p>
                            <p class="mt-2 text-sm text-on-surface-variant">Reduce risk by replacing reused credentials.</p>
                        </article>
                        <article class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                            <p class="text-sm font-medium text-on-surface-variant">Breached Accounts</p>
                            <p class="mt-2 text-3xl font-semibold text-on-surface">{{ breachedPasswords }}</p>
                            <p class="mt-2 text-sm text-on-surface-variant">No alerts now. Keep breach monitoring enabled.</p>
                        </article>
                    </div>
                </section>

                <section v-if="isPasswordGenerator" class="mb-8 overflow-hidden rounded-2xl border border-outline-variant bg-surface">
                    <div class="border-b border-outline-variant p-6">
                        <h2 class="text-xl font-semibold tracking-tight text-on-surface">{{ categoryTitle }}</h2>
                        <p class="mt-1 text-sm text-on-surface-variant">Generate high-entropy passwords for your accounts</p>
                    </div>
                    <div class="space-y-6 p-6">
                        <div class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <label for="generatorLength" class="text-sm font-medium text-on-surface-variant">
                                    Length: <span class="font-semibold text-on-surface">{{ generatorLength }}</span>
                                </label>
                                <button
                                    type="button"
                                    class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container"
                                    @click="regenerateGeneratedPassword"
                                >
                                    Generate New
                                </button>
                            </div>
                            <input
                                id="generatorLength"
                                v-model.number="generatorLength"
                                type="range"
                                min="12"
                                max="40"
                                class="w-full accent-primary"
                                @input="regenerateGeneratedPassword"
                            >
                            <div class="mt-4 rounded-lg border border-outline-variant bg-surface px-4 py-3">
                                <p class="text-xs uppercase tracking-wider text-on-surface-variant">Generated Password</p>
                                <p class="mt-2 break-all font-mono text-on-surface">{{ generatedPassword }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-low hover:text-on-surface"
                            @click="copyToClipboard(generatedPassword)"
                        >
                            Copy Password
                        </button>
                    </div>
                </section>

                <section v-if="isImportExport" class="mb-8 overflow-hidden rounded-2xl border border-outline-variant bg-surface">
                    <div class="border-b border-outline-variant p-6">
                        <h2 class="text-xl font-semibold tracking-tight text-on-surface">{{ categoryTitle }}</h2>
                        <p class="mt-1 text-sm text-on-surface-variant">Move your vault data securely between formats</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2">
                        <article class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                            <p class="text-lg font-semibold text-on-surface">Import</p>
                            <p class="mt-2 text-sm text-on-surface-variant">Supported formats: CSV, JSON, browser exports.</p>
                            <button type="button" class="mt-4 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container">
                                Start Import
                            </button>
                        </article>
                        <article class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                            <p class="text-lg font-semibold text-on-surface">Export</p>
                            <p class="mt-2 text-sm text-on-surface-variant">Create encrypted backups for secure archival.</p>
                            <button type="button" class="mt-4 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container">
                                Create Export
                            </button>
                        </article>
                    </div>
                </section>

                <section v-if="isPasswordSharing" class="mb-8 overflow-hidden rounded-2xl border border-outline-variant bg-surface">
                    <div class="border-b border-outline-variant p-6">
                        <h2 class="text-xl font-semibold tracking-tight text-on-surface">{{ categoryTitle }}</h2>
                        <p class="mt-1 text-sm text-on-surface-variant">Share credentials securely with trusted people</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2">
                        <article class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                            <p class="text-lg font-semibold text-on-surface">Shared Vault Access</p>
                            <p class="mt-2 text-sm text-on-surface-variant">Grant role-based access without exposing plain passwords.</p>
                            <button type="button" class="mt-4 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container">
                                Invite Member
                            </button>
                        </article>
                        <article class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                            <p class="text-lg font-semibold text-on-surface">One-Time Secure Link</p>
                            <p class="mt-2 text-sm text-on-surface-variant">Send temporary links that automatically expire.</p>
                            <button type="button" class="mt-4 rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface">
                                Create Link
                            </button>
                        </article>
                    </div>
                </section>

                <section v-if="isVaultCategory" class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <button
                        class="group flex items-center gap-4 rounded-xl border border-outline-variant bg-surface p-6 text-left transition-all hover:border-primary"
                        @click="selectedCategory = 'password-generator'"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed transition-transform group-hover:scale-110">
                            <Key class="h-6 w-6 text-primary" />
                        </div>
                        <div>
                            <p class="mb-1 font-semibold text-on-surface">Generate Password</p>
                            <p class="text-sm text-on-surface-variant">Create a strong password</p>
                        </div>
                    </button>

                    <button
                        class="group flex items-center gap-4 rounded-xl border border-outline-variant bg-surface p-6 text-left transition-all hover:border-primary"
                        @click="selectedCategory = 'import-export'"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed transition-transform group-hover:scale-110">
                            <Zap class="h-6 w-6 text-primary" />
                        </div>
                        <div>
                            <p class="mb-1 font-semibold text-on-surface">Import / Export</p>
                            <p class="text-sm text-on-surface-variant">Move your vault data securely</p>
                        </div>
                    </button>

                    <button
                        class="group flex items-center gap-4 rounded-xl border border-outline-variant bg-surface p-6 text-left transition-all hover:border-primary"
                        @click="selectedCategory = 'password-sharing'"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed transition-transform group-hover:scale-110">
                            <Users class="h-6 w-6 text-primary" />
                        </div>
                        <div>
                            <p class="mb-1 font-semibold text-on-surface">Share Passwords</p>
                            <p class="text-sm text-on-surface-variant">With family or team</p>
                        </div>
                    </button>
                </section>

                <section v-if="isVaultCategory" class="overflow-hidden rounded-2xl border border-outline-variant bg-surface">
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

                <section v-if="isVaultCategory" class="mt-8 overflow-hidden rounded-2xl border border-outline-variant bg-surface">
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
                </div>
            </div>

    </DashboardLayout>
</template>
