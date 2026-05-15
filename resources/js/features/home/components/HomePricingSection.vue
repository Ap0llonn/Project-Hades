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
    <section id="pricing" class="mx-auto w-full max-w-7xl px-6 py-24">
        <div>
            <div class="mb-14 text-center" data-aos="fade-up">
                <h2 class="mb-4 text-4xl font-semibold tracking-tight text-gray-900 md:text-5xl">
                    Simple plans, serious security
                </h2>
                <p class="mx-auto max-w-2xl text-lg text-gray-600">
                    Start free, scale when needed, and keep the same security foundation at every tier.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <article
                    v-for="plan in plans"
                    :key="plan.name"
                    class="relative overflow-hidden rounded-2xl border p-8 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg"
                    :class="plan.featured
                        ? 'border-blue-300 bg-gradient-to-br from-blue-600 to-blue-500 text-white shadow-xl shadow-blue-600/20'
                        : 'border-gray-200 bg-white text-gray-900'"
                    data-aos="fade-up"
                >
                    <div
                        v-if="plan.featured"
                        class="absolute right-4 top-4 rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-white"
                    >
                        Most Popular
                    </div>

                    <div class="mb-8">
                        <h3 class="mb-3 text-2xl font-bold tracking-tight">{{ plan.name }}</h3>
                        <p
                            class="mb-6 text-sm leading-relaxed"
                            :class="plan.featured ? 'text-white/90' : 'text-gray-600'"
                        >
                            {{ plan.summary }}
                        </p>

                        <p class="flex items-end gap-2">
                            <span class="text-4xl font-black tracking-tight">{{ plan.price }}</span>
                            <span
                                class="pb-1 text-sm font-semibold"
                                :class="plan.featured ? 'text-white/80' : 'text-gray-600'"
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
                                :class="plan.featured ? 'bg-white/20' : 'bg-blue-100'"
                            >
                                <Check
                                    class="h-3.5 w-3.5"
                                    :class="plan.featured ? 'text-white' : 'text-blue-600'"
                                />
                            </span>
                            <span class="text-sm font-medium">{{ feature }}</span>
                        </li>
                    </ul>

                    <button
                        class="w-full rounded-2xl px-5 py-3 text-sm font-bold transition-all active:scale-95"
                        :class="plan.featured
                            ? 'bg-white text-blue-700 hover:bg-blue-50'
                            : 'bg-blue-600 text-white hover:bg-blue-700'"
                    >
                        {{ plan.cta }}
                    </button>
                </article>
            </div>
        </div>
    </section>
</template>
