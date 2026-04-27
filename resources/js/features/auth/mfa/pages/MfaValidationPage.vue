<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { AlertCircle, ArrowLeft, ChevronDown, KeyRound, ShieldCheck, Smartphone } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import AuthLayout from '../../../../shared/layouts/AuthLayout.vue';

const props = defineProps({
    challenge: {
        type: Object,
        default: () => ({}),
    },
});

const method = ref('app');
const appCode = ref('');
const recoveryCode = ref('');
const isRecoveryMenuOpen = ref(false);
const error = ref('');

const isAppCodeComplete = computed(() => appCode.value.length === 6);
const canSubmitRecoveryCode = computed(() => recoveryCode.value.trim().length > 0);
const canSubmit = computed(() => {
    if (method.value === 'app') {
        return isAppCodeComplete.value;
    }

    return canSubmitRecoveryCode.value;
});

const titleText = computed(() => {
    if (method.value === 'recovery') {
        return 'Enter one of your recovery codes';
    }

    return 'Enter the code from your authenticator app';
});

const activeCode = computed(() => {
    return method.value === 'recovery' ? recoveryCode.value : appCode.value;
});

const inputPlaceholder = computed(() => (method.value === 'recovery' ? 'XXXX-XXXX-XXXX' : '000000'));
const inputMaxLength = computed(() => (method.value === 'recovery' ? 14 : 6));
const emailHint = computed(() => props.challenge?.emailHint ?? '');

const switchMethod = (nextMethod) => {
    method.value = nextMethod;
    appCode.value = '';
    recoveryCode.value = '';
    isRecoveryMenuOpen.value = false;
    error.value = '';
};

const handleCodeInput = (event) => {
    const rawValue = event.target.value;
    error.value = '';

    if (method.value === 'recovery') {
        recoveryCode.value = rawValue
            .toUpperCase()
            .replace(/[^A-Z0-9-]/g, '')
            .slice(0, 14);
        return;
    }

    appCode.value = rawValue.replace(/\D/g, '').slice(0, 6);
};

const selectRecoveryMethod = () => {
    switchMethod('recovery');
};

const totpRequest = useForm({
    code: '',
});

function verifyCode() {
    error.value = '';
    totpRequest.code = activeCode.value.trim();

    totpRequest.post(route('mfa.totp.verify-challenge'), {
        preserveScroll: true,
        onError: (errors) => {
            error.value = errors.code ?? 'Unable to verify code.';
        },
    });
}
</script>

