<script setup>
import {ref, watch} from 'vue';
import {router, usePage} from '@inertiajs/vue3';
import {route} from 'ziggy-js';
import {Bell, Fingerprint, KeyRound, Shield, Smartphone} from 'lucide-vue-next';
import {useModal} from '../../../../shared/modal/index.ts';

const modal = useModal();
const page = usePage();

const securityProps = defineProps({
    security: {
        type: Object,
        default: () => ({
            mfa_activated: false,
            totp_enabled: false,
        }),
    }
})

const twoFactorEnabled = ref(false);
const mfaTotpEnabled = ref(false);
const mfaEmailEnabled = ref(true);
const passkeyEnabled = ref(false);
const biometricEnabled = ref(true);

watch(
    () => securityProps.security,
    (value) => {
        twoFactorEnabled.value = Boolean(value?.mfa_activated);
        mfaTotpEnabled.value = Boolean(value?.totp_enabled);
    },
    { immediate: true, deep: true },
);

const oauthProviders = ref([
    {
        name: 'Google',
        linked: true,
        account: 'sam.doe@gmail.com',
    },
    {
        name: 'GitHub',
        linked: false,
        account: '',
    },
    {
        name: 'Microsoft',
        linked: false,
        account: '',
    },
]);

const passkeys = ref([
    {
        name: 'MacBook Pro',
        createdAt: 'April 8, 2026',
    },
    {
        name: 'iPhone 16',
        createdAt: 'April 12, 2026',
    },
]);

const activeSessions = ref([
    {
        device: 'Windows 11 | Chrome',
        location: 'Montreal, CA',
        lastSeen: 'Active now',
        current: true,
    },
    {
        device: 'iPhone 16 | iOS App',
        location: 'Montreal, CA',
        lastSeen: '2 hours ago',
        current: false,
    },
    {
        device: 'MacBook Pro | Safari',
        location: 'Quebec, CA',
        lastSeen: 'Yesterday',
        current: false,
    },
]);

const openTotpSetupModal = (payload) => {
    modal.form({
        title: 'Set up authenticator app',
        message: 'Scan this QR code, then enter the 6-digit code from your authenticator app.',
        qrSvg: payload.qrSvg,
        confirmLabel: 'Activate',
        cancelLabel: 'Cancel',
        fields: [
            {
                name: 'setupKey',
                label: 'Setup key',
                required: true,
                initialValue: payload.setupKey ?? '',
            },
            {
                name: 'verificationCode',
                label: 'Verification code',
                placeholder: '123456',
                autocomplete: 'one-time-code',
                required: true,
            },
        ],
        onSubmit: async (values) => {
            const verificationCode = values.verificationCode.trim();

            if (!/^\d{6}$/.test(verificationCode)) {
                throw new Error('Verification code must be exactly 6 digits.');
            }

            await new Promise((resolve, reject) => {
                router.post(
                    route('mfa.totp.verify-setup'),
                    {
                        code: verificationCode,
                    },
                    {
                        preserveScroll: true,
                        preserveState: true,
                        onSuccess: () => resolve(),
                        onError: (errors) => {
                            reject(new Error(errors.code ?? 'Unable to verify authenticator code.'));
                        },
                        onCancel: () => {
                            reject(new Error('TOTP verification was cancelled.'));
                        },
                    },
                );
            });

            mfaTotpEnabled.value = true;
            twoFactorEnabled.value = true;

            modal.confirmation({
                title: 'Authenticator app activated',
                message: 'TOTP is now configured for your account.',
                confirmLabel: 'Close',
                cancelLabel: null,
            });
        },
    });
};

const openTotpRemovalModal = () => {
    modal.form({
        title: 'Remove authenticator app',
        message: 'Enter your master password to remove this authenticator method.',
        confirmLabel: 'Remove',
        cancelLabel: 'Cancel',
        fields: [
            {
                name: 'masterPassword',
                label: 'Master password',
                type: 'password',
                autocomplete: 'current-password',
                placeholder: 'Enter your master password',
                required: true,
            },
        ],
        onSubmit: async (values) => {
            const masterPassword = values.masterPassword.trim();

            await new Promise((resolve, reject) => {
                router.post(
                    route('mfa.totp.disable'),
                    {
                        masterPassword,
                    },
                    {
                        preserveScroll: true,
                        preserveState: true,
                        onSuccess: () => resolve(),
                        onError: (errors) => {
                            reject(new Error(errors.masterPassword ?? 'Unable to remove authenticator app.'));
                        },
                        onCancel: () => {
                            reject(new Error('Authenticator removal was cancelled.'));
                        },
                    },
                );
            });

            mfaTotpEnabled.value = false;
            twoFactorEnabled.value = false;

            modal.confirmation({
                title: 'Authenticator app removed',
                message: 'The authenticator app has been removed from your account.',
                confirmLabel: 'Close',
                cancelLabel: null,
            });
        },
    });
};

