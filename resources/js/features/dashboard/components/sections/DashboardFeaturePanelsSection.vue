<script setup>
defineProps({
    isSecurityCenter: {
        type: Boolean,
        required: true,
    },
    isPasswordGenerator: {
        type: Boolean,
        required: true,
    },
    isImportExport: {
        type: Boolean,
        required: true,
    },
    isPasswordSharing: {
        type: Boolean,
        required: true,
    },
    categoryTitle: {
        type: String,
        required: true,
    },
    weakPasswords: {
        type: Number,
        required: true,
    },
    reusedPasswords: {
        type: Number,
        required: true,
    },
    breachedPasswords: {
        type: Number,
        required: true,
    },
    generatorLength: {
        type: Number,
        required: true,
    },
    generatedPassword: {
        type: String,
        required: true,
    },
    shareableItems: {
        type: Array,
        default: () => [],
    },
    selectedShareServiceId: {
        type: String,
        default: '',
    },
    shareRecipientEmail: {
        type: String,
        default: '',
    },
    shareBusy: {
        type: Boolean,
        default: false,
    },
    shareStatus: {
        type: String,
        default: '',
    },
    incomingShares: {
        type: Array,
        default: () => [],
    },
    incomingSharesBusy: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    'update:generatorLength',
    'regenerate-password',
    'copy-generated-password',
    'update:selectedShareServiceId',
    'update:shareRecipientEmail',
    'share-password',
    'refresh-incoming-shares',
    'revoke-incoming-share',
]);

const onGeneratorLengthInput = (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement)) {
        return;
    }

    emit('update:generatorLength', Number(target.value));
    emit('regenerate-password');
};

const onServiceSelect = (event) => {
    const target = event.target;
    if (!(target instanceof HTMLSelectElement)) {
        return;
    }

    emit('update:selectedShareServiceId', target.value);
};

const onRecipientInput = (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement)) {
        return;
    }

    emit('update:shareRecipientEmail', target.value);
};
</script>

<template>
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
                        @click="emit('regenerate-password')"
                    >
                        Generate New
                    </button>
                </div>
                <input
                    id="generatorLength"
                    :value="generatorLength"
                    type="range"
                    min="12"
                    max="40"
                    class="w-full accent-primary"
                    @input="onGeneratorLengthInput"
                >
                <div class="mt-4 rounded-lg border border-outline-variant bg-surface px-4 py-3">
                    <p class="text-xs uppercase tracking-wider text-on-surface-variant">Generated Password</p>
                    <p class="mt-2 break-all font-mono text-on-surface">{{ generatedPassword }}</p>
                </div>
            </div>
            <button
                type="button"
                class="rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-low hover:text-on-surface"
                @click="emit('copy-generated-password')"
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
        <div class="grid grid-cols-1 gap-4 p-6 lg:grid-cols-2">
            <article class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                <p class="text-lg font-semibold text-on-surface">Create Share</p>
                <p class="mt-2 text-sm text-on-surface-variant">Pick one of your vault items and enter recipient email.</p>

                <div class="mt-4 grid gap-3">
                    <label class="grid gap-1">
                        <span class="text-xs font-medium text-on-surface-variant">Vault item</span>
                        <select
                            :value="selectedShareServiceId"
                            class="rounded-lg border border-outline-variant bg-surface px-3 py-2 text-sm text-on-surface"
                            @change="onServiceSelect"
                        >
                            <option value="">Select item</option>
                            <option
                                v-for="item in shareableItems"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                    </label>

                    <label class="grid gap-1">
                        <span class="text-xs font-medium text-on-surface-variant">Recipient email</span>
                        <input
                            :value="shareRecipientEmail"
                            type="email"
                            placeholder="friend@example.com"
                            class="rounded-lg border border-outline-variant bg-surface px-3 py-2 text-sm text-on-surface"
                            @input="onRecipientInput"
                        >
                    </label>
                </div>

                <button
                    type="button"
                    class="mt-4 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="shareBusy"
                    @click="emit('share-password')"
                >
                    {{ shareBusy ? 'Sharing...' : 'Share item' }}
                </button>

                <p
                    v-if="shareStatus"
                    class="mt-3 text-sm"
                    :class="shareStatus.toLowerCase().includes('failed') || shareStatus.toLowerCase().includes('error') ? 'text-red-600' : 'text-on-surface-variant'"
                >
                    {{ shareStatus }}
                </p>
            </article>

            <article class="rounded-xl border border-outline-variant bg-surface-container-low p-5">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-lg font-semibold text-on-surface">Incoming Shares</p>
                    <button
                        type="button"
                        class="rounded-lg border border-outline-variant px-3 py-1.5 text-xs font-semibold text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="incomingSharesBusy"
                        @click="emit('refresh-incoming-shares')"
                    >
                        Refresh
                    </button>
                </div>

                <div v-if="incomingSharesBusy" class="mt-3 text-sm text-on-surface-variant">Loading shares...</div>

                <div v-else-if="incomingShares.length === 0" class="mt-3 text-sm text-on-surface-variant">
                    No incoming shares.
                </div>

                <ul v-else class="mt-3 space-y-3">
                    <li
                        v-for="share in incomingShares"
                        :key="share.id"
                        class="rounded-lg border border-outline-variant bg-surface p-3"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-on-surface">{{ share.name }}</p>
                                <p class="mt-0.5 truncate text-xs text-on-surface-variant">
                                    From {{ share.sharedBy }}
                                </p>
                                <p class="mt-0.5 truncate text-xs text-on-surface-variant">
                                    {{ share.username }}
                                </p>
                                <p
                                    v-if="share.error"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ share.error }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="rounded-md border border-outline-variant px-2 py-1 text-xs font-semibold text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface"
                                @click="emit('revoke-incoming-share', share)"
                            >
                                Remove
                            </button>
                        </div>
                    </li>
                </ul>
            </article>
        </div>
    </section>
</template>
