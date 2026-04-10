<script setup>
import { Shield } from 'lucide-vue-next';
</script>

<template>
    <section id="get-started" class="px-6 py-24">
        <div class="mx-auto w-full max-w-4xl rounded-[3rem] border border-white bg-white/80 p-16 text-center backdrop-blur-2xl [box-shadow:0_40px_60px_-15px_rgb(25_28_30_/_0.06)]">
            <div class="mx-auto mb-8 flex h-20 w-20 items-center justify-center rounded-3xl bg-primary/10">
                <Shield class="h-8 w-8 text-primary" />
            </div>
            <h2 class="mb-6 text-4xl font-bold text-on-surface">Ready to fortify your digital life?</h2>
            <p class="mx-auto mb-10 max-w-xl text-lg text-on-surface-variant">
                Join over 2 million individuals and teams who trust The Vault with their most
                sensitive information.
            </p>
            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                <button class="rounded-2xl bg-primary px-10 py-5 text-xl font-bold text-on-primary shadow-xl transition-all hover:bg-primary-container active:scale-95">
                    Create Your Vault
                </button>
                <button class="rounded-2xl bg-surface-container-high px-10 py-5 text-xl font-bold text-on-surface transition-all hover:bg-surface-container-highest active:scale-95">
                    Contact Sales
                </button>
            </div>
        </div>
    </section>
</template>
