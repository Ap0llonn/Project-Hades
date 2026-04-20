<script setup>
import { Bell, Fingerprint, KeyRound, Shield, Smartphone } from 'lucide-vue-next';

defineProps({
    twoFactorEnabled: {
        type: Boolean,
        required: true,
    },
    mfaEmailEnabled: {
        type: Boolean,
        required: true,
    },
    mfaTotpEnabled: {
        type: Boolean,
        required: true,
    },
    passkeyEnabled: {
        type: Boolean,
        required: true,
    },
    biometricEnabled: {
        type: Boolean,
        required: true,
    },
    oauthProviders: {
        type: Array,
        required: true,
    },
    passkeys: {
        type: Array,
        required: true,
    },
    activeSessions: {
        type: Array,
        required: true,
    },
});

defineEmits([
    'toggle-two-factor',
    'toggle-email',
    'toggle-passkey',
    'toggle-biometric',
    'totp-action',
]);
</script>

<template>
    <div class="space-y-8">
        <section>
            <div>
                <h3 class="text-2xl font-semibold tracking-tight text-on-surface">Authentication Methods</h3>
                <p class="mt-1 text-sm text-on-surface-variant">Manage how you access your vault</p>
            </div>

            <div class="mt-4 divide-y divide-outline-variant border-y border-outline-variant">
                <div class="flex items-center justify-between gap-4 py-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600">
                            <KeyRound class="h-6 w-6" />
                        </div>
                        <div>
                            <p class="text-2xl font-semibold tracking-tight text-on-surface">Master Password</p>
                            <p class="mt-1 text-sm text-on-surface-variant">Your primary authentication method</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="text-lg font-medium text-primary transition-colors hover:text-primary-container"
                    >
                        Change Password
                    </button>
                </div>

                <div class="py-5">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-600">
                                <Smartphone class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-2xl font-semibold tracking-tight text-on-surface">Two-Factor Authentication (2FA)</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Add an extra layer of security with 2FA</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors"
                            :class="twoFactorEnabled ? 'bg-primary' : 'bg-surface-container-high'"
                            @click="$emit('toggle-two-factor')"
                        >
                            <span
                                class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform"
                                :class="twoFactorEnabled ? 'translate-x-7' : 'translate-x-1'"
                            />
                        </button>
                    </div>

                    <div v-if="twoFactorEnabled" class="mt-4 border-t border-outline-variant pt-4">
                        <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-on-surface-variant">Two-factor methods</p>

                        <article class="flex items-start justify-between gap-4 border-b border-outline-variant py-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-secondary-container text-primary">
                                    <Smartphone class="h-5 w-5" />
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-on-surface">Authenticator app</p>
                                        <span
                                            class="rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="mfaTotpEnabled
                                                ? 'bg-secondary-container text-primary'
                                                : 'bg-surface-container-high text-on-surface-variant'"
                                        >
                                            {{ mfaTotpEnabled ? 'Configured' : 'Not configured' }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-on-surface-variant">
                                        Use an authenticator app or browser extension to get verification codes.
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="shrink-0 rounded-md border border-primary px-3 py-1 text-sm font-medium text-primary transition-colors hover:bg-primary hover:text-on-primary"
                                @click="$emit('totp-action')"
                            >
                                {{ mfaTotpEnabled ? 'Edit' : 'Setup' }}
                            </button>
                        </article>

                        <article class="flex items-start justify-between gap-4 pt-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-surface-container text-on-surface-variant">
                                    <Bell class="h-5 w-5" />
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-on-surface">Email code</p>
                                        <span
                                            class="rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="mfaEmailEnabled
                                                ? 'bg-secondary-container text-primary'
                                                : 'bg-surface-container-high text-on-surface-variant'"
                                        >
                                            {{ mfaEmailEnabled ? 'Configured' : 'Not configured' }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-on-surface-variant">
                                        Receive one-time codes by email when additional verification is required.
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="shrink-0 rounded-md border border-primary px-3 py-1 text-sm font-medium text-primary transition-colors hover:bg-primary hover:text-on-primary"
                                @click="$emit('toggle-email')"
                            >
                                {{ mfaEmailEnabled ? 'Edit' : 'Setup' }}
                            </button>
                        </article>
                    </div>
                </div>

                <div class="py-5">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                                <Shield class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-2xl font-semibold tracking-tight text-on-surface">Passkey</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Sign in without a password</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors"
                            :class="passkeyEnabled ? 'bg-primary' : 'bg-surface-container-high'"
                            @click="$emit('toggle-passkey')"
                        >
                            <span
                                class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform"
                                :class="passkeyEnabled ? 'translate-x-7' : 'translate-x-1'"
                            />
                        </button>
                    </div>

                    <div v-if="passkeyEnabled" class="mt-4 space-y-2 border-t border-outline-variant pt-4">
                        <div
                            v-for="passkey in passkeys"
                            :key="passkey.name"
                            class="py-1"
                        >
                            <p class="text-sm font-medium text-on-surface">{{ passkey.name }}</p>
                            <p class="text-xs text-on-surface-variant">Registered on {{ passkey.createdAt }}</p>
                        </div>
                        <button type="button" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container">
                            Register New Passkey
                        </button>
                    </div>
                </div>

                <div class="py-5">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                                <Fingerprint class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-2xl font-semibold tracking-tight text-on-surface">Biometric Security</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Face ID, Touch ID, or Windows Hello</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors"
                            :class="biometricEnabled ? 'bg-primary' : 'bg-surface-container-high'"
                            @click="$emit('toggle-biometric')"
                        >
                            <span
                                class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform"
                                :class="biometricEnabled ? 'translate-x-7' : 'translate-x-1'"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-t border-outline-variant pt-6">
            <p class="font-medium text-on-surface">OAuth2 Account Linking</p>
            <p class="mt-1 text-sm text-on-surface-variant">Link external identity providers for trusted sign-in.</p>
            <div class="mt-4 divide-y divide-outline-variant">
                <div
                    v-for="provider in oauthProviders"
                    :key="provider.name"
                    class="flex items-center justify-between py-3"
                >
                    <div>
                        <p class="text-sm font-medium text-on-surface">{{ provider.name }}</p>
                        <p class="text-xs text-on-surface-variant">
                            {{ provider.linked ? `Linked as ${provider.account}` : 'Not linked' }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors"
                        :class="provider.linked
                            ? 'border border-outline-variant text-on-surface-variant hover:bg-surface-container'
                            : 'bg-primary text-on-primary hover:bg-primary-container'"
                    >
                        {{ provider.linked ? 'Unlink' : 'Link' }}
                    </button>
                </div>
            </div>
        </section>

        <section class="border-t border-outline-variant pt-6">
            <p class="font-medium text-on-surface">Sessions</p>
            <p class="mt-1 text-sm text-on-surface-variant">Review and revoke active account sessions across devices.</p>
            <div class="mt-4 divide-y divide-outline-variant">
                <div
                    v-for="session in activeSessions"
                    :key="`${session.device}-${session.lastSeen}`"
                    class="flex items-center justify-between py-3"
                >
                    <div>
                        <p class="text-sm font-medium text-on-surface">{{ session.device }}</p>
                        <p class="text-xs text-on-surface-variant">{{ session.location }} | {{ session.lastSeen }}</p>
                    </div>
                    <span
                        class="rounded-full px-2 py-1 text-xs font-medium"
                        :class="session.current ? 'bg-secondary-container text-primary' : 'bg-surface-container text-on-surface-variant'"
                    >
                        {{ session.current ? 'Current' : 'Active' }}
                    </span>
                </div>
            </div>
            <button type="button" class="mt-4 rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface">
                Revoke Other Sessions
            </button>
        </section>
    </div>
</template>