<template>
    <Head title="Multi-factor Authentication | VaultGuardian" />

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

            <div class="relative z-10 flex min-h-[calc(100vh-120px)] items-center justify-center px-6 py-12">
                <div class="w-full max-w-md">
                <a
                    :href="route('login')"
                    class="mb-5 inline-flex items-center gap-2 text-sm text-gray-600 transition-colors hover:text-blue-600"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back to login
                </a>

                <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-xl shadow-blue-500/10">
                    <div class="mb-8 text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-100 to-blue-50">
                            <ShieldCheck class="h-8 w-8 text-blue-600" />
                        </div>
                        <h1 class="mb-2 text-3xl tracking-tight text-gray-900" style="font-family: 'DM Sans', sans-serif; font-weight: 600;">
                            Two-Factor Authentication
                        </h1>
                        <p class="text-gray-600" style="font-family: 'DM Sans', sans-serif;">
                            {{ titleText }}
                        </p>
                        <p v-if="emailHint" class="mt-2 text-xs text-gray-500">
                            Verifying account {{ emailHint }}
                        </p>
                    </div>

                    <div class="mb-6 grid grid-cols-1 gap-3">
                        <button
                            type="button"
                            class="rounded-xl border px-4 py-3 text-left transition-colors"
                            :class="method === 'app'
                                ? 'border-blue-500 bg-blue-50 text-blue-800 dark:!border-blue-400 dark:!bg-slate-800 dark:!text-blue-100'
                                : 'border-gray-300 bg-white text-gray-700 hover:border-blue-300 dark:border-slate-600 dark:bg-slate-900/50 dark:text-slate-100 dark:hover:border-blue-400'"
                            @click="switchMethod('app')"
                        >
                            <span class="mb-1 flex items-center gap-2 text-sm" style="font-weight: 600;">
                                <Smartphone class="h-4 w-4" />
                                Auth app
                            </span>
                            <span
                                class="block text-xs"
                                :class="method === 'app' ? 'text-blue-700 dark:!text-blue-200' : 'text-gray-600 dark:text-slate-300'"
                            >
                                Use your authenticator app code.
                            </span>
                        </button>

                    </div>

                    <div v-if="method !== 'recovery'" class="mb-6 rounded-xl bg-gray-50 p-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg"
                                :class="'bg-gradient-to-br from-indigo-100 to-indigo-50'"
                            >
                                <Smartphone class="h-5 w-5 text-indigo-600" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900" style="font-weight: 600;">
                                    Authenticator App
                                </p>
                                <p class="text-xs text-gray-600">
                                    Open your app to view the code
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="mb-3 block text-sm text-gray-700" style="font-weight: 500;">
                            {{ method === 'recovery' ? 'Recovery Code' : 'Verification Code' }}
                        </label>
                        <input
                            type="text"
                            :value="activeCode"
                            :placeholder="inputPlaceholder"
                            :maxlength="inputMaxLength"
                            autocomplete="one-time-code"
                            class="w-full rounded-xl border-2 border-gray-300 bg-white px-6 py-4 text-center text-2xl tracking-[0.45em] text-gray-900 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @input="handleCodeInput"
                        />
                        <p v-if="error" class="mt-2 flex items-center gap-2 text-sm text-red-600">
                            <AlertCircle class="h-4 w-4" />
                            {{ error }}
                        </p>
                    </div>

                    <div class="mb-8">
                        <div class="relative">
                            <button
                                type="button"
                                class="group flex w-full items-center justify-between rounded-lg bg-gray-50 px-4 py-3 transition-colors hover:bg-gray-100 dark:bg-slate-800 dark:hover:bg-slate-700"
                                @click="isRecoveryMenuOpen = !isRecoveryMenuOpen"
                            >
                                <div class="flex items-center gap-3">
                                    <KeyRound class="h-5 w-5 text-gray-600 dark:text-slate-200" />
                                    <span class="text-sm text-gray-700 dark:text-slate-100" style="font-weight: 600;">
                                        More options
                                    </span>
                                </div>
                                <ChevronDown class="h-4 w-4 text-gray-500 transition-transform dark:text-slate-300" :class="isRecoveryMenuOpen ? 'rotate-180' : ''" />
                            </button>

                            <Transition
                                enter-active-class="transition duration-150 ease-out"
                                enter-from-class="translate-y-1 opacity-0"
                                enter-to-class="translate-y-0 opacity-100"
                                leave-active-class="transition duration-100 ease-in"
                                leave-from-class="translate-y-0 opacity-100"
                                leave-to-class="translate-y-1 opacity-0"
                            >
                                <div
                                    v-if="isRecoveryMenuOpen"
                                    class="absolute left-0 z-20 mt-2 w-full rounded-lg border border-gray-200 bg-white p-1 shadow-lg dark:border-slate-600 dark:bg-slate-800"
                                >
                                    <button
                                        type="button"
                                        class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm text-gray-700 transition-colors hover:bg-gray-100 dark:text-slate-100 dark:hover:bg-slate-700"
                                        @click="selectRecoveryMethod"
                                    >
                                        <KeyRound class="h-4 w-4" />
                                        Recovery code
                                    </button>
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <div class="mb-6 flex gap-3">
                        <a
                            :href="route('login')"
                            class="flex-1 rounded-xl border border-gray-300 px-6 py-4 text-center text-gray-700 transition-colors hover:bg-gray-50 dark:border-slate-500 dark:text-slate-100 dark:hover:bg-slate-900/40"
                            style="font-weight: 600;"
                        >
                            Back
                        </a>
                        <button
                            type="button"
                            :disabled="!canSubmit"
                            class="flex-1 rounded-xl cursor-pointer bg-blue-600 px-6 py-4 text-white shadow-lg shadow-blue-600/20 transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-700 dark:disabled:bg-slate-700 dark:disabled:text-slate-300"
                            style="font-weight: 600;"
                            @click="verifyCode"
                        >
                            {{ totpRequest.processing ? 'Verifying...' : 'Verify' }}
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Having trouble?
                            <button type="button" class="text-blue-600 hover:underline" style="font-weight: 500;">
                                Contact support
                            </button>
                        </p>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </AuthLayout>
</template>
