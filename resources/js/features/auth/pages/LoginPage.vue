<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff, Lock, Mail, Shield } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AuthLayout from '../../../shared/layouts/AuthLayout.vue';

const showPassword = ref(false);

const loginRequest = useForm({
    email: '',
    password: '',
    _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
});

const errorMessage = computed(() => loginRequest.errors.email || loginRequest.errors.password || '');

function handleSubmit() {
    loginRequest.post('/login');
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

            <div class="relative z-10 mx-auto w-full max-w-md px-6 py-14">
                <div class="rounded-3xl border border-gray-200 bg-white p-10 shadow-xl">
                    <div class="mb-8 flex justify-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50">
                            <Shield class="h-8 w-8 text-blue-600" :stroke-width="1.5" />
                        </div>
                    </div>

                    <h1 class="mb-3 text-center text-3xl font-bold tracking-tight text-gray-900">Welcome back</h1>
                    <p class="mb-8 text-center text-gray-600">Sign in to access your secure vault</p>

                    <form class="space-y-5" @submit.prevent="handleSubmit">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700" for="email">Email</label>
                            <div class="relative">
                                <Mail class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                <input
                                    id="email"
                                    v-model="loginRequest.email"
                                    type="email"
                                    autocomplete="email"
                                    placeholder="you@example.com"
                                    class="w-full rounded-xl border border-gray-300 py-3.5 pl-12 pr-4 transition-all focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700" for="password">Master Password</label>
                            <div class="relative">
                                <Lock class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                <input
                                    id="password"
                                    v-model="loginRequest.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    autocomplete="current-password"
                                    placeholder="Enter your master password"
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
                        </div>

                        <p
                            v-if="errorMessage"
                            class="rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700"
                        >
                            {{ errorMessage }}
                        </p>

                        <div class="flex items-center justify-between">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <span class="text-sm text-gray-600">Remember me</span>
                            </label>
                            <a href="#" class="text-sm font-semibold text-blue-600 transition-colors hover:text-blue-700">
                                Forgot password?
                            </a>
                        </div>

                        <button
                            type="submit"
                            :disabled="loginRequest.processing"
                            class="w-full rounded-xl bg-blue-600 py-4 font-semibold text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/30 disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            {{ loginRequest.processing ? 'Signing In...' : 'Sign In' }}
                        </button>
                    </form>

                    <div class="my-8 flex items-center gap-4">
                        <div class="h-px flex-1 bg-gray-200" />
                        <span class="text-sm text-gray-500">or</span>
                        <div class="h-px flex-1 bg-gray-200" />
                    </div>

                    <p class="text-center text-gray-600">
                        Don't have an account?
                        <Link href="/start-account" class="font-semibold text-blue-600 transition-colors hover:text-blue-700">
                            Sign up
                        </Link>
                    </p>
                </div>

                <div class="mt-8 text-center">
                    <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                        <Shield class="h-4 w-4 text-blue-600" />
                        <span>256-bit AES encryption | Zero-knowledge security</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
