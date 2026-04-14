<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import { Eye, EyeOff, Fingerprint, Lock, Shield } from 'lucide-vue-next';
import { CryptoGenerator } from '../../../shared/utils';
import CryptoEncryptor from '../../../shared/utils/crypto/CryptoEncryptor';
import AuthLayout from '../../../shared/layouts/AuthLayout.vue';
import PasswordEntropyVerification from '../components/PasswordEntropyVerification.vue';

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
const passwordValidation = ref({
    message: '',
    score: 0,
    isEntropyMet: false,
});

const finishAccountRequest = useForm({
    id: route().params.id,
    password: '',
    confirm_password: '',
    encrypted_master_key: null,
    kdf_salt: '',
    kdf_params: null,
});

function getError(key) {
    return finishAccountRequest.errors[key] || page.props.errors?.[key] || '';
}

const encryptionErrorMessage = computed(
    () =>
        cryptoError.value ||
        getError('encrypted_master_key') ||
        getError('encrypted_master_key.ciphertext') ||
        getError('encrypted_master_key.iv') ||
        getError('kdf_salt') ||
        getError('kdf_params') ||
        getError('kdf_params.algorithm') ||
        getError('kdf_params.opsLimit') ||
        getError('kdf_params.memoryKb') ||
        getError('kdf_params.type') ||
        '',
);

const accountErrorMessage = computed(() => getError('email'));

function handlePasswordValidationChange(nextValidationState) {
    passwordValidation.value = nextValidationState;
}

async function handleSubmit() {
    cryptoError.value = '';
    finishAccountRequest.clearErrors();

    const passwordValidationMessage = passwordValidation.value.message;
    if (passwordValidationMessage) {
        finishAccountRequest.setError('password', passwordValidationMessage);
        return;
    }

    if (finishAccountRequest.password !== finishAccountRequest.confirm_password) {
        finishAccountRequest.setError('confirm_password', 'Passwords do not match.');
        return;
    }

    try {
        const masterKey = await CryptoGenerator.generateMasterKey();
        const wrappedMK = await CryptoEncryptor.wrapMasterKeyWithPassword(masterKey, finishAccountRequest.password);

        finishAccountRequest.encrypted_master_key = {
            ciphertext: wrappedMK.ciphertextBase64,
            iv: wrappedMK.ivBase64,
        };
        finishAccountRequest.kdf_salt = wrappedMK.saltBase64;
        finishAccountRequest.kdf_params = {
            algorithm: wrappedMK.kdfAlgorithm,
            opsLimit: wrappedMK.argonOpsLimit,
            memoryKb: wrappedMK.argonMemoryKb,
            type: wrappedMK.argonType,
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

            <section class="relative z-10 mx-auto w-full max-w-2xl px-6 pb-20 pt-14">
                <div class="mb-10 text-center">
                    <h1 class="mb-4 text-4xl font-bold tracking-tight text-gray-900 md:text-5xl">Finish account setup</h1>
                    <p class="text-gray-600">Email verified for:</p>
                    <p class="font-semibold text-blue-700">{{ props.email }}</p>
                    <p v-if="accountErrorMessage" class="mt-3 text-sm text-red-600">
                        {{ accountErrorMessage }}
                    </p>
                </div>

                <form class="rounded-3xl border border-gray-200 bg-white p-6 shadow-xl sm:p-8" @submit.prevent="handleSubmit">
                    <div class="mb-5 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        Important: your master password cannot be changed later. Store it safely before continuing.
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Master password</label>
                        <div class="relative">
                            <Lock class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                id="password"
                                v-model="finishAccountRequest.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                placeholder="Choose a strong master password"
                                class="w-full rounded-xl border border-gray-300 py-3.5 pl-12 pr-12 transition-all focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            />
                            <button
                                type="button"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors hover:text-gray-600"
                                @click="showPassword = !showPassword"
                            >
                                <EyeOff v-if="showPassword" class="h-5 w-5" />
                                <Eye v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            At least 12 chars, mixed case, number, symbol, and strong entropy.
                        </p>
                        <p v-if="getError('password')" class="mt-2 text-xs text-red-600">
                            {{ getError('password') }}
                        </p>
                        <PasswordEntropyVerification
                            :password="finishAccountRequest.password"
                            :email="props.email"
                            @validation-change="handlePasswordValidationChange"
                        />
                    </div>

                    <div class="mt-5">
                        <label for="confirm_password" class="mb-2 block text-sm font-semibold text-gray-700">
                            Confirm master password
                        </label>
                        <div class="relative">
                            <Lock class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                id="confirm_password"
                                v-model="finishAccountRequest.confirm_password"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                placeholder="Confirm your password"
                                class="w-full rounded-xl border border-gray-300 py-3.5 pl-12 pr-12 transition-all focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            />
                            <button
                                type="button"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors hover:text-gray-600"
                                @click="showConfirmPassword = !showConfirmPassword"
                            >
                                <EyeOff v-if="showConfirmPassword" class="h-5 w-5" />
                                <Eye v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <p
                            v-if="getError('confirm_password')"
                            class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700"
                        >
                            {{ getError('confirm_password') }}
                        </p>
                    </div>

                    <p
                        v-if="encryptionErrorMessage"
                        class="mt-5 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700"
                    >
                        {{ encryptionErrorMessage }}
                    </p>

                    <button
                        type="submit"
                        :disabled="finishAccountRequest.processing"
                        class="mt-6 w-full rounded-xl bg-blue-600 px-5 py-4 font-semibold text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/30 disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        {{ finishAccountRequest.processing ? 'Securing Account...' : 'Finish Setup' }}
                    </button>
                </form>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-4 text-sm text-gray-500 md:gap-8">
                    <div class="flex items-center gap-2">
                        <Shield class="h-4 w-4 text-blue-600" />
                        <span>256-bit AES encryption</span>
                    </div>
                    <div class="hidden h-4 w-px bg-gray-300 md:block" />
                    <div class="flex items-center gap-2">
                        <Fingerprint class="h-4 w-4 text-blue-600" />
                        <span>Biometric authentication</span>
                    </div>
                </div>
            </section>
        </div>
    </AuthLayout>
</template>
