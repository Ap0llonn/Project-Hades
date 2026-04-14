<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowRight, Eye, Fingerprint, Mail, Shield } from 'lucide-vue-next';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import AuthLayout from '../../../shared/layouts/AuthLayout.vue';

const page = usePage();

const startAccountRequest = useForm({
    email: '',
});

const emailError = computed(() => startAccountRequest.errors.email || page.props.errors?.email || '');

function handleSubmit() {
    startAccountRequest.post(route('start-account.perform'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Start Account | VaultGuardian" />

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

            <section class="relative z-10 mx-auto w-full max-w-4xl px-6 pb-20 pt-14 text-center">
                <h1 class="mb-6 text-5xl tracking-tight text-gray-900 md:text-7xl">
                    Start your
                    <span class="bg-gradient-to-r from-blue-600 to-blue-400 bg-clip-text font-bold text-transparent">
                        secure journey
                    </span>
                </h1>

                <p class="mx-auto mb-10 max-w-2xl text-lg text-gray-600">
                    Enter your email to receive a secure verification link. After verification, you will finish setup by
                    creating your master password.
                </p>

                <form class="mx-auto w-full max-w-3xl text-left" @submit.prevent="handleSubmit">
                    <div class="mb-4 flex flex-col gap-3 sm:relative sm:block">
                        <Mail class="left-6 top-1/2 z-10 hidden h-6 w-6 -translate-y-1/2 text-gray-400 sm:absolute sm:block" />
                        <input
                            id="email"
                            v-model="startAccountRequest.email"
                            type="email"
                            autocomplete="email"
                            placeholder="Enter your email address"
                            class="w-full rounded-2xl border-2 border-gray-300 px-5 py-4 text-base shadow-lg transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/20 sm:py-6 sm:pl-16 sm:pr-52 sm:text-lg"
                            @input="startAccountRequest.clearErrors('email')"
                        />
                        <button
                            type="submit"
                            :disabled="startAccountRequest.processing"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-8 py-4 font-semibold text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/30 disabled:cursor-not-allowed disabled:opacity-70 sm:absolute sm:right-2 sm:top-1/2 sm:w-auto sm:-translate-y-1/2"
                        >
                            Send Link
                            <ArrowRight class="h-5 w-5" />
                        </button>
                    </div>

                    <p v-if="emailError" class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700">
                        {{ emailError }}
                    </p>
                </form>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-4 text-sm text-gray-500 md:gap-8">
                    <div class="flex items-center gap-2">
                        <Shield class="h-4 w-4 text-blue-600" />
                        <span>256-bit AES encryption</span>
                    </div>
                    <div class="hidden h-4 w-px bg-gray-300 md:block" />
                    <div class="flex items-center gap-2">
                        <Fingerprint class="h-4 w-4 text-blue-600" />
                        <span>Biometric authentication</span>
                    </div>
                    <div class="hidden h-4 w-px bg-gray-300 md:block" />
                    <div class="flex items-center gap-2">
                        <Eye class="h-4 w-4 text-blue-600" />
                        <span>Zero-knowledge architecture</span>
                    </div>
                </div>
            </section>
        </div>
    </AuthLayout>
</template>
