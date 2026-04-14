<script setup>
import { Link } from '@inertiajs/vue3';
import { Fingerprint, LockKeyhole, ShieldCheck } from 'lucide-vue-next';
import AuthLayout from '../../../shared/layouts/AuthLayout.vue';

defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        required: true,
    },
});

const trustPoints = [
    'End-to-end encrypted vault architecture',
    'Passkeys, MFA, and recovery workflows',
    'Session monitoring with suspicious activity detection',
];
</script>

<template>
    <AuthLayout>
        <div class="relative overflow-hidden bg-background px-6 py-12 text-on-surface">
            <div class="absolute -left-16 -top-16 h-64 w-64 rounded-full bg-primary/10 blur-[100px]" />
            <div class="absolute -bottom-16 -right-16 h-64 w-64 rounded-full bg-secondary-container/40 blur-[100px]" />

            <div class="relative mx-auto grid w-full max-w-7xl gap-8 lg:grid-cols-2">
                <section class="rounded-[2rem] border border-outline-variant/50 bg-surface-container-low p-10 [box-shadow:0_40px_60px_-15px_rgb(25_28_30_/_0.06)]">
                    <Link href="/" class="mb-10 inline-flex items-center gap-2 text-xl font-bold tracking-tight text-primary">
                        <LockKeyhole class="h-5 w-5" />
                        <span>The Vault</span>
                    </Link>

                    <h1 class="mb-4 text-4xl font-extrabold tracking-tight">{{ title }}</h1>
                    <p class="mb-8 max-w-lg text-lg leading-relaxed text-on-surface-variant">
                        {{ subtitle }}
                    </p>

                    <div class="space-y-3">
                        <div
                            v-for="point in trustPoints"
                            :key="point"
                            class="flex items-start gap-3 rounded-xl bg-surface-container-lowest p-4"
                        >
                            <ShieldCheck class="mt-0.5 h-5 w-5 shrink-0 text-primary" />
                            <p class="text-sm font-medium">{{ point }}</p>
                        </div>
                    </div>

                    <div class="mt-10 inline-flex items-center gap-2 rounded-full bg-primary-fixed px-4 py-2 text-xs font-bold uppercase tracking-wider text-primary">
                        <Fingerprint class="h-4 w-4" />
                        <span>Zero-Knowledge Ready</span>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-outline-variant/50 bg-white/80 p-8 backdrop-blur-2xl [box-shadow:0_40px_60px_-15px_rgb(25_28_30_/_0.06)] sm:p-10">
                    <slot />
                </section>
            </div>
        </div>
    </AuthLayout>
</template>
