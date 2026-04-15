<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff, Lock, Mail, Shield } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import AuthLayout from '../../../shared/layouts/AuthLayout.vue';

const showPassword = ref(false);

const loginRequest = useForm({
    email: '',
    password: '',
    _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
});

const errorMessage = computed(() => loginRequest.errors.email || loginRequest.errors.password || '');

function handleSubmit() {
    loginRequest.post(route('login.perform'));
}
</script>

<template>
    <Head title="Login | VaultGuardian" />

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

            <div class="mx-auto max-w-2xl px-6 py-10 md:py-12">
                <div>
                    <h1
                        class="mb-3 text-center text-3xl tracking-tight text-gray-900 sm:text-4xl md:text-5xl"
                        style="font-family: 'DM Sans', sans-serif; font-weight: 700;"
                        data-aos="fade-up"
                        data-aos-delay="70"
                    >
                        Welcome back
                    </h1>
                    <p
                        class="mb-10 text-center text-sm text-gray-600 sm:text-base md:mb-12"
                        style="font-family: 'DM Sans', sans-serif;"
                        data-aos="fade-up"
                        data-aos-delay="140"
                    >
                        Sign in to access your secure vault.
                    </p>
                </div>

                <form class="mx-auto max-w-xl space-y-5 md:space-y-6" data-aos="fade-up" data-aos-delay="220" @submit.prevent="handleSubmit">
                    <div data-aos="fade-up" data-aos-delay="280">
                        <label class="mb-2 block text-sm text-gray-700" for="email" style="font-family: 'DM Sans', sans-serif; font-weight: 600;">
                            Email
                        </label>
                        <div class="relative">
                            <Mail class="absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                id="email"
                                v-model="loginRequest.email"
                                type="email"
                                autocomplete="email"
                                placeholder="you@example.com"
                                class="w-full rounded-xl border-2 border-gray-300 py-4 pl-14 pr-4 text-base transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/20 md:text-lg"
                                style="font-family: 'DM Sans', sans-serif;"
                            />
                        </div>
                    </div>

                    <div data-aos="fade-up" data-aos-delay="340">
                        <label class="mb-2 block text-sm text-gray-700" for="password" style="font-family: 'DM Sans', sans-serif; font-weight: 600;">
                            Master Password
                        </label>
                        <div class="relative">
                            <Lock class="absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                id="password"
                                v-model="loginRequest.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                placeholder="Enter your master password"
                                class="w-full rounded-xl border-2 border-gray-300 py-4 pl-14 pr-14 text-base transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/20 md:text-lg"
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
                    </div>

                    <p
                        v-if="errorMessage"
                        class="rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700"
                        data-aos="fade-up"
                        data-aos-delay="380"
                    >
                        {{ errorMessage }}
                    </p>

                    <div class="flex flex-wrap items-center justify-between gap-3" data-aos="fade-up" data-aos-delay="420">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            <span class="text-sm text-gray-600" style="font-family: 'DM Sans', sans-serif;">Remember me</span>
                        </label>
                        <a
                            href="#"
                            class="text-sm text-blue-600 transition-colors hover:text-blue-700"
                            style="font-family: 'DM Sans', sans-serif; font-weight: 600;"
                        >
                            Forgot password?
                        </a>
                    </div>

                    <button
                        type="submit"
                        :disabled="loginRequest.processing"
                        class="w-full rounded-xl bg-blue-600 py-4 text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/30 disabled:cursor-not-allowed disabled:opacity-70"
                        style="font-family: 'DM Sans', sans-serif; font-weight: 600;"
                        data-aos="fade-up"
                        data-aos-delay="460"
                    >
                        {{ loginRequest.processing ? 'Signing In...' : 'Sign In' }}
                    </button>

                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4" data-aos="fade-up" data-aos-delay="520">
                        <div class="flex gap-3">
                            <Shield class="mt-0.5 h-5 w-5 shrink-0 text-blue-600" />
                            <p class="text-sm text-gray-700" style="font-family: 'DM Sans', sans-serif;">
                                Your vault remains protected with zero-knowledge encryption on every sign in.
                            </p>
                        </div>
                    </div>

                    <p class="text-center text-gray-600" style="font-family: 'DM Sans', sans-serif;" data-aos="fade-up" data-aos-delay="580">
                        Don't have an account?
                        <a :href="route('start-account')" class="font-semibold text-blue-600 transition-colors hover:text-blue-700">
                            Sign up
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>
