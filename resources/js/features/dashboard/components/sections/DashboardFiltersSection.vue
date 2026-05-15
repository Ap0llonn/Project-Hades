<script setup>
import {
    Archive,
    CreditCard,
    FileText,
    Folder,
    FolderPlus,
    Globe,
    HelpCircle,
    IdCard,
    Key,
    Search,
    Star,
    Trash2,
    User,
} from 'lucide-vue-next';

const props = defineProps({
    searchQuery: {
        type: String,
        required: true,
    },
    allItemsVaultFilter: {
        type: String,
        required: true,
    },
    allItemsTypeFilter: {
        type: String,
        required: true,
    },
    allItemsFolderFilter: {
        type: String,
        required: true,
    },
    allItemsLifecycleFilter: {
        type: String,
        required: true,
    },
});

const emit = defineEmits([
    'update:searchQuery',
    'update:allItemsVaultFilter',
    'update:allItemsTypeFilter',
    'update:allItemsFolderFilter',
    'update:allItemsLifecycleFilter',
]);

const updateSearchQuery = (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement)) {
        return;
    }

    emit('update:searchQuery', target.value);
};

const resetFilters = () => {
    emit('update:allItemsTypeFilter', 'all-items');
    emit('update:allItemsLifecycleFilter', 'active');
    emit('update:allItemsFolderFilter', 'no-folder');
    emit('update:allItemsVaultFilter', 'all-vaults');
};
</script>

<template>
    <aside class="self-start lg:sticky lg:top-28 lg:max-h-[calc(100vh-8rem)] lg:overflow-y-auto">
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
                        :value="searchQuery"
                        type="text"
                        placeholder="Search in vault"
                        class="w-full rounded-lg border border-outline-variant bg-surface py-2.5 pl-10 pr-3 text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                        @input="updateSearchQuery"
                    >
                </div>

                <div>
                    <p class="mb-2 text-lg font-semibold text-primary">All Vaults</p>
                    <div class="space-y-1 text-on-surface">
                        <button
                            type="button"
                            class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low"
                            @click="emit('update:allItemsVaultFilter', 'my-vault')"
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
                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="emit('update:allItemsTypeFilter', 'favorites')">
                            <Star class="h-4 w-4" />
                            <span>Favorites</span>
                        </button>
                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="emit('update:allItemsTypeFilter', 'login')">
                            <Globe class="h-4 w-4" />
                            <span>Login</span>
                        </button>
                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="emit('update:allItemsTypeFilter', 'card')">
                            <CreditCard class="h-4 w-4" />
                            <span>Payment Card</span>
                        </button>
                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="emit('update:allItemsTypeFilter', 'identity')">
                            <IdCard class="h-4 w-4" />
                            <span>Identity</span>
                        </button>
                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="emit('update:allItemsTypeFilter', 'note')">
                            <FileText class="h-4 w-4" />
                            <span>Note</span>
                        </button>
                        <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="emit('update:allItemsTypeFilter', 'ssh')">
                            <Key class="h-4 w-4" />
                            <span>SSH Key</span>
                        </button>
                    </div>
                </div>

                <div class="border-t border-outline-variant pt-4">
                    <p class="mb-2 text-lg font-semibold text-on-surface-variant">Folders</p>
                    <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="emit('update:allItemsFolderFilter', 'no-folder')">
                        <Folder class="h-4 w-4" />
                        <span>No Folder</span>
                    </button>
                </div>

                <div class="border-t border-outline-variant pt-4">
                    <button type="button" class="flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="emit('update:allItemsLifecycleFilter', 'archived')">
                        <Archive class="h-4 w-4" />
                        <span>Archive</span>
                    </button>
                    <button type="button" class="mt-1 flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left text-base transition-colors hover:bg-surface-container-low" @click="emit('update:allItemsLifecycleFilter', 'trash')">
                        <Trash2 class="h-4 w-4" />
                        <span>Trash</span>
                    </button>
                    <button type="button" class="mt-3 rounded-md border border-outline-variant px-3 py-1.5 text-sm font-medium text-on-surface-variant transition-colors hover:bg-surface-container-low hover:text-on-surface" @click="resetFilters">
                        Reset filters
                    </button>
                </div>
            </div>
        </div>
    </aside>
</template>

