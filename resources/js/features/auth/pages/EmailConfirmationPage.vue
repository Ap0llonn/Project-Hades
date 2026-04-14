<script setup>
import {Head, Link, useForm} from '@inertiajs/vue3';
import {ArrowRight, Lock, Mail, RefreshCw} from 'lucide-vue-next';
import {route} from 'ziggy-js';
import AuthLayout from "@/shared/layouts/AuthLayout.vue";

const props = defineProps({
    email: {
        type: String,
        default: '',
    },
});

const resendForm = useForm({
    email: props.email,
});

function handleResend() {
    if (!resendForm.email) {
        return;
    }

    resendForm.post(route('start-account.perform'));
}
</script>

<template>
    <Head title="Confirm Email | VaultGuardian"/>
    <AuthLayout>
        <div class="relative min-h-screen overflow-x-hidden bg-white text-gray-900">
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

            <div class="fixed left-1/4 top-0 h-96 w-96 rounded-full bg-blue-500 opacity-[0.15] blur-[120px]"/>
            <div class="fixed bottom-0 right-1/4 h-96 w-96 rounded-full bg-blue-400 opacity-[0.12] blur-[120px]"/>

            <div class="relative z-10">


                <div class="mx-auto max-w-2xl px-6 py-20">
                    <div class="flex flex-col items-center text-center">


                        <h1
                            class="animate-fade-up mb-6 text-5xl tracking-tight text-gray-900 md:text-6xl"
                            style="animation-delay: 240ms; font-family: 'DM Sans', sans-serif; font-weight: 700; line-height: 1.1;"
                        >
                            Please check your email
                        </h1>

                        <div class="animate-fade-up mb-12" style="animation-delay: 360ms;">
                            <p class="mb-3 text-lg text-gray-600" style="font-family: 'DM Sans', sans-serif;">
                                We've sent a verification link to
                            </p>
                            <p class="mb-6 text-xl text-blue-600"
                               style="font-family: 'DM Sans', sans-serif; font-weight: 600;">
                                {{ props.email || 'your email address' }}
                            </p>
                            <p class="text-gray-500" style="font-family: 'DM Sans', sans-serif;">
                                Click the link in the email to verify your account and continue setting up your vault
                                from there.
                            </p>
                        </div>

                        <div class="w-full max-w-md animate-fade-up space-y-4" style="animation-delay: 480ms;">

                            <button
                                type="button"
                                :disabled="resendForm.processing || !resendForm.email"
                                class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-300 px-8 py-4 text-gray-900 transition-all hover:border-blue-600 disabled:cursor-not-allowed disabled:opacity-60"
                                style="font-family: 'DM Sans', sans-serif; font-weight: 600;"
                                @click="handleResend"
                            >
                                <RefreshCw class="h-5 w-5" :class="{ 'animate-spin': resendForm.processing }"/>
                                {{ resendForm.processing ? 'Sending...' : 'Resend verification email' }}
                            </button>
                        </div>

                        <div class="animate-fade-up mt-12" style="animation-delay: 600ms;">
                            <div class="max-w-md rounded-xl border border-blue-100 bg-blue-50 p-4">
                                <p class="text-sm text-gray-700" style="font-family: 'DM Sans', sans-serif;">
                                    <span style="font-weight: 600;">Didn't receive the email?</span>
                                    Check your spam folder or try resending the verification email.
                                </p>
                            </div>
                        </div>

                        <div class="animate-fade-up mt-8" style="animation-delay: 720ms;">
                            <Link
                                :href="route('start-account')"
                                class="text-sm text-gray-600 transition-colors hover:text-blue-600"
                                style="font-family: 'DM Sans', sans-serif;"
                            >
                                Wrong email? <span style="font-weight: 600;">Go back and change it</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<style scoped>
@keyframes fade-up {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-down {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes glow {
    0% {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    }
    50% {
        box-shadow: 0 0 40px rgba(59, 130, 246, 0.5);
    }
    100% {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    }
}

@keyframes orbit {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.animate-fade-up {
    opacity: 0;
    animation: fade-up 0.8s ease forwards;
}

.animate-fade-down {
    opacity: 0;
    animation: fade-down 0.6s ease forwards;
}

.animate-glow {
    animation: glow 2s ease-in-out infinite;
}

.animate-orbit {
    animation: orbit 20s linear infinite;
}
</style>
