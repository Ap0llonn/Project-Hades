<script setup>
import { Head } from '@inertiajs/vue3';
import { CircleAlert, FileText, Scale, ShieldCheck } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import DefaultLayout from '../../../shared/layouts/DefaultLayout.vue';

const termsSections = [
    {
        title: '1. Agreement and Scope',
        body: 'These Terms govern your access to and use of VaultGuardian websites, applications, and related services. By creating an account or using the Service, you agree to these Terms.',
    },
    {
        title: '2. Eligibility and Account Registration',
        body: 'You must be legally capable of entering a binding agreement and provide accurate registration details. You are responsible for account activity and for protecting credentials, recovery methods, and device access.',
    },
    {
        title: '3. Security Responsibilities',
        body: 'VaultGuardian applies strong encryption and secure operational controls, but you remain responsible for maintaining the confidentiality of your master password and account recovery materials. We cannot recover encrypted vault data if required secrets are lost.',
    },
    {
        title: '4. Acceptable Use',
        body: 'You may not misuse, interfere with, reverse engineer, or attempt unauthorized access to the Service. You may not use VaultGuardian to store or transmit unlawful material or to violate others\' rights.',
    },
    {
        title: '5. Subscriptions, Billing, and Renewals',
        body: 'Paid plans are billed in advance on a recurring basis unless canceled. Prices, taxes, and billing periods are disclosed at purchase. You authorize recurring charges until cancellation. Fees are generally non-refundable unless required by law.',
    },
    {
        title: '6. Intellectual Property',
        body: 'VaultGuardian and related marks, software, and content are protected by intellectual property laws. These Terms grant a limited, non-exclusive, non-transferable right to use the Service in accordance with these Terms.',
    },
    {
        title: '7. Third-Party Services',
        body: 'The Service may integrate with third-party platforms or providers. We are not responsible for third-party services, terms, or privacy practices, and your use of those services is governed by their policies.',
    },
    {
        title: '8. Suspension and Termination',
        body: 'We may suspend or terminate access if you violate these Terms, create security risk, or where required by law. You may stop using the Service at any time and may request account deletion subject to legal and operational retention obligations.',
    },
    {
        title: '9. Disclaimers',
        body: 'Except as expressly stated, the Service is provided "as is" and "as available." We disclaim implied warranties including merchantability, fitness for a particular purpose, and non-infringement to the fullest extent permitted by law.',
    },
    {
        title: '10. Limitation of Liability',
        body: 'To the maximum extent permitted by law, VaultGuardian and its affiliates are not liable for indirect, incidental, special, consequential, or punitive damages, or loss of profits, data, or goodwill arising from or related to your use of the Service.',
    },
    {
        title: '11. Governing Law and Disputes',
        body: 'These Terms are governed by applicable laws of the jurisdiction where VaultGuardian is established, without regard to conflict of law principles, unless local law requires otherwise.',
    },
    {
        title: '12. Changes to Terms',
        body: 'We may update these Terms from time to time. Material updates will be communicated through the Service or by email. Continued use after the effective date constitutes acceptance of the revised Terms.',
    },
];
</script>

<template>
    <Head title="Terms of Service | VaultGuardian" />

    <DefaultLayout>
        <div class="relative min-h-full overflow-x-hidden bg-white text-gray-900">
            <div class="pointer-events-none fixed inset-0 opacity-40">
                <div
                    class="absolute inset-0"
                    style="
                        background-image:
                            linear-gradient(rgba(59, 130, 246, 0.08) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(59, 130, 246, 0.08) 1px, transparent 1px);
                        background-size: 60px 60px;
                    "
                />
            </div>

            <div class="fixed left-1/4 top-0 h-96 w-96 rounded-full bg-blue-500 opacity-15 blur-[120px]" />
            <div class="fixed bottom-0 right-1/4 h-96 w-96 rounded-full bg-blue-400 opacity-10 blur-[120px]" />

            <section class="relative z-10 mx-auto w-full max-w-5xl px-6 pb-20 pt-14">
                <div class="mb-8 rounded-3xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100/50 p-8 md:p-10">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-white px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.13em] text-blue-700">
                        <Scale class="h-3.5 w-3.5" />
                        Legal
                    </div>
                    <h1 class="mb-4 text-4xl font-bold tracking-tight text-gray-900 md:text-5xl">Terms of Service</h1>
                    <p class="max-w-3xl text-base text-gray-600 md:text-lg">
                        These Terms explain your rights and responsibilities when using VaultGuardian and describe how we operate the service.
                    </p>
                    <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-gray-600">
                        <span class="rounded-full border border-blue-200 bg-white px-3 py-1">Effective date: April 15, 2026</span>
                        <span class="rounded-full border border-blue-200 bg-white px-3 py-1">Last updated: April 15, 2026</span>
                    </div>
                </div>

                <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="mb-3 inline-flex rounded-lg bg-blue-100 p-2.5 text-blue-600">
                            <ShieldCheck class="h-5 w-5" />
                        </div>
                        <h2 class="mb-2 text-sm font-semibold uppercase tracking-wider text-gray-900">Security Focus</h2>
                        <p class="text-sm text-gray-600">You control your credentials and are responsible for protecting account access.</p>
                    </article>

                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="mb-3 inline-flex rounded-lg bg-blue-100 p-2.5 text-blue-600">
                            <FileText class="h-5 w-5" />
                        </div>
                        <h2 class="mb-2 text-sm font-semibold uppercase tracking-wider text-gray-900">Clear Rules</h2>
                        <p class="text-sm text-gray-600">Acceptable use, billing terms, and legal limitations are defined below.</p>
                    </article>

                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="mb-3 inline-flex rounded-lg bg-blue-100 p-2.5 text-blue-600">
                            <CircleAlert class="h-5 w-5" />
                        </div>
                        <h2 class="mb-2 text-sm font-semibold uppercase tracking-wider text-gray-900">Updates</h2>
                        <p class="text-sm text-gray-600">Material policy changes are communicated through the app or email.</p>
                    </article>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm md:p-8">
                    <div class="space-y-8">
                        <section v-for="section in termsSections" :key="section.title">
                            <h2 class="mb-2 text-xl font-semibold tracking-tight text-gray-900">{{ section.title }}</h2>
                            <p class="leading-relaxed text-gray-700">{{ section.body }}</p>
                        </section>

                        <section>
                            <h2 class="mb-2 text-xl font-semibold tracking-tight text-gray-900">13. Contact Us</h2>
                            <p class="leading-relaxed text-gray-700">
                                For legal requests or Terms questions, contact:
                                <a href="mailto:legal@vaultguardian.com" class="font-medium text-blue-600 hover:text-blue-700">
                                    legal@vaultguardian.com
                                </a>
                            </p>
                        </section>
                    </div>
                </div>

                <div class="mt-10 flex flex-wrap items-center gap-3">
                    <a
                        :href="route('privacy', {}, false)"
                        class="inline-flex items-center rounded-lg border border-blue-200 bg-white px-5 py-2.5 text-sm font-semibold text-blue-700 transition-all hover:border-blue-300 hover:bg-blue-50"
                    >
                        Read Privacy Policy
                    </a>
                    <a
                        :href="route('home')"
                        class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition-all hover:bg-blue-700"
                    >
                        Back to Home
                    </a>
                </div>
            </section>
        </div>
    </DefaultLayout>
</template>
