<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { UserPlus } from 'lucide-vue-next';
import AuthSplitLayout from '../components/AuthSplitLayout.vue';
import AuthTextField from '../components/AuthTextField.vue';
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

        <form class="grid gap-5 sm:grid-cols-2" @submit.prevent>
            <AuthTextField
                id="first_name"
                label="First name"
                name="first_name"
                placeholder="Sam"
                autocomplete="given-name"
                :required="true"
            />
            <AuthTextField
                id="last_name"
                label="Last name"
                name="last_name"
                placeholder="Tremblay"
                autocomplete="family-name"
                :required="true"
            />
            <div class="sm:col-span-2">
                <AuthTextField
                    id="email"
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="you@company.com"
                    autocomplete="email"
                    :required="true"
                />
            </div>
            <div class="sm:col-span-2">
                <AuthTextField
                    id="phone"
                    label="Phone (optional)"
                    name="phone"
                    type="tel"
                    placeholder="+1 (555) 123-4567"
                    autocomplete="tel"
                />
            </div>
            <div class="sm:col-span-2">
                <AuthTextField
                    id="password"
                    label="Password"
                    name="password"
                    type="password"
                    placeholder="Choose a strong password"
                    autocomplete="new-password"
                    :required="true"
                    helper="Use at least 12 characters with uppercase, lowercase, number, and symbol."
                />
            </div>
            <div class="sm:col-span-2">
                <AuthTextField
                    id="password_confirmation"
                    label="Confirm password"
                    name="password_confirmation"
                    type="password"
                    placeholder="Repeat your password"
                    autocomplete="new-password"
                    :required="true"
                />
            </div>

            <label class="sm:col-span-2 inline-flex items-start gap-2 rounded-xl bg-surface-container-lowest p-3 text-sm text-on-surface-variant">
                <input
                    type="checkbox"
                    name="terms"
                    required
                    class="mt-0.5 h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary/30"
                >
                <span>
                    I agree to the Terms of Service and Privacy Policy.
                </span>
            </label>

            <button
                type="submit"
                class="sm:col-span-2 mt-1 w-full rounded-2xl bg-primary px-5 py-3 text-sm font-bold text-on-primary shadow-md transition-all hover:bg-primary-container active:scale-95"
            >
                Create Account
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
