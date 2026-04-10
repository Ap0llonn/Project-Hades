<script setup>
import { Activity, ArrowRight, Clock3, KeyRound, Share2 } from 'lucide-vue-next';

const authTags = ['Passkeys / WebAuthn', 'TOTP + SMS + Email', 'Recovery Codes'];
</script>

<template>
    <section id="features" class="bg-surface-container-low py-24">
        <div class="mx-auto w-full max-w-7xl px-6">
            <div class="mb-20 text-center">
                <h2 class="mb-4 text-4xl font-bold text-on-surface">Fortified with Intelligence</h2>
                <p class="mx-auto max-w-2xl text-lg text-on-surface-variant">
                    Built to feel effortless for users and uncompromising for attackers.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <article class="md:col-span-2 flex min-h-[320px] flex-col justify-between rounded-[2rem] bg-surface-container-lowest p-10 [box-shadow:0_40px_60px_-15px_rgb(25_28_30_/_0.06)]">
                    <div>
                        <span class="mb-6 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-fixed text-primary">
                            <KeyRound class="h-6 w-6" />
                        </span>
                        <h3 class="mb-3 text-2xl font-bold text-on-surface">Sign In Once, Stay Unbreakable</h3>
                        <p class="max-w-md leading-relaxed text-on-surface-variant">
                            Passwords, passkeys, and layered MFA work together to deliver a login
                            experience that feels smooth and defends like enterprise security.
                        </p>
                    </div>
                    <div class="mt-8 flex flex-wrap gap-2">
                        <span
                            v-for="tag in authTags"
                            :key="tag"
                            class="rounded-full bg-surface-container px-3 py-1 text-xs font-bold text-on-secondary-container"
                        >
                            {{ tag }}
                        </span>
                    </div>
                </article>

                <article class="flex flex-col justify-between rounded-[2rem] bg-gradient-to-br from-primary to-primary-container p-10 text-on-primary shadow-xl">
                    <div>
                        <span class="mb-6 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20">
                            <Share2 class="h-6 w-6" />
                        </span>
                        <h3 class="mb-3 text-2xl font-bold">Share with Confidence</h3>
                        <p class="leading-relaxed text-white/90">
                            Send secrets to teammates or family with end-to-end encryption and precise
                            permissions for view, edit, or admin access.
                        </p>
                    </div>
                    <a class="mt-8 inline-flex items-center gap-2 text-sm font-bold transition-transform hover:translate-x-1" href="#">
                        Learn More
                        <ArrowRight class="h-4 w-4" />
                    </a>
                </article>

                <article class="flex flex-col items-center rounded-[2rem] bg-surface-container-lowest p-10 text-center [box-shadow:0_40px_60px_-15px_rgb(25_28_30_/_0.06)]">
                    <div class="mb-8 flex h-20 w-20 items-center justify-center rounded-full bg-tertiary-fixed text-tertiary">
                        <Clock3 class="h-8 w-8" />
                    </div>
                    <h3 class="mb-3 text-2xl font-bold text-on-surface">Secrets That Wait for the Right Moment</h3>
                    <p class="leading-relaxed text-on-surface-variant">
                        Lock sensitive data until a future date and reveal it only when it matters.
                        Perfect for digital legacy, legal docs, and delayed access workflows.
                    </p>
                </article>

                <article
                    id="security-audit"
                    class="relative overflow-hidden rounded-[2rem] bg-inverse-surface p-10 text-inverse-on-surface md:col-span-2"
                >
                    <div class="relative z-10">
                        <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10">
                            <Activity class="h-6 w-6 text-primary-fixed" />
                        </div>
                        <h3 class="mb-3 text-2xl font-bold">Always-On Session Protection</h3>
                        <p class="mb-8 max-w-md text-inverse-on-surface/75">
                            Smart token rotation, reuse detection, and full activity history give your
                            team instant visibility and faster incident response.
                        </p>

                        <div class="flex flex-wrap gap-4">
                            <div class="rounded-2xl bg-white/10 px-6 py-4 backdrop-blur">
                                <div class="mb-1 text-3xl font-black text-primary-fixed">15m</div>
                                <div class="text-[10px] font-bold uppercase tracking-widest opacity-60">
                                    Access Token TTL
                                </div>
                            </div>
                            <div class="rounded-2xl bg-white/10 px-6 py-4 backdrop-blur">
                                <div class="mb-1 text-3xl font-black text-error-container">30d</div>
                                <div class="text-[10px] font-bold uppercase tracking-widest opacity-60">
                                    Refresh Rotation
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-primary/30 to-transparent" />
                </article>
            </div>
        </div>
    </section>
</template>
