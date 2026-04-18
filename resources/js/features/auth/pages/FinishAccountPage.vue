<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import { CheckCircle2, Circle, Eye, EyeOff, Lock, Shield } from 'lucide-vue-next';
import { CryptoGenerator } from '../../../shared/utils';
import CryptoEncryptor from '../../../shared/utils/crypto/CryptoEncryptor';
import {
    getPasswordEntropy,
    getPasswordMeterClass,
    getPasswordStrengthLabel,
    MIN_PASSWORD_LENGTH,
    MIN_ZXCVBN_SCORE,
} from '../utils/passwordValidation';
import AuthLayout from '../../../shared/layouts/AuthLayout.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
});

const page = usePage();

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const cryptoError = ref('');

const finishAccountRequest = useForm({
    id: route().params.id,
    password: '',
    confirm_password: '',
    encrypted_private_key: null,
    kdf_salt: '',
    kdf_params: null,
    public_key: ''
});

function getError(key) {
    return finishAccountRequest.errors[key] || page.props.errors?.[key] || '';
}

const encryptionErrorMessage = computed(
    () =>
        cryptoError.value ||
        getError('encrypted_private_key') ||
        getError('encrypted_private_key.ciphertext') ||
        getError('encrypted_private_key.iv') ||
        getError('kdf_salt') ||
        getError('kdf_params') ||
        getError('kdf_params.algorithm') ||
        getError('kdf_params.opsLimit') ||
        getError('kdf_params.memoryKb') ||
        getError('kdf_params.type') ||
        '',
);

const accountErrorMessage = computed(() => getError('email'));

const hasMinLength = computed(() => finishAccountRequest.password.length >= MIN_PASSWORD_LENGTH);
const entropyResult = computed(() => getPasswordEntropy(finishAccountRequest.password, [props.email]));
const entropyScore = computed(() => entropyResult.value?.score ?? 0);
const entropyLabel = computed(() => getPasswordStrengthLabel(entropyScore.value));
const entropyMeterClass = computed(() => getPasswordMeterClass(entropyScore.value));
const entropyMeterWidth = computed(() => {
    if (! finishAccountRequest.password) {
        return '0%';
    }

    return `${((entropyScore.value + 1) / 5) * 100}%`;
});
const hasStrongEntropy = computed(() => entropyScore.value >= MIN_ZXCVBN_SCORE);
const passwordsMatch = computed(
    () =>
        finishAccountRequest.password.length > 0 &&
        finishAccountRequest.confirm_password.length > 0 &&
        finishAccountRequest.password === finishAccountRequest.confirm_password,
);
const isValid = computed(
    () => hasMinLength.value && hasStrongEntropy.value && passwordsMatch.value && !finishAccountRequest.processing,
);

async function handleSubmit() {
    cryptoError.value = '';
    finishAccountRequest.clearErrors();

    if (! hasMinLength.value) {
        finishAccountRequest.setError('password', `Password must be at least ${MIN_PASSWORD_LENGTH} characters.`);
        return;
    }

    if (! hasStrongEntropy.value) {
        finishAccountRequest.setError('password', 'Password entropy must be Strong or Very Strong.');
        return;
    }

    if (finishAccountRequest.password !== finishAccountRequest.confirm_password) {
        finishAccountRequest.setError('confirm_password', 'Passwords do not match.');
        return;
    }

    try {
        const keyPair = await CryptoGenerator.generateClientKeyPair();
        const wrappedPK = await CryptoEncryptor.wrapMasterKeyWithPassword(keyPair.privateKeyBase64, finishAccountRequest.password);
        finishAccountRequest.public_key = keyPair.masterKeyBase64;
        finishAccountRequest.encrypted_private_key = {
            ciphertext: wrappedPK.ciphertextBase64,
            iv: wrappedPK.ivBase64,
        };
        finishAccountRequest.kdf_salt = wrappedPK.saltBase64;
        finishAccountRequest.kdf_params = {
            algorithm: wrappedPK.kdfAlgorithm,
            opsLimit: wrappedPK.argonOpsLimit,
            memoryKb: wrappedPK.argonMemoryKb,
            type: wrappedPK.argonType,
        };
        finishAccountRequest.post(route('finish-account.perform'), {
            preserveScroll: true,
        });
    } catch (_error) {
        cryptoError.value = 'Unable to secure your account keys. Please try again.';
    }
}
</script>

