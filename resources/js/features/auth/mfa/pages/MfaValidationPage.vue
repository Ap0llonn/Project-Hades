<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { AlertCircle, ArrowLeft, Bell, KeyRound, ShieldCheck, Smartphone } from 'lucide-vue-next';
import { computed, ref, watchEffect } from 'vue';
import { route } from 'ziggy-js';
import AuthLayout from '../../../../shared/layouts/AuthLayout.vue';

const props = defineProps({
    challenge: {
        type: Object,
        default: () => ({}),
    },
});

const method = ref('totp');
const totpCode = ref('');
const emailCode = ref('');
const recoveryCode = ref('');
const isSendingEmailCode = ref(false);
const emailCodeMessage = ref('');
const hasRequestedEmailCode = ref(false);
const error = ref('');

const availableMethods = computed(() => {
    const methods = props.challenge?.methods ?? {};

    return {
        totp: Boolean(methods.totp),
        email: Boolean(methods.email),
        recovery: Boolean(methods.recovery),
    };
});

const availableMethodOrder = computed(() => {
    const order = ['totp', 'email', 'recovery'];
    return order.filter((item) => availableMethods.value[item]);
});

watchEffect(() => {
    if (!availableMethodOrder.value.includes(method.value)) {
        method.value = availableMethodOrder.value[0] ?? 'totp';
    }
});

watchEffect(() => {
    if (method.value === 'email' && availableMethods.value.email && !hasRequestedEmailCode.value) {
        requestEmailCode();
    }
});

const isTotpCodeComplete = computed(() => totpCode.value.length === 6);
const isEmailCodeComplete = computed(() => emailCode.value.length === 6);
const canSubmitRecoveryCode = computed(() => recoveryCode.value.trim().length > 0);

const canSubmit = computed(() => {
    if (method.value === 'recovery') {
        return canSubmitRecoveryCode.value;
    }

    if (method.value === 'email') {
        return isEmailCodeComplete.value;
    }

    return isTotpCodeComplete.value;
});

const titleText = computed(() => {
    if (method.value === 'recovery') {
        return 'Enter one of your recovery codes';
    }

    if (method.value === 'email') {
        return 'Enter the code sent to your email';
    }

    return 'Enter the code from your authenticator app';
});

const activeCode = computed(() => {
    if (method.value === 'recovery') {
        return recoveryCode.value;
    }

    if (method.value === 'email') {
        return emailCode.value;
    }

    return totpCode.value;
});

const inputPlaceholder = computed(() => {
    if (method.value === 'recovery') {
        return 'XXXX-XXXX-XXXX';
    }

    return '000000';
});

const inputMaxLength = computed(() => (method.value === 'recovery' ? 14 : 6));
const emailHint = computed(() => props.challenge?.emailHint ?? '');

function requestEmailCode(force = false) {
    if (!availableMethods.value.email || isSendingEmailCode.value) {
        return;
    }

    if (!force && hasRequestedEmailCode.value) {
        return;
    }

    error.value = '';
    emailCodeMessage.value = '';
    isSendingEmailCode.value = true;

    useForm({ force: Boolean(force) }).post(route('mfa.email.request-challenge'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            hasRequestedEmailCode.value = true;
            emailCodeMessage.value = 'Verification code ready. Check your inbox.';
        },
        onError: (errors) => {
            if (!force) {
                hasRequestedEmailCode.value = false;
            }
            error.value = errors.code ?? 'Unable to send email verification code.';
        },
        onFinish: () => {
            isSendingEmailCode.value = false;
        },
    });
}

const switchMethod = (nextMethod) => {
    if (!availableMethods.value[nextMethod]) {
        return;
    }

    method.value = nextMethod;
    totpCode.value = '';
    emailCode.value = '';
    recoveryCode.value = '';
    emailCodeMessage.value = '';
    error.value = '';

    if (nextMethod === 'email' && !hasRequestedEmailCode.value) {
        requestEmailCode();
    }
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

    if (method.value === 'email') {
        emailCode.value = rawValue.replace(/\D/g, '').slice(0, 6);
        return;
    }

    totpCode.value = rawValue.replace(/\D/g, '').slice(0, 6);
};

