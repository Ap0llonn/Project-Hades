<script setup>
import {computed, ref, watch} from 'vue';
import {router, usePage} from '@inertiajs/vue3';
import {route} from 'ziggy-js';
import {base64URLStringToBuffer, bufferToBase64URLString} from '@simplewebauthn/browser';
import {Bell, Fingerprint, KeyRound, Shield, Smartphone} from 'lucide-vue-next';
import {useModal} from '../../../../shared/modal/index.ts';
import {CryptoDecryptor, CryptoEncryptor} from '../../../../shared/utils';
import {dekService} from '../../../../shared/services/dekService';

const modal = useModal();
const page = usePage();

const securityProps = defineProps({
    security: {
        type: Object,
        default: () => ({
            mfa_activated: false,
            totp_enabled: false,
            email_enabled: false,
            passkeys: [],
        }),
    }
})

const mfaTotpEnabled = ref(false);
const mfaEmailEnabled = ref(false);
const twoFactorEnabled = computed(() => mfaTotpEnabled.value || mfaEmailEnabled.value);
const passkeys = ref([]);
const passkeySupported = ref(typeof window.browserSupportsWebAuthn === 'function' && window.browserSupportsWebAuthn());
const hasPasskeys = computed(() => passkeys.value.length > 0);
const biometricEnabled = ref(true);

watch(
    () => securityProps.security,
    (value) => {
        mfaTotpEnabled.value = Boolean(value?.totp_enabled);
        mfaEmailEnabled.value = Boolean(value?.email_enabled);
        passkeys.value = Array.isArray(value?.passkeys) ? value.passkeys : [];
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
    if (!twoFactorEnabled.value) {
        modal.confirmation({
            title: 'Enable two-factor authentication',
            message: 'Set up at least one method below to enable MFA on your account.',
            confirmLabel: 'Close',
            cancelLabel: null,
        });
        return;
    }

    if (mfaTotpEnabled.value && !mfaEmailEnabled.value) {
        openTotpRemovalModal();
        return;
    }

    if (mfaEmailEnabled.value && !mfaTotpEnabled.value) {
        openEmailRemovalModal();
        return;
    }

    modal.confirmation({
        title: 'Multiple MFA methods enabled',
        message: 'Remove Authenticator app and Email code below to fully disable MFA.',
        confirmLabel: 'Close',
        cancelLabel: null,
    });
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
const openEmailSetupModal = () => {
    modal.form({
        title: 'Set up email 2FA',
        message: 'Check your email and enter the 6-digit verification code.',
        confirmLabel: 'Activate',
        cancelLabel: 'Cancel',
        fields: [
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
                    route('mfa.email.verify'),
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
                            reject(new Error('email verification was cancelled.'));
                        },
                    },
                );
            });

            mfaEmailEnabled.value = true;

            modal.confirmation({
                title: 'Email MFA activated',
                message: 'Email MFA is now configured for your account.',
                confirmLabel: 'Close',
                cancelLabel: null,
            });
        },
    });
};

const openEmailRemovalModal = () => {
    modal.form({
        title: 'Remove email 2FA',
        message: 'Enter your master password to remove email verification from your account.',
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
                    route('mfa.email.disable'),
                    {
                        masterPassword,
                    },
                    {
                        preserveScroll: true,
                        preserveState: true,
                        onSuccess: () => resolve(),
                        onError: (errors) => {
                            reject(new Error(errors.masterPassword ?? 'Unable to remove email 2FA.'));
                        },
                        onCancel: () => {
                            reject(new Error('Email 2FA removal was cancelled.'));
                        },
                    },
                );
            });

            mfaEmailEnabled.value = false;

            modal.confirmation({
                title: 'Email 2FA removed',
                message: 'Email verification has been removed from your account.',
                confirmLabel: 'Close',
                cancelLabel: null,
            });
        },
    });
};

const handleEmailAction = () => {
    if (mfaEmailEnabled.value) {
        openEmailRemovalModal();
        return;
    }

    router.post(route('mfa.email.generate'), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            openEmailSetupModal();
        },
        onError: (errors) => {
            modal.danger({
                title: 'Email setup unavailable',
                message: errors.code ?? 'Unable to send email verification code.',
                confirmLabel: 'Close',
                cancelLabel: null,
            });
        },
    });
};

