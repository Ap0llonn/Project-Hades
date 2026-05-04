<script setup>
import {computed, ref, watch} from 'vue';
import {router, usePage} from '@inertiajs/vue3';
import {route} from 'ziggy-js';
import {base64URLStringToBuffer, bufferToBase64URLString} from '@simplewebauthn/browser';
import {Shield} from 'lucide-vue-next';
import {useModal} from '../../../../../../shared/modal/index.ts';
import {CryptoDecryptor, CryptoEncryptor} from '../../../../../../shared/utils';
import {dekService} from '../../../../../../shared/services/dekService';

const modal = useModal();
const page = usePage();

const settingProps = defineProps({
    security: {
        type: Object,
        default: () => ({
            mfa_activated: false,
            totp_enabled: false,
            email_enabled: false,
            passkeys: [],
        }),
    },
});

const passkeys = ref([]);
const passkeySupported = ref(typeof window.browserSupportsWebAuthn === 'function' && window.browserSupportsWebAuthn());
const hasPasskeys = computed(() => passkeys.value.length > 0);
const autoPromptTriggered = ref(false);

watch(
    () => settingProps.security,
    (value) => {
        passkeys.value = Array.isArray(value?.passkeys) ? value.passkeys : [];
    },
    {immediate: true, deep: true},
);

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

    const assertion = await navigator.credentials.get({publicKey});
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
    if (!bootstrap?.dek_wrapper) {
        throw new Error('No DEK wrapper is available for this account.');
    }

    let dekBytes;

    if (bootstrap.dek_wrapper.type === 'password') {
        if (masterPassword === '') {
            throw new Error('Master password is required.');
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

        dekBytes = await CryptoDecryptor.decryptBytesWithPassword({
            ciphertextBase64: bootstrap.dek_wrapper.ciphertext,
            ivBase64: bootstrap.dek_wrapper.nonce,
            saltBase64: bootstrap.dek_wrapper.prf_salt,
            keyLengthBits,
            argonOpsLimit,
            argonMemoryKb,
            argonType,
        }, masterPassword);
    } else if (bootstrap.dek_wrapper.type === 'passkey') {
        const credentialId = typeof bootstrap.dek_wrapper.credential_id === 'string'
            ? bootstrap.dek_wrapper.credential_id.trim()
            : '';
        const prfSalt = typeof bootstrap.dek_wrapper.prf_salt === 'string'
            ? bootstrap.dek_wrapper.prf_salt.trim()
            : '';

        if (credentialId === '' || prfSalt === '') {
            throw new Error('Current passkey wrapper is missing PRF parameters.');
        }

        const prfSaltBytes = new Uint8Array(base64URLStringToBuffer(prfSalt));
        const prfKeyBytes = await derivePasskeyPrfKeyBytes(credentialId, prfSaltBytes);

        dekBytes = await CryptoDecryptor.decryptBytesWithKey({
            ciphertextBase64: bootstrap.dek_wrapper.ciphertext,
            ivBase64: bootstrap.dek_wrapper.nonce,
        }, prfKeyBytes);
    } else {
        throw new Error('Current authentication method cannot register a new passkey wrapper.');
    }

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

    const registrationResponse = await window.startRegistration({optionsJSON: options});
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
                required: false,
            },
        ],
        onSubmit: async (values) => {
            const passkeyName = values.passkeyName.trim();
            const masterPassword = values.masterPassword.trim();

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

watch(
    () => page.props?.security?.oauth_passkey_prompt,
    (value) => {
        if (autoPromptTriggered.value || !value || !passkeySupported.value) {
            return;
        }

        autoPromptTriggered.value = true;
        openPasskeyRegistrationModal();
    },
    { immediate: true },
);

const removePasskey = (passkeyId) => {
    modal.danger({
        title: 'Remove passkey',
        message: 'This passkey will no longer be able to sign in to your account.',
        confirmLabel: 'Remove',
        cancelLabel: 'Cancel',
        onConfirm: async () => {
            await new Promise((resolve, reject) => {
                router.delete(route('settings.security.passkeys.destroy', {passkeyId}), {
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
    <div class="py-5">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
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
</template>