const challengeRequest = useForm({
    method: '',
    code: '',
});

function verifyCode() {
    error.value = '';
    challengeRequest.method = method.value;
    challengeRequest.code = activeCode.value.trim();

    challengeRequest.post(route('mfa.totp.verify-challenge'), {
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
                            v-if="availableMethods.totp"
                            type="button"
                            class="rounded-xl border px-4 py-3 text-left transition-colors"
                            :class="method === 'totp'
                                ? 'border-blue-500 bg-blue-50 text-blue-800'
                                : 'border-gray-300 bg-white text-gray-700 hover:border-blue-300'"
                            @click="switchMethod('totp')"
                        >
                            <span class="mb-1 flex items-center gap-2 text-sm" style="font-weight: 600;">
                                <Smartphone class="h-4 w-4" />
                                Authenticator app
                            </span>
                            <span class="block text-xs" :class="method === 'totp' ? 'text-blue-700' : 'text-gray-600'">
                                Use your authenticator app code.
                            </span>
                        </button>

                        <button
                            v-if="availableMethods.email"
                            type="button"
                            class="rounded-xl border px-4 py-3 text-left transition-colors"
                            :class="method === 'email'
                                ? 'border-blue-500 bg-blue-50 text-blue-800'
                                : 'border-gray-300 bg-white text-gray-700 hover:border-blue-300'"
                            @click="switchMethod('email')"
                        >
                            <span class="mb-1 flex items-center gap-2 text-sm" style="font-weight: 600;">
                                <Bell class="h-4 w-4" />
                                Email code
                            </span>
                            <span class="block text-xs" :class="method === 'email' ? 'text-blue-700' : 'text-gray-600'">
                                Receive a one-time code by email.
                            </span>
                        </button>

                        <button
                            v-if="availableMethods.recovery"
                            type="button"
                            class="rounded-xl border px-4 py-3 text-left transition-colors"
                            :class="method === 'recovery'
                                ? 'border-blue-500 bg-blue-50 text-blue-800'
                                : 'border-gray-300 bg-white text-gray-700 hover:border-blue-300'"
                            @click="switchMethod('recovery')"
                        >
                            <span class="mb-1 flex items-center gap-2 text-sm" style="font-weight: 600;">
                                <KeyRound class="h-4 w-4" />
                                Recovery code
                            </span>
                            <span class="block text-xs" :class="method === 'recovery' ? 'text-blue-700' : 'text-gray-600'">
                                Use one of your saved recovery codes.
                            </span>
                        </button>
                    </div>

                    <div v-if="method === 'totp'" class="mb-6 rounded-xl bg-gray-50 p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-100 to-indigo-50">
                                <Smartphone class="h-5 w-5 text-indigo-600" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900" style="font-weight: 600;">
                                    Authenticator App
                                </p>
                                <p class="text-xs text-gray-600">
                                    Open your app to view the code.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="method === 'email'" class="mb-6 rounded-xl bg-gray-50 p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-100 to-indigo-50">
                                <Bell class="h-5 w-5 text-indigo-600" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900" style="font-weight: 600;">
                                    Email Verification
                                </p>
                                <p class="text-xs text-gray-600">
                                    A 6-digit code will be sent to your email address.
                                </p>
                            </div>
                            <button
                                type="button"
                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-700 transition-colors hover:bg-gray-100"
                                :disabled="isSendingEmailCode"
                                @click="requestEmailCode(true)"
                            >
                                {{ isSendingEmailCode ? 'Sending...' : 'Resend code' }}
                            </button>
                        </div>
                        <p v-if="emailCodeMessage" class="mt-2 text-xs text-green-600">
                            {{ emailCodeMessage }}
                        </p>
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

                    <p class="mb-8 text-xs text-gray-500">
                        Available methods are shown based on your account security settings.
                    </p>

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
                            {{ challengeRequest.processing ? 'Verifying...' : 'Verify' }}
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