<template>
    <Head title="Finish Account | VaultGuardian" />

    <AuthLayout>
        <div class="relative min-h-full overflow-x-hidden bg-white text-gray-900">
            <div class="pointer-events-none fixed inset-0 opacity-40">
                <div
                    class="absolute inset-0"
                    style="
                        background-image:
                            linear-gradient(rgba(59, 130, 246, 0.08) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(59, 130, 246, 0.08) 1px, transparent 1px);
                        background-size: 60px 60px;
                    "
                />
            </div>

            <div class="fixed left-1/4 top-0 h-96 w-96 rounded-full bg-blue-500 opacity-15 blur-[120px]" />
            <div class="fixed bottom-0 right-1/4 h-96 w-96 rounded-full bg-blue-400 opacity-10 blur-[120px]" />

            <div class="mx-auto max-w-2xl px-6 py-12">
                <div>

                    <h1
                        class="mb-4 text-center text-4xl tracking-tight text-gray-900 md:text-5xl"
                        style="font-family: 'DM Sans', sans-serif; font-weight: 700;"
                    >
                        Create your master password
                    </h1>
                    <p class="mb-3 text-center text-gray-600" style="font-family: 'DM Sans', sans-serif;">
                        Creating account for <span class="text-blue-600" style="font-weight: 600;">{{ props.email }}</span>
                    </p>
                    <p class="mb-12 text-center text-sm text-gray-500" style="font-family: 'DM Sans', sans-serif;">
                        This password encrypts your vault. Make it strong and memorable.
                    </p>
                    <p v-if="accountErrorMessage" class="mb-6 text-center text-sm text-red-600">
                        {{ accountErrorMessage }}
                    </p>
                </div>

                <form class="space-y-6" @submit.prevent="handleSubmit">
                    <div>
                        <label class="mb-2 block text-sm text-gray-700" style="font-family: 'DM Sans', sans-serif; font-weight: 600;">
                            Master Password
                        </label>
                        <div class="relative">
                            <Lock class="absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                id="password"
                                v-model="finishAccountRequest.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                placeholder="Create a strong master password"
                                class="w-full rounded-xl border-2 border-gray-300 py-4 pl-14 pr-14 text-lg transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                                style="font-family: 'DM Sans', sans-serif;"
                            />
                            <button
                                type="button"
                                class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 transition-colors hover:text-gray-600"
                                @click="showPassword = !showPassword"
                            >
                                <EyeOff v-if="showPassword" class="h-5 w-5" />
                                <Eye v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <p v-if="getError('password')" class="mt-2 text-sm text-red-600">
                            {{ getError('password') }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm text-gray-700" style="font-family: 'DM Sans', sans-serif; font-weight: 600;">
                            Confirm Master Password
                        </label>
                        <div class="relative">
                            <Lock class="absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                id="confirm_password"
                                v-model="finishAccountRequest.confirm_password"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                placeholder="Re-enter your master password"
                                class="w-full rounded-xl border-2 border-gray-300 py-4 pl-14 pr-14 text-lg transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                                style="font-family: 'DM Sans', sans-serif;"
                            />
                            <button
                                type="button"
                                class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 transition-colors hover:text-gray-600"
                                @click="showConfirmPassword = !showConfirmPassword"
                            >
                                <EyeOff v-if="showConfirmPassword" class="h-5 w-5" />
                                <Eye v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <p v-if="getError('confirm_password')" class="mt-2 text-sm text-red-600">
                            {{ getError('confirm_password') }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100/50 p-6">
                        <p class="mb-4 text-sm text-gray-900" style="font-family: 'DM Sans', sans-serif; font-weight: 700;">
                            Password Requirements
                        </p>
                        <div class="mb-4">
                            <div class="mb-2 flex items-center justify-between text-xs">
                                <span class="text-gray-600" style="font-family: 'DM Sans', sans-serif;">Entropy strength</span>
                                <span class="font-semibold text-gray-900" style="font-family: 'DM Sans', sans-serif;">
                                    {{ entropyLabel }} ({{ entropyScore }}/4)
                                </span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-blue-100">
                                <div
                                    class="h-full rounded-full transition-all"
                                    :class="entropyMeterClass"
                                    :style="{ width: entropyMeterWidth }"
                                />
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <CheckCircle2 v-if="hasMinLength" class="h-5 w-5 shrink-0 text-blue-600" />
                                <Circle v-else class="h-5 w-5 shrink-0 text-gray-400" />
                                <span
                                    class="text-sm"
                                    :class="hasMinLength ? 'text-gray-900' : 'text-gray-500'"
                                    style="font-family: 'DM Sans', sans-serif;"
                                >
                                    At least 12 characters
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <CheckCircle2 v-if="hasStrongEntropy" class="h-5 w-5 shrink-0 text-blue-600" />
                                <Circle v-else class="h-5 w-5 shrink-0 text-gray-400" />
                                <span
                                    class="text-sm"
                                    :class="hasStrongEntropy ? 'text-gray-900' : 'text-gray-500'"
                                    style="font-family: 'DM Sans', sans-serif;"
                                >
                                    Strong entropy score
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <CheckCircle2 v-if="passwordsMatch" class="h-5 w-5 shrink-0 text-blue-600" />
                                <Circle v-else class="h-5 w-5 shrink-0 text-gray-400" />
                                <span
                                    class="text-sm"
                                    :class="passwordsMatch ? 'text-gray-900' : 'text-gray-500'"
                                    style="font-family: 'DM Sans', sans-serif;"
                                >
                                    Passwords match
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <div class="flex gap-3">
                            <Shield class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />
                            <div>
                                <p class="mb-1 text-sm text-amber-900" style="font-family: 'DM Sans', sans-serif; font-weight: 600;">
                                    Important: Keep this password safe
                                </p>
                                <p class="text-sm text-amber-800" style="font-family: 'DM Sans', sans-serif;">
                                    We cannot reset your master password. If you forget it, you'll lose access to your vault permanently.
                                </p>
                            </div>
                        </div>
                    </div>

                    <p
                        v-if="encryptionErrorMessage"
                        class="rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700"
                    >
                        {{ encryptionErrorMessage }}
                    </p>

                    <button
                        type="submit"
                        :disabled="!isValid"
                        class="w-full rounded-xl py-4 text-white transition-all shadow-lg"
                        :class="isValid
                            ? 'cursor-pointer bg-blue-600 shadow-blue-600/20 hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/30'
                            : 'cursor-not-allowed bg-gray-300 shadow-gray-300/20'"
                        style="font-family: 'DM Sans', sans-serif; font-weight: 600;"
                    >
                        {{ finishAccountRequest.processing ? 'Creating Vault...' : 'Create My Vault' }}
                    </button>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>