const handleTwoFactorToggle = () => {
    if (twoFactorEnabled.value && mfaTotpEnabled.value) {
        openTotpRemovalModal();
        return;
    }

    twoFactorEnabled.value = !twoFactorEnabled.value;
};

const handleTotpAction = () => {
    if (mfaTotpEnabled.value) {
        openTotpRemovalModal();
        return;
    }

    router.post(route('mfa.totp.setup-qr'), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (visitPage) => {
            const payload = visitPage?.flash?.totpSetup
                ?? visitPage?.props?.flash?.totpSetup
                ?? page.props?.flash?.totpSetup;

            if (!payload || !payload.qrSvg) {
                modal.danger({
                    title: 'TOTP setup unavailable',
                    message: 'Unable to generate TOTP setup QR.',
                    confirmLabel: 'Close',
                    cancelLabel: null,
                });
                return;
            }

            openTotpSetupModal(payload);
        },
        onError: (errors) => {
            modal.danger({
                title: 'TOTP setup unavailable',
                message: errors.code ?? 'Unable to generate TOTP setup QR.',
                confirmLabel: 'Close',
                cancelLabel: null,
            });
        },
    });
};
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
                            <KeyRound class="h-6 w-6"/>
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
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-600">
                                <Smartphone class="h-6 w-6"/>
                            </div>
                            <div>
                                <p class="text-2xl font-semibold tracking-tight text-on-surface">Two-Factor
                                    Authentication (2FA)</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Add an extra layer of security with
                                    2FA</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors"
                            :class="twoFactorEnabled ? 'bg-primary' : 'bg-surface-container-high'"
                            @click="handleTwoFactorToggle"
                        >
                            <span
                                class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform"
                                :class="twoFactorEnabled ? 'translate-x-7' : 'translate-x-1'"
                            />
                        </button>
                    </div>

                    <div v-if="twoFactorEnabled" class="mt-4 border-t border-outline-variant pt-4">
                        <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-on-surface-variant">
                            Two-factor methods</p>

                        <article class="flex items-start justify-between gap-4 border-b border-outline-variant py-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-secondary-container text-primary">
                                    <Smartphone class="h-5 w-5"/>
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
                                class="shrink-0 rounded-md border px-3 py-1 text-sm font-medium transition-colors"
                                :class="mfaTotpEnabled
                                    ? 'border-red-500 text-red-600 hover:bg-red-600 hover:text-white'
                                    : 'border-primary text-primary hover:bg-primary hover:text-on-primary'"
                                @click="handleTotpAction"
                            >
                                {{ mfaTotpEnabled ? 'Remove' : 'Setup' }}
                            </button>
                        </article>

                        <article class="flex items-start justify-between gap-4 pt-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-surface-container text-on-surface-variant">
                                    <Bell class="h-5 w-5"/>
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
                                @click="mfaEmailEnabled = !mfaEmailEnabled"
                            >
                                {{ mfaEmailEnabled ? 'Edit' : 'Setup' }}
                            </button>
                        </article>
                    </div>
                </div>

                <div class="py-5">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                                <Shield class="h-6 w-6"/>
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
                            @click="passkeyEnabled = !passkeyEnabled"
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
                        <button type="button"
                                class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container">
                            Register New Passkey
                        </button>
                    </div>
                </div>

                <div class="py-5">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                                <Fingerprint class="h-6 w-6"/>
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
                            @click="biometricEnabled = !biometricEnabled"
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
            <p class="mt-1 text-sm text-on-surface-variant">Review and revoke active account sessions across
                devices.</p>
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
            <button type="button"
                    class="mt-4 rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface">
                Revoke Other Sessions
            </button>
        </section>
    </div>
</template>
