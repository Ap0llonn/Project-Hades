<script setup>
import {
    CreditCard,
    EyeOff,
    KeyRound,
    LockKeyhole,
    MoreHorizontal,
    ShieldCheck,
} from 'lucide-vue-next';

const vaultRows = [
    { tag: 'Key', icon: KeyRound, mode: 'Hidden', modeIcon: EyeOff },
    { tag: 'Card', icon: CreditCard, mode: 'More', modeIcon: MoreHorizontal },
];
</script>

<template>
    <section class="relative overflow-hidden px-6 pb-28 pt-20">
        <div class="mx-auto grid w-full max-w-7xl items-center gap-16 lg:grid-cols-2">
            <div class="z-10">
                <span class="mb-6 inline-flex items-center rounded-full bg-secondary-container px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-on-secondary-container">
                    Enterprise Grade Security
                </span>

                <h1 class="mb-8 text-5xl font-extrabold leading-[1.05] tracking-tight text-on-surface md:text-7xl">
                    The Digital Vault for
                    <span class="bg-gradient-to-br from-primary to-primary-container bg-clip-text text-transparent">
                        Total Protection
                    </span>
                </h1>

                <p class="mb-10 max-w-xl text-lg leading-relaxed text-on-surface-variant md:text-xl">
                    A premium environment where your sensitive data is protected by sophisticated,
                    invisible forces.
                </p>

                <div class="flex flex-wrap gap-4">
                    <button class="rounded-2xl bg-gradient-to-br from-primary to-primary-container px-8 py-4 text-lg font-bold text-on-primary shadow-lg transition-transform active:scale-95">
                        Get Started Free
                    </button>
                    <button class="rounded-2xl bg-surface-container-high px-8 py-4 text-lg font-bold text-primary transition-transform active:scale-95">
                        Enterprise Demo
                    </button>
                </div>
            </div>

            <div class="relative">
                <div class="relative z-10 rounded-[2rem] border border-white/25 bg-white/80 p-8 backdrop-blur-2xl [box-shadow:0_40px_60px_-15px_rgb(25_28_30_/_0.06)]">
                    <div class="mb-8 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-on-primary">
                                <LockKeyhole class="h-6 w-6" />
                            </div>
                            <div>
                                <h3 class="font-bold text-on-surface">Main Vault</h3>
                                <p class="text-xs text-on-surface-variant">256-bit AES Encryption</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-2 text-sm font-bold text-primary">
                            <ShieldCheck class="h-5 w-5" />
                            <span>Shielded</span>
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="row in vaultRows"
                            :key="row.tag"
                            class="flex items-center justify-between rounded-xl bg-surface-container-low p-4"
                        >
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-surface-container-lowest text-on-surface-variant">
                                    <component :is="row.icon" class="h-4 w-4" />
                                </div>
                                <div>
                                    <div class="mb-1 h-2 w-24 rounded-full bg-on-surface-variant/20" />
                                    <div class="h-2 w-32 rounded-full bg-on-surface-variant/10" />
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-on-surface-variant">
                                <component :is="row.modeIcon" class="h-4 w-4" />
                                <span>{{ row.mode }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                Security Strength
                            </span>
                            <span class="text-xs font-bold text-primary">Ultra Secure</span>
                        </div>
                        <div class="flex gap-2">
                            <div v-for="index in 8" :key="index" class="h-1.5 flex-1 rounded-full bg-primary" />
                        </div>
                    </div>
                </div>

                <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-primary/10 blur-[100px]" />
                <div class="absolute -bottom-10 -left-10 h-64 w-64 rounded-full bg-secondary-container/40 blur-[100px]" />
            </div>
        </div>
    </section>
</template>
