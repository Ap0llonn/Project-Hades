<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { CryptoGenerator } from '@/shared/utils';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import { UserPlus } from 'lucide-vue-next';
import AuthSplitLayout from '../components/AuthSplitLayout.vue';
import AuthTextField from '../components/AuthTextField.vue';
import CryptoEncryptor from '@/shared/utils/crypto/CryptoEncryptor';

const signupRequest = useForm({
    email: '',
    password: '',
    confirm_password: '',
    firstName: '',
    lastName: '',

    encrypted_master_key: null,
    kdf_salt: '',
    kdf_params: null,
});

const cryptoError = ref('');

async function handleSubmit() {
    cryptoError.value = '';
    signupRequest.clearErrors();

    try {
        const masterKey = await CryptoGenerator.generateMasterKey();
        const wrappedMK = await CryptoEncryptor.wrapMasterKeyWithPassword(
            masterKey,
            signupRequest.password,
        );

        signupRequest.encrypted_master_key = {
            ciphertext: wrappedMK.ciphertextBase64,
            iv: wrappedMK.ivBase64,
        };

        signupRequest.kdf_salt = wrappedMK.saltBase64;

        signupRequest.kdf_params = {
            algorithm: wrappedMK.kdfAlgorithm,
            opsLimit: wrappedMK.argonOpsLimit,
            memoryKb: wrappedMK.argonMemoryKb,
            type: wrappedMK.argonType,
        };

        signupRequest.post(route('signup.perform'));
    } catch (_error) {
        cryptoError.value = 'Unable to secure your account keys. Please try again.';
    }
}
</script>

<template>
    <Head title="Sign Up | The Vault" />

    <AuthSplitLayout
        title="Create your secure account"
        subtitle="Set up your vault with strong password rules, encrypted key wrapping, and recovery-ready protection."
    >
        <div class="mb-8 flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-on-primary">
                <UserPlus class="h-5 w-5" />
            </span>
            <div>
                <h2 class="text-2xl font-bold text-on-surface">Sign Up</h2>
                <p class="text-sm text-on-surface-variant">Create your account in less than a minute.</p>
            </div>
        </div>

        <form class="grid gap-5 sm:grid-cols-2" @submit.prevent="handleSubmit">
            <AuthTextField
                v-model="signupRequest.firstName"
                id="first_name"
                label="First name"
                name="firstName"
                placeholder="Sam"
                autocomplete="given-name"
                :required="true"
            />
            <p
                v-if="signupRequest.errors.firstName"
                class="sm:col-span-1 -mt-3 text-xs text-red-600"
            >
                {{ signupRequest.errors.firstName }}
            </p>
            <AuthTextField
                v-model="signupRequest.lastName"
                id="last_name"
                label="Last name"
                name="lastName"
                placeholder="Tremblay"
                autocomplete="family-name"
                :required="true"
            />
            <p
                v-if="signupRequest.errors.lastName"
                class="sm:col-span-1 -mt-3 text-xs text-red-600"
            >
                {{ signupRequest.errors.lastName }}
            </p>
            <div class="sm:col-span-2">
                <AuthTextField
                    v-model="signupRequest.email"
                    id="email"
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="you@company.com"
                    autocomplete="email"
                    :required="true"
                />
                <p
                    v-if="signupRequest.errors.email"
                    class="mt-2 text-xs text-red-600"
                >
                    {{ signupRequest.errors.email }}
                </p>
            </div>
            <div class="sm:col-span-2">
                <AuthTextField
                    v-model="signupRequest.password"
                    id="password"
                    label="Password"
                    name="password"
                    type="password"
                    placeholder="Choose a strong password"
                    autocomplete="new-password"
                    :required="true"
                    helper="Use at least 12 characters with uppercase, lowercase, number, and symbol."
                />
                <p
                    v-if="signupRequest.errors.password"
                    class="mt-2 text-xs text-red-600"
                >
                    {{ signupRequest.errors.password }}
                </p>
            </div>
            <div class="sm:col-span-2">
                <AuthTextField
                    v-model="signupRequest.confirm_password"
                    id="confirm-password"
                    label="Password confirmation"
                    name="confirm_password"
                    type="password"
                    placeholder="confirm your password"
                    :required="true"
                />
                <p
                    v-if="signupRequest.errors.confirm_password"
                    class="mt-2 text-xs text-red-600"
                >
                    {{ signupRequest.errors.confirm_password }}
                </p>
            </div>
            <div class="sm:col-span-2">
                <p
                    v-if="cryptoError || signupRequest.errors.encrypted_master_key || signupRequest.errors['encrypted_master_key.ciphertext'] || signupRequest.errors['encrypted_master_key.iv'] || signupRequest.errors.kdf_salt || signupRequest.errors.kdf_params || signupRequest.errors['kdf_params.algorithm'] || signupRequest.errors['kdf_params.opsLimit'] || signupRequest.errors['kdf_params.memoryKb'] || signupRequest.errors['kdf_params.type']"
                    class="rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700"
                >
                    {{
                        cryptoError ||
                        signupRequest.errors.encrypted_master_key ||
                        signupRequest.errors['encrypted_master_key.ciphertext'] ||
                        signupRequest.errors['encrypted_master_key.iv'] ||
                        signupRequest.errors.kdf_salt ||
                        signupRequest.errors.kdf_params ||
                        signupRequest.errors['kdf_params.algorithm'] ||
                        signupRequest.errors['kdf_params.opsLimit'] ||
                        signupRequest.errors['kdf_params.memoryKb'] ||
                        signupRequest.errors['kdf_params.type']
                    }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="signupRequest.processing"
                class="sm:col-span-2 mt-1 w-full rounded-2xl bg-primary px-5 py-3 text-sm font-bold text-on-primary shadow-md transition-all hover:bg-primary-container active:scale-95 disabled:cursor-not-allowed disabled:opacity-70"
            >
                {{ signupRequest.processing ? 'Creating Account...' : 'Create Account' }}
            </button>
        </form>

        <p class="mt-6 text-sm text-on-surface-variant">
            Already have an account?
            <Link href="/login" class="font-semibold text-primary hover:text-primary-container">
                Sign in
            </Link>
        </p>
    </AuthSplitLayout>
</template>
