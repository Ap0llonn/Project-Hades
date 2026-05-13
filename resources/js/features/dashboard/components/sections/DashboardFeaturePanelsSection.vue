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
});

const emit = defineEmits(['update:generatorLength', 'regenerate-password', 'copy-generated-password']);

const onGeneratorLengthInput = (event) => {
    const target = event.target;
    if (!(target instanceof HTMLInputElement)) {
        return;
    }

    emit('update:generatorLength', Number(target.value));
    emit('regenerate-password');
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
</template>

