<script setup>
import { ref, watch } from 'vue';
import { Check } from 'lucide-vue-next';

defineProps({
    isSecurityCenter: { type: Boolean, required: true },
    isPasswordGenerator: { type: Boolean, required: true },
    isImportExport: { type: Boolean, required: true },
    isPasswordSharing: { type: Boolean, required: true },
    categoryTitle: { type: String, required: true },
    weakPasswords: { type: Number, required: true },
    reusedPasswords: { type: Number, required: true },
    breachedPasswords: { type: Number, required: true },
});

const length = ref(20);
const useUpper = ref(true);
const useLower = ref(true);
const useNumbers = ref(true);
const useSpecial = ref(true);
const generatedPassword = ref('');

const LOWER = 'abcdefghijklmnopqrstuvwxyz';
const UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
const DIGITS = '0123456789';
const SPECIAL = '!@#$%^&*()-_=+[]{};:,.?/|';

const buildCharset = () => {
    let charset = '';
    if (useLower.value) charset += LOWER;
    if (useUpper.value) charset += UPPER;
    if (useNumbers.value) charset += DIGITS;
    if (useSpecial.value) charset += SPECIAL;
    return charset || LOWER;
};

const generate = () => {
    const charset = buildCharset();
    const array = new Uint32Array(length.value);
    crypto.getRandomValues(array);
    generatedPassword.value = Array.from(array)
        .map((n) => charset[n % charset.length])
        .join('');
};

const copyPassword = async () => {
    try {
        await navigator.clipboard.writeText(generatedPassword.value);
    } catch { /* silent */ }
};

watch([length, useUpper, useLower, useNumbers, useSpecial], generate);

generate();
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

                <!-- Length -->
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <label for="generatorLength" class="text-sm font-medium text-on-surface-variant">
                        Length: <span class="font-semibold text-on-surface">{{ length }}</span>
                    </label>
                    <button
                        type="button"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container"
                        @click="generate"
                    >
                        Generate New
                    </button>
                </div>
                <input
                    id="generatorLength"
                    v-model.number="length"
                    type="range"
                    min="8"
                    max="64"
                    class="w-full accent-primary"
                >

                <!-- Options -->
                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
                    <button
                        v-for="opt in [
                            { label: 'Uppercase', model: useUpper },
                            { label: 'Lowercase', model: useLower },
                            { label: 'Numbers',   model: useNumbers },
                            { label: 'Special',   model: useSpecial },
                        ]"
                        :key="opt.label"
                        type="button"
                        class="flex items-center justify-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-semibold transition-colors"
                        :class="opt.model.value
                            ? 'border-primary bg-primary/10 text-primary'
                            : 'border-outline-variant bg-surface text-on-surface-variant hover:bg-surface-container'"
                        @click="opt.model.value = !opt.model.value"
                    >
                        <Check v-if="opt.model.value" class="h-3 w-3 shrink-0" />
                        {{ opt.label }}
                    </button>
                </div>

                <!-- Result -->
                <div class="mt-4 rounded-lg border border-outline-variant bg-surface px-4 py-3">
                    <p class="text-xs uppercase tracking-wider text-on-surface-variant">Generated Password</p>
                    <p class="mt-2 break-all font-mono text-sm text-on-surface">{{ generatedPassword }}</p>
                </div>
            </div>

            <button
                type="button"
                class="rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-low hover:text-on-surface"
                @click="copyPassword"
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

