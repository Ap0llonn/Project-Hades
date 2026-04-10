<script setup>
import { Check, Sparkles } from 'lucide-vue-next';

const plans = [
    {
        name: 'Starter',
        price: '$0',
        cadence: '/month',
        summary: 'For personal password management and single-device usage.',
        cta: 'Start Free',
        featured: false,
        features: [
            'Unlimited vault items',
            'One secure vault',
            'Browser autofill',
            'Basic breach alerts',
        ],
    },
    {
        name: 'Pro',
        price: '$9',
        cadence: '/month',
        summary: 'For professionals who need secure sharing and advanced controls.',
        cta: 'Upgrade to Pro',
        featured: true,
        features: [
            'Everything in Starter',
            'Secure sharing with permissions',
            'Priority security monitoring',
            '2FA and passkey enforcement',
        ],
    },
    {
        name: 'Enterprise',
        price: 'Custom',
        cadence: '',
        summary: 'For teams with compliance, SSO, and audit requirements.',
        cta: 'Contact Sales',
        featured: false,
        features: [
            'Everything in Pro',
            'SSO / SCIM provisioning',
            'Advanced audit logs',
            'Dedicated success manager',
        ],
    },
];
</script>

<template>
    <section id="pricing" class="bg-surface-container-low px-6 py-24">
        <div class="mx-auto w-full max-w-7xl">
            <div class="mb-14 text-center">
                <p class="mb-4 inline-flex items-center gap-2 rounded-full bg-secondary-container px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-on-secondary-container">
                    <Sparkles class="h-4 w-4" />
                    <span>Pricing</span>
                </p>
                <h2 class="mb-4 text-4xl font-bold text-on-surface">
                    Simple plans, serious security
                </h2>
                <p class="mx-auto max-w-2xl text-lg text-on-surface-variant">
                    Start free, scale when needed, and keep the same security foundation at every tier.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <article
                    v-for="plan in plans"
                    :key="plan.name"
                    class="rounded-[2rem] border p-8 transition-all"
                    :class="plan.featured
                        ? 'border-primary bg-gradient-to-br from-primary to-primary-container text-on-primary shadow-xl'
                        : 'border-outline-variant/60 bg-surface-container-lowest text-on-surface [box-shadow:0_40px_60px_-15px_rgb(25_28_30_/_0.06)]'"
                >
                    <div class="mb-8">
                        <h3 class="mb-3 text-2xl font-bold">{{ plan.name }}</h3>
                        <p
                            class="mb-6 text-sm leading-relaxed"
                            :class="plan.featured ? 'text-white/90' : 'text-on-surface-variant'"
                        >
                            {{ plan.summary }}
                        </p>

                        <p class="flex items-end gap-2">
                            <span class="text-4xl font-black tracking-tight">{{ plan.price }}</span>
                            <span
                                class="pb-1 text-sm font-semibold"
                                :class="plan.featured ? 'text-white/80' : 'text-on-surface-variant'"
                            >
                                {{ plan.cadence }}
                            </span>
                        </p>
                    </div>

                    <ul class="mb-8 space-y-3">
                        <li
                            v-for="feature in plan.features"
                            :key="feature"
                            class="flex items-start gap-3"
                        >
                            <span
                                class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full"
                                :class="plan.featured ? 'bg-white/20' : 'bg-primary/10'"
                            >
                                <Check
                                    class="h-3.5 w-3.5"
                                    :class="plan.featured ? 'text-white' : 'text-primary'"
                                />
                            </span>
                            <span class="text-sm font-medium">{{ feature }}</span>
                        </li>
                    </ul>

                    <button
                        class="w-full rounded-2xl px-5 py-3 text-sm font-bold transition-all active:scale-95"
                        :class="plan.featured
                            ? 'bg-white text-primary hover:bg-surface-container'
                            : 'bg-primary text-on-primary hover:bg-primary-container'"
                    >
                        {{ plan.cta }}
                    </button>
                </article>
            </div>
        </div>
    </section>
</template>
