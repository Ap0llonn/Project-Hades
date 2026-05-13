<script setup>
import { computed, onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import DashboardFeaturePanelsSection from '../components/sections/DashboardFeaturePanelsSection.vue';
import DashboardFiltersSection from '../components/sections/DashboardFiltersSection.vue';
import DashboardHeaderSection from '../components/sections/DashboardHeaderSection.vue';
import DashboardQuickActionsSection from '../components/sections/DashboardQuickActionsSection.vue';
import DashboardSecurityHealthSection from '../components/sections/DashboardSecurityHealthSection.vue';
import DashboardVaultItemsSection from '../components/sections/DashboardVaultItemsSection.vue';
import { useDashboardServices } from '../composables/useDashboardServices';
import { openCardItemModal } from '../components/item-modals/cardItemModalForm';
import { openIdentityItemModal } from '../components/item-modals/identityItemModalForm';
import { openLoginItemModal } from '../components/item-modals/loginItemModalForm';
import { openNoteItemModal } from '../components/item-modals/noteItemModalForm';
import { useModal } from '../../../shared/modal';

const searchQuery = ref('');
const selectedCategory = ref('all');
const allItemsVaultFilter = ref('all-vaults');
const allItemsTypeFilter = ref('all-items');
const allItemsFolderFilter = ref('no-folder');
const allItemsLifecycleFilter = ref('active');
const generatorLength = ref(20);
const generatedPassword = ref('');
const modal = useModal();

const {
    passwords,
    visiblePasswords,
    loadServices,
    createService,
    deleteService,
    toggleFavorite,
    togglePasswordVisibility,
} = useDashboardServices();

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

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
    } catch {
        // Silent failure for unsupported clipboard contexts.
    }
};

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

const saveNewItem = async (item) => {
    const newItem = await createService(item);
    modal.dismiss();

    if (newItem !== null) {
        selectedCategory.value = newItem.category;
    }
};

const openAddItemModal = (type) => {
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

const requestDeleteService = (id) => {
    const targetItem = passwords.value.find((item) => String(item.id) === String(id));
    const itemName = (targetItem?.name ?? 'this item').trim() || 'this item';

    modal.danger({
        title: 'Delete service',
        message: `Delete "${itemName}" permanently? This action cannot be undone.`,
        confirmLabel: 'Delete',
        cancelLabel: 'Cancel',
        onConfirm: async () => {
            await deleteService(id);
        },
    });
};

onMounted(() => {
    loadServices().catch((error) => {
        console.error('Unable to load encrypted services.', error);
    });
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
        <DashboardHeaderSection
            v-model:search-query="searchQuery"
            @add-item="openAddItemModal"
        />

        <div class="p-6 md:p-8">
            <div :class="selectedCategory === 'all' ? 'grid grid-cols-1 gap-8 lg:grid-cols-[320px_minmax(0,1fr)] lg:items-start' : ''">
                <DashboardFiltersSection
                    v-if="selectedCategory === 'all'"
                    v-model:search-query="searchQuery"
                    v-model:all-items-vault-filter="allItemsVaultFilter"
                    v-model:all-items-type-filter="allItemsTypeFilter"
                    v-model:all-items-folder-filter="allItemsFolderFilter"
                    v-model:all-items-lifecycle-filter="allItemsLifecycleFilter"
                />

                <div :class="selectedCategory === 'all' ? 'min-w-0' : ''">
                    <DashboardSecurityHealthSection
                        v-if="isVaultCategory || isSecurityCenter"
                        :security-score="securityScore"
                        :weak-passwords="weakPasswords"
                        :reused-passwords="reusedPasswords"
                        :breached-passwords="breachedPasswords"
                    />

                    <DashboardFeaturePanelsSection
                        :is-security-center="isSecurityCenter"
                        :is-password-generator="isPasswordGenerator"
                        :is-import-export="isImportExport"
                        :is-password-sharing="isPasswordSharing"
                        :category-title="categoryTitle"
                        :weak-passwords="weakPasswords"
                        :reused-passwords="reusedPasswords"
                        :breached-passwords="breachedPasswords"
                        :generator-length="generatorLength"
                        :generated-password="generatedPassword"
                        @update:generator-length="generatorLength = $event"
                        @regenerate-password="regenerateGeneratedPassword"
                        @copy-generated-password="copyToClipboard(generatedPassword)"
                    />

                    <DashboardQuickActionsSection
                        v-if="isVaultCategory"
                        @select-category="selectedCategory = $event"
                    />

                    <DashboardVaultItemsSection
                        v-if="isVaultCategory"
                        :category-title="categoryTitle"
                        :filtered-passwords="filteredPasswords"
                        :passwords="passwords"
                        :visible-passwords="visiblePasswords"
                        @toggle-password-visibility="togglePasswordVisibility"
                        @copy-password="copyToClipboard"
                        @toggle-favorite="toggleFavorite"
                        @delete-item="requestDeleteService"
                    />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
