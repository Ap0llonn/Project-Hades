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
const selectedShareServiceId = ref('');
const shareRecipientEmail = ref('');
const shareStatus = ref('');
const shareBusy = ref(false);
const incomingShares = ref([]);
const incomingSharesBusy = ref(false);
const sharedVaultItems = ref([]);
const modal = useModal();

const {
    passwords,
    visiblePasswords,
    passwordStrength,
    loadServices,
    createService,
    deleteService,
    toggleFavorite,
    togglePasswordVisibility,
    lookupShareRecipient,
    createShareEnvelope,
    shareService,
    loadIncomingShares,
    revokeShare,
    decryptIncomingShareEnvelope,
    decryptSharedServiceData,
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
    allVaultItems.value.filter((pwd) => {
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

const favoriteCount = computed(() => allVaultItems.value.filter((p) => p.favorite).length);
const loginCount = computed(() => allVaultItems.value.filter((p) => p.category === 'login').length);
const cardCount = computed(() => allVaultItems.value.filter((p) => p.category === 'card').length);
const noteCount = computed(() => allVaultItems.value.filter((p) => p.category === 'note').length);
const weakPasswords = computed(() => allVaultItems.value.filter((p) => p.strength === 'weak').length);
const securityScore = 78;
const reusedPasswords = 2;
const breachedPasswords = 0;
const securityAlertCount = computed(() => weakPasswords.value + reusedPasswords + breachedPasswords);
const isVaultCategory = computed(() => vaultCategories.includes(selectedCategory.value));
const isSecurityCenter = computed(() => selectedCategory.value === 'security-center');
const isPasswordGenerator = computed(() => selectedCategory.value === 'password-generator');
const isImportExport = computed(() => selectedCategory.value === 'import-export');
const isPasswordSharing = computed(() => selectedCategory.value === 'password-sharing');
const allVaultItems = computed(() => [...passwords.value, ...sharedVaultItems.value]);

const shareableItems = computed(() =>
    passwords.value.map((item) => ({
        id: String(item.id),
        name: String(item.name ?? 'Secure item'),
    })),
);

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

const apiTypeToCategory = (type) => {
    const normalized = String(type ?? '').trim();
    if (normalized === 'credit_card') {
        return 'card';
    }
    if (normalized === 'identity' || normalized === 'note' || normalized === 'login') {
        return normalized;
    }

    return 'note';
};

const formatLastUsed = (isoDate) => {
    const value = String(isoDate ?? '').trim();
    if (!value) {
        return 'just now';
    }

    const parsed = new Date(value);
    if (Number.isNaN(parsed.getTime())) {
        return 'just now';
    }

    return parsed.toLocaleString();
};

const requestDeleteService = (id) => {
    const targetItem = allVaultItems.value.find((item) => String(item.id) === String(id));
    const itemName = (targetItem?.name ?? 'this item').trim() || 'this item';

    if (targetItem?.isShared) {
        modal.danger({
            title: 'Remove shared item',
            message: `Remove "${itemName}" from your shared items list?`,
            confirmLabel: 'Remove',
            cancelLabel: 'Cancel',
            onConfirm: async () => {
                await removeIncomingShare({
                    serviceId: targetItem.serviceId,
                    id: targetItem.shareId,
                });
            },
        });
        return;
    }

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

const handleToggleFavorite = async (item) => {
    if (item?.isShared) {
        shareStatus.value = 'Favorites are only available for your own items.';
        return;
    }

    await toggleFavorite(item);
};

const buildIncomingShareView = async (share) => {
    const sharedBy = String(share?.shared_by?.email ?? 'Unknown sender');
    const serviceId = String(share?.service_id ?? '');
    const shareId = String(share?.id ?? `${serviceId}:${sharedBy}`);
    const serviceType = apiTypeToCategory(share?.service?.type);
    const status = String(share?.service?.status ?? 'active');
    const lastUsed = formatLastUsed(share?.service?.updated_at ?? share?.updated_at);
    const servicePayload = share?.service?.payload ?? null;
    const fallbackName = String(share?.service?.type ?? 'shared item');

    let name = fallbackName;
    let username = '';
    let password = serviceType === 'login' ? '' : '***';
    let url = '';
    let note = '';
    let error = '';

    try {
        const sharedDekBase64 = await decryptIncomingShareEnvelope(share?.key_envelope ?? null);
        if (!sharedDekBase64) {
            error = 'Unsupported shared key envelope.';
        } else {
            const decrypted = await decryptSharedServiceData(servicePayload, sharedDekBase64);
            if (decrypted && typeof decrypted === 'object') {
                name = String(decrypted.name ?? fallbackName);
                if (serviceType === 'login') {
                    username = String(decrypted.username ?? '').trim();
                    password = String(decrypted.password ?? '');
                    url = String(decrypted.url ?? '').trim();
                } else if (serviceType === 'card') {
                    const cardLastFour = String(decrypted.cardNumber ?? '').replace(/\s+/g, '').slice(-4);
                    username = `**** **** **** ${cardLastFour || '0000'}`;
                } else if (serviceType === 'identity') {
                    username = String(decrypted.email ?? decrypted.fullName ?? '').trim() || 'Identity';
                } else {
                    username = 'Secure note';
                }
                note = String(decrypted.note ?? '').trim();
            } else {
                error = 'Unable to decrypt shared item payload.';
            }
        }
    } catch {
        error = 'Unable to decrypt shared item payload.';
    }

    return {
        id: `shared:${shareId}`,
        shareId,
        serviceId,
        name,
        username: username || 'No username',
        password,
        url,
        note,
        category: serviceType,
        favorite: false,
        requiresMasterPasswordForNote: false,
        lastUsed,
        strength: serviceType === 'login' ? passwordStrength(password) : 'strong',
        status,
        isShared: true,
        sharedBy,
        error,
    };
};

const refreshIncomingShares = async () => {
    incomingSharesBusy.value = true;
    try {
        const shares = await loadIncomingShares();
        const mapped = await Promise.all(shares.map((share) => buildIncomingShareView(share)));
        incomingShares.value = mapped;
        sharedVaultItems.value = mapped.filter((item) => !item.error);
    } catch (error) {
        incomingShares.value = [];
        sharedVaultItems.value = [];
        shareStatus.value = error instanceof Error ? error.message : 'Failed to load incoming shares.';
    } finally {
        incomingSharesBusy.value = false;
    }
};

const submitShare = async () => {
    shareStatus.value = '';

    const serviceId = String(selectedShareServiceId.value ?? '').trim();
    const recipientEmail = String(shareRecipientEmail.value ?? '').trim();

    if (!serviceId || !recipientEmail) {
        shareStatus.value = 'Select an item and enter recipient email.';
        return;
    }

    shareBusy.value = true;

    try {
        const recipient = await lookupShareRecipient(recipientEmail);
        const envelope = await createShareEnvelope(recipient.public_key);
        await shareService(serviceId, recipient.email, envelope);
        shareStatus.value = `Shared with ${recipient.email}.`;
        shareRecipientEmail.value = '';
        await refreshIncomingShares();
    } catch (error) {
        shareStatus.value = error instanceof Error ? `Share failed: ${error.message}` : 'Share failed.';
    } finally {
        shareBusy.value = false;
    }
};

const removeIncomingShare = async (share) => {
    const serviceId = String(share?.serviceId ?? '').trim();
    const shareId = String(share?.shareId ?? share?.id ?? '').trim();

    if (!serviceId || !shareId) {
        return;
    }

    try {
        await revokeShare(serviceId, shareId);
        await refreshIncomingShares();
    } catch (error) {
        shareStatus.value = error instanceof Error ? error.message : 'Unable to remove incoming share.';
    }
};

onMounted(() => {
    loadServices()
        .then(async () => {
            if (!selectedShareServiceId.value && shareableItems.value.length > 0) {
                selectedShareServiceId.value = shareableItems.value[0].id;
            }
            await refreshIncomingShares();
        })
        .catch((error) => {
            console.error('Unable to load encrypted services.', error);
        });
});

regenerateGeneratedPassword();
</script>

<template>
    <Head title="Dashboard | VaultGuardian" />

    <DashboardLayout
        :selected-category="selectedCategory"
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
                        :shareable-items="shareableItems"
                        :selected-share-service-id="selectedShareServiceId"
                        :share-recipient-email="shareRecipientEmail"
                        :share-busy="shareBusy"
                        :share-status="shareStatus"
                        :incoming-shares="incomingShares"
                        :incoming-shares-busy="incomingSharesBusy"
                        @update:generator-length="generatorLength = $event"
                        @regenerate-password="regenerateGeneratedPassword"
                        @copy-generated-password="copyToClipboard(generatedPassword)"
                        @update:selected-share-service-id="selectedShareServiceId = $event"
                        @update:share-recipient-email="shareRecipientEmail = $event"
                        @share-password="submitShare"
                        @refresh-incoming-shares="refreshIncomingShares"
                        @revoke-incoming-share="removeIncomingShare"
                    />

                    <DashboardQuickActionsSection
                        v-if="isVaultCategory"
                        @select-category="selectedCategory = $event"
                    />

                    <DashboardVaultItemsSection
                        v-if="isVaultCategory"
                        :category-title="categoryTitle"
                        :filtered-passwords="filteredPasswords"
                        :passwords="allVaultItems"
                        :visible-passwords="visiblePasswords"
                        @toggle-password-visibility="togglePasswordVisibility"
                        @copy-password="copyToClipboard"
                        @toggle-favorite="handleToggleFavorite"
                        @delete-item="requestDeleteService"
                    />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
