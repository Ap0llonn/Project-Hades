<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { LogIn } from 'lucide-vue-next';
import AuthSplitLayout from '../components/AuthSplitLayout.vue';
import AuthTextField from '../components/AuthTextField.vue';
</script>

<template>
    <Head title="Login | The Vault" />

    <AuthSplitLayout
        title="Welcome back"
        subtitle="Access your vault with password, passkeys, and advanced account protections."
    >
        <div class="mb-8 flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-on-primary">
                <LogIn class="h-5 w-5" />
            </span>
            <div>
                <h2 class="text-2xl font-bold text-on-surface">Login</h2>
                <p class="text-sm text-on-surface-variant">Secure session starts in seconds.</p>
            </div>
        </div>

        <form class="space-y-5" @submit.prevent>
            <AuthTextField
                id="email"
                label="Email"
                name="email"
                type="email"
                placeholder="you@company.com"
                autocomplete="email"
                :required="true"
            />
            <AuthTextField
                id="password"
                label="Password"
                name="password"
                type="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                :required="true"
            />

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <label class="inline-flex items-center gap-2 text-sm text-on-surface-variant">
                    <input
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary/30"
                    >
                    <span>Remember me for 20 days</span>
                </label>
                <a href="#" class="shrink-0 text-sm font-semibold text-primary hover:text-primary-container">
                    Forgot password?
                </a>
            </div>

            <button
                type="submit"
                class="w-full rounded-2xl bg-primary px-5 py-3 text-sm font-bold text-on-primary shadow-md transition-all hover:bg-primary-container active:scale-95"
            >
                Sign In
            </button>
        </form>

        <p class="mt-6 text-sm text-on-surface-variant">
            New to The Vault?
            <Link href="/signup" class="font-semibold text-primary hover:text-primary-container">
                Create an account
            </Link>
        </p>
    </AuthSplitLayout>
</template>