const formatPasskeyTimestamp = (value) => {
    if (!value) {
        return 'Never';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return 'Never';
    }

    return date.toLocaleString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const PASSKEY_PRF_SALT_BYTES = 32;

const asPositiveIntegerOrUndefined = (value) => {
    const parsedValue = Number(value);

    if (!Number.isFinite(parsedValue) || parsedValue <= 0) {
        return undefined;
    }

    return parsedValue;
};

const asPrfResultBytes = (prfResult) => {
    if (prfResult instanceof ArrayBuffer) {
        return new Uint8Array(prfResult);
    }

    if (ArrayBuffer.isView(prfResult)) {
        return new Uint8Array(prfResult.buffer, prfResult.byteOffset, prfResult.byteLength);
    }

    if (Array.isArray(prfResult)) {
        return new Uint8Array(prfResult);
    }

    if (typeof prfResult === 'string' && prfResult !== '') {
        return new Uint8Array(base64URLStringToBuffer(prfResult));
    }

    return null;
};

const extractPrfResultFromExtensions = (clientExtensionResults) => {
    const prf = clientExtensionResults?.prf;
    if (!prf || typeof prf !== 'object') {
        return null;
    }

    const nestedResult = prf?.results?.first;
    if (nestedResult !== undefined) {
        return asPrfResultBytes(nestedResult);
    }

    if (prf?.first !== undefined) {
        return asPrfResultBytes(prf.first);
    }

    return null;
};

const derivePasskeyPrfKeyBytes = async (credentialId, prfSaltBytes) => {
    const publicKey = {
        challenge: window.crypto.getRandomValues(new Uint8Array(32)),
        rpId: window.location.hostname,
        userVerification: 'required',
        allowCredentials: [
            {
                id: base64URLStringToBuffer(credentialId),
                type: 'public-key',
            },
        ],
        extensions: {
            prf: {
                eval: {
                    first: prfSaltBytes,
                },
            },
        },
    };

    const assertion = await navigator.credentials.get({ publicKey });
    if (!(assertion instanceof PublicKeyCredential)) {
        throw new Error('Unable to evaluate PRF for this passkey.');
    }

    const prfBytes = extractPrfResultFromExtensions(assertion.getClientExtensionResults?.());
    if (!prfBytes || prfBytes.length === 0) {
        throw new Error('This passkey does not expose PRF output on this browser/device.');
    }

    return prfBytes;
};

const createPasskey = async (name, masterPassword) => {
    if (!passkeySupported.value) {
        modal.danger({
            title: 'Passkeys unavailable',
            message: 'This browser or device does not support passkeys.',
            confirmLabel: 'Close',
            cancelLabel: null,
        });
        return;
    }

    const bootstrap = await dekService.fetchBootstrap();
    if (!bootstrap?.dek_wrapper || bootstrap.dek_wrapper.type !== 'password') {
        throw new Error('Sign in with your master password before registering a passkey wrapper.');
    }

    const prfParams = bootstrap.dek_wrapper.prf_params && typeof bootstrap.dek_wrapper.prf_params === 'object'
        ? bootstrap.dek_wrapper.prf_params
        : {};
    const keyLengthBits = asPositiveIntegerOrUndefined(prfParams.keyLengthBits);
    const argonOpsLimit = asPositiveIntegerOrUndefined(prfParams.opsLimit);
    const argonMemoryKb = asPositiveIntegerOrUndefined(prfParams.memoryKb);
    const argonType = prfParams.type === 'Argon2i13' || prfParams.type === 'Argon2id13'
        ? prfParams.type
        : undefined;

    const dekBytes = await CryptoDecryptor.decryptBytesWithPassword({
        ciphertextBase64: bootstrap.dek_wrapper.ciphertext,
        ivBase64: bootstrap.dek_wrapper.nonce,
        saltBase64: bootstrap.dek_wrapper.prf_salt,
        keyLengthBits,
        argonOpsLimit,
        argonMemoryKb,
        argonType,
    }, masterPassword);

    const optionsResponse = await fetch(route('settings.security.passkeys.options'), {
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
        },
    });

    if (!optionsResponse.ok) {
        throw new Error('Unable to generate passkey options.');
    }

    const options = await optionsResponse.json();
    options.extensions = {
        ...(options.extensions ?? {}),
        prf: {},
    };

    const registrationResponse = await window.startRegistration({ optionsJSON: options });
    const credentialId = typeof registrationResponse?.id === 'string'
        ? registrationResponse.id.trim()
        : '';

    if (credentialId === '') {
        throw new Error('Passkey registration returned an invalid credential id.');
    }

    const prfSaltBytes = window.crypto.getRandomValues(new Uint8Array(PASSKEY_PRF_SALT_BYTES));
    const prfKeyBytes = await derivePasskeyPrfKeyBytes(credentialId, prfSaltBytes);
    const wrappedDekWithPrf = await CryptoEncryptor.encryptBytesWithKey(dekBytes, prfKeyBytes);

    await new Promise((resolve, reject) => {
        router.post(
            route('settings.security.passkeys.store'),
            {
                name,
                options: JSON.stringify(options),
                passkey: JSON.stringify(registrationResponse),
                wrapped_dek: {
                    ciphertext: wrappedDekWithPrf.ciphertextBase64,
                    iv: wrappedDekWithPrf.ivBase64,
                    prf_salt: bufferToBase64URLString(prfSaltBytes),
                    prf_output_length: prfKeyBytes.length,
                },
            },
            {
                preserveScroll: true,
                preserveState: false,
                onSuccess: () => resolve(),
                onError: (errors) => {
                    reject(new Error(errors.passkey ?? 'Unable to register passkey.'));
                },
                onCancel: () => {
                    reject(new Error('Passkey registration was cancelled.'));
                },
            },
        );
    });
};

