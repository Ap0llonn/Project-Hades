<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2, ShieldCheck } from 'lucide-vue-next';
import AuthSplitLayout from '../components/AuthSplitLayout.vue';
</script>

<template>
    <Head title="Email Verified | The Vault" />

    <AuthSplitLayout
        title="Email confirmed"
        subtitle="Your account is now verified. You can securely sign in and access your vault."
    >
        <div class="mb-8 flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-on-primary">
                <CheckCircle2 class="h-5 w-5" />
            </span>
            <div>
                <h2 class="text-2xl font-bold text-on-surface">Verification complete</h2>
                <p class="text-sm text-on-surface-variant">Your email has been successfully confirmed.</p>
            </div>
        </div>

        <div class="space-y-5">
            <div class="overflow-hidden rounded-2xl border border-outline-variant/50 bg-surface-container-lowest p-4">
                <img
                    :src="'/images/email-confirmation.svg'"
                    alt="Email verified"
                    class="mx-auto h-auto w-full max-w-md"
                >
            </div>

            <div class="rounded-2xl border border-outline-variant/50 bg-surface-container-lowest p-4 text-sm text-on-surface">
                <p class="flex items-center gap-2">
                    <ShieldCheck class="h-4 w-4 text-primary" />
                    Your identity is confirmed and your vault is ready.
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
