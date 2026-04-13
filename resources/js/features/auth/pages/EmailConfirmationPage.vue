<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Check, Mail } from 'lucide-vue-next';
import AuthSplitLayout from '../components/AuthSplitLayout.vue';
</script>

<template>
    <Head title="Confirm Email | The Vault" />

    <AuthSplitLayout
        title="Confirm your email"
        subtitle="We sent a confirmation link to your inbox. Open the email and confirm your account to continue."
    >
        <div class="mb-8 flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-on-primary">
                <Mail class="h-5 w-5" />
            </span>
            <div>
                <h2 class="text-2xl font-bold text-on-surface">Check your inbox</h2>
                <p class="text-sm text-on-surface-variant">Confirmation email sent successfully.</p>
            </div>
        </div>

        <div class="space-y-5">
            <div class="overflow-hidden rounded-2xl border border-outline-variant/50 bg-surface-container-lowest p-4">
                <img
                    :src="'/images/email-confirmation.svg'"
                    alt="Email confirmation"
                    class="mx-auto h-auto w-full max-w-md"
                >
            </div>

            <div class="space-y-3 rounded-2xl border border-outline-variant/50 bg-surface-container-lowest p-4">
                <p class="flex items-center gap-2 text-sm text-on-surface">
                    <Check class="h-4 w-4 text-primary" />
                    Open your inbox.
                </p>
                <p class="flex items-center gap-2 text-sm text-on-surface">
                    <Check class="h-4 w-4 text-primary" />
                    Click the confirmation link.
                </p>
                <p class="flex items-center gap-2 text-sm text-on-surface">
                    <Check class="h-4 w-4 text-primary" />
                    Sign in and access your vault.
                </p>
            </div>

            <Link
                href="/login"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-primary px-5 py-3 text-sm font-bold text-on-primary shadow-md transition-all hover:bg-primary-container active:scale-95"
            >
                Continue to Login
            </Link>
        </div>
    </AuthSplitLayout>
</template>