const openPasskeyRegistrationModal = () => {
    if (!passkeySupported.value) {
        modal.danger({
            title: 'Passkeys unavailable',
            message: 'This browser or device does not support passkeys.',
            confirmLabel: 'Close',
            cancelLabel: null,
        });
        return;
    }

    modal.form({
        title: 'Register passkey',
        message: 'Save a passkey to sign in quickly without typing your master password.',
        confirmLabel: 'Register',
        cancelLabel: 'Cancel',
        fields: [
            {
                name: 'passkeyName',
                label: 'Passkey name',
                placeholder: 'This device',
                required: false,
            },
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
            const passkeyName = values.passkeyName.trim();
            const masterPassword = values.masterPassword.trim();

            if (masterPassword === '') {
                throw new Error('Master password is required.');
            }

            await createPasskey(passkeyName, masterPassword);

            modal.confirmation({
                title: 'Passkey registered',
                message: 'You can now use this passkey to sign in and unlock your DEK wrapper.',
                confirmLabel: 'Close',
                cancelLabel: null,
            });
        },
    });
};

const removePasskey = (passkeyId) => {
    modal.danger({
        title: 'Remove passkey',
        message: 'This passkey will no longer be able to sign in to your account.',
        confirmLabel: 'Remove',
        cancelLabel: 'Cancel',
        onConfirm: async () => {
            await new Promise((resolve, reject) => {
                router.delete(route('settings.security.passkeys.destroy', { passkeyId }), {
                    preserveScroll: true,
                    preserveState: false,
                    onSuccess: () => resolve(),
                    onError: (errors) => {
                        reject(new Error(errors.passkey ?? 'Unable to remove passkey.'));
                    },
                    onCancel: () => {
                        reject(new Error('Passkey removal was cancelled.'));
                    },
                });
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
                                class="shrink-0 rounded-md border px-3 py-1 text-sm font-medium transition-colors"
                                :class="mfaEmailEnabled
                                    ? 'border-red-500 text-red-600 hover:bg-red-600 hover:text-white'
                                    : 'border-primary text-primary hover:bg-primary hover:text-on-primary'"
                                @click="handleEmailAction"
                            >
                                {{ mfaEmailEnabled ? 'Remove' : 'Setup' }}
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
                            class="shrink-0 rounded-md border px-3 py-1 text-sm font-medium transition-colors"
                            :class="passkeySupported
                                ? 'border-primary text-primary hover:bg-primary hover:text-on-primary'
                                : 'border-outline-variant text-on-surface-variant cursor-not-allowed'"
                            :disabled="!passkeySupported"
                            @click="openPasskeyRegistrationModal"
                        >
                            Register
                        </button>
                    </div>

                    <div class="mt-4 border-t border-outline-variant pt-4">
                        <p v-if="!passkeySupported" class="text-sm text-on-surface-variant">
                            This browser does not support passkeys.
                        </p>

                        <p v-else-if="!hasPasskeys" class="text-sm text-on-surface-variant">
                            No passkeys registered yet.
                        </p>

                        <div v-else class="space-y-3">
                            <article
                                v-for="passkey in passkeys"
                                :key="passkey.id"
                                class="flex items-start justify-between gap-4 border-b border-outline-variant pb-3 last:border-b-0 last:pb-0"
                            >
                                <div class="min-w-0">
                                    <p class="font-semibold text-on-surface">{{ passkey.name }}</p>
                                    <p class="mt-1 text-xs text-on-surface-variant">
                                        Registered: {{ formatPasskeyTimestamp(passkey.created_at) }}
                                    </p>
                                    <p class="mt-1 text-xs text-on-surface-variant">
                                        Last used: {{ formatPasskeyTimestamp(passkey.last_used_at) }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="shrink-0 rounded-md border border-red-500 px-3 py-1 text-sm font-medium text-red-600 transition-colors hover:bg-red-600 hover:text-white"
                                    @click="removePasskey(passkey.id)"
                                >
                                    Remove
                                </button>
                            </article>
                        </div>
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
