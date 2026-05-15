<script setup>
import { AlertCircle, Copy, Shield } from 'lucide-vue-next';

defineProps({
    securityScore: {
        type: Number,
        required: true,
    },
    weakPasswords: {
        type: Number,
        required: true,
    },
    reusedPasswords: {
        type: Number,
        required: true,
    },
    breachedPasswords: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <section class="mb-8">
        <div class="rounded-2xl border border-outline-variant bg-gradient-to-br from-surface-container to-surface-container-high p-8">
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <h2 class="mb-2 text-2xl font-semibold tracking-tight text-on-surface">Security Health Score</h2>
                    <p class="text-on-surface-variant">Your overall password security rating</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-primary bg-surface">
                        <span class="text-2xl font-bold text-primary">{{ securityScore }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-outline-variant bg-surface p-5">
                    <div class="mb-2 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-error-container">
                            <AlertCircle class="h-5 w-5 text-on-surface" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-on-surface">{{ weakPasswords }}</p>
                            <p class="text-sm text-on-surface-variant">Weak passwords</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-outline-variant bg-surface p-5">
                    <div class="mb-2 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-container">
                            <Copy class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-on-surface">{{ reusedPasswords }}</p>
                            <p class="text-sm text-on-surface-variant">Reused passwords</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-outline-variant bg-surface p-5">
                    <div class="mb-2 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-tertiary-fixed">
                            <Shield class="h-5 w-5 text-primary" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-on-surface">{{ breachedPasswords }}</p>
                            <p class="text-sm text-on-surface-variant">Breached accounts</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

