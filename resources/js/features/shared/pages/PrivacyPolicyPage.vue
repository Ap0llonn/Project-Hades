<script setup>
import { Head } from '@inertiajs/vue3';
import { Database, Lock, Route, UserCheck } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import DefaultLayout from '../../../shared/layouts/DefaultLayout.vue';

const policySections = [
    {
        title: '1. Scope',
        body: 'This Privacy Policy describes how VaultGuardian collects, uses, discloses, and protects personal information when you use our websites, apps, and related services.',
    },
    {
        title: '2. Information We Collect',
        body: 'Depending on your use of the Service, we may collect account identifiers (such as email), authentication events, device and browser metadata, billing records, support communications, and service diagnostics.',
    },
    {
        title: '3. How We Use Personal Information',
        body: 'We use data to provide and secure the Service, detect abuse, process transactions, improve performance, communicate important updates, and comply with legal obligations.',
    },
    {
        title: '4. Legal Bases for Processing',
        body: 'Where applicable, we process information under contract necessity, legitimate interests, legal compliance, and consent where required.',
    },
    {
        title: '5. Data Sharing and Disclosure',
        body: 'We do not sell personal information. We may share data with vetted service providers (hosting, payment, support, analytics), with your direction, or when required by law.',
    },
    {
        title: '6. International Transfers',
        body: 'If your information is transferred across borders, we apply appropriate safeguards consistent with applicable data protection laws.',
    },
    {
        title: '7. Data Retention',
        body: 'We retain personal information only as long as necessary for the purposes described in this Policy, including legal, security, and accounting requirements.',
    },
    {
        title: '8. Security Measures',
        body: 'We maintain administrative, technical, and organizational safeguards designed to protect personal information against unauthorized access, disclosure, alteration, and loss.',
    },
    {
        title: '9. Your Privacy Rights',
        body: 'Subject to local law, you may have rights to access, correct, delete, restrict, object to processing, or request portability of your personal information. You may also withdraw consent where processing relies on consent.',
    },
    {
        title: '10. Cookies and Similar Technologies',
        body: 'We use cookies and similar tools to maintain sessions, enhance functionality, and understand service usage. You can manage cookie preferences through browser controls where available.',
    },
    {
        title: '11. Children\'s Privacy',
        body: 'The Service is not directed to children under 13 (or the relevant minimum age in your jurisdiction), and we do not knowingly collect personal information from children without proper authorization.',
    },
    {
        title: '12. Policy Updates',
        body: 'We may revise this Privacy Policy from time to time. Material changes will be communicated through the Service, by email, or through other appropriate channels.',
    },
];
</script>

<template>
    <Head title="Privacy Policy | VaultGuardian" />

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
                        <Lock class="h-3.5 w-3.5" />
                        Privacy
                    </div>
                    <h1 class="mb-4 text-4xl font-bold tracking-tight text-gray-900 md:text-5xl">Privacy Policy</h1>
                    <p class="max-w-3xl text-base text-gray-600 md:text-lg">
                        This Policy explains what information we process, why we process it, and how you can exercise your privacy rights.
                    </p>
                    <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-gray-600">
                        <span class="rounded-full border border-blue-200 bg-white px-3 py-1">Effective date: April 15, 2026</span>
                        <span class="rounded-full border border-blue-200 bg-white px-3 py-1">Last updated: April 15, 2026</span>
                    </div>
                </div>

                <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="mb-3 inline-flex rounded-lg bg-blue-100 p-2.5 text-blue-600">
                            <Database class="h-5 w-5" />
                        </div>
                        <h2 class="mb-2 text-sm font-semibold uppercase tracking-wider text-gray-900">Data Minimization</h2>
                        <p class="text-sm text-gray-600">We collect only the information needed to operate and secure the service.</p>
                    </article>

                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="mb-3 inline-flex rounded-lg bg-blue-100 p-2.5 text-blue-600">
                            <UserCheck class="h-5 w-5" />
                        </div>
                        <h2 class="mb-2 text-sm font-semibold uppercase tracking-wider text-gray-900">User Rights</h2>
                        <p class="text-sm text-gray-600">You can request access, correction, deletion, or portability where applicable.</p>
                    </article>

                    <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="mb-3 inline-flex rounded-lg bg-blue-100 p-2.5 text-blue-600">
                            <Route class="h-5 w-5" />
                        </div>
                        <h2 class="mb-2 text-sm font-semibold uppercase tracking-wider text-gray-900">Transparency</h2>
                        <p class="text-sm text-gray-600">Material policy changes are announced before they take effect.</p>
                    </article>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm md:p-8">
                    <div class="space-y-8">
                        <section v-for="section in policySections" :key="section.title">
                            <h2 class="mb-2 text-xl font-semibold tracking-tight text-gray-900">{{ section.title }}</h2>
                            <p class="leading-relaxed text-gray-700">{{ section.body }}</p>
                        </section>

                        <section>
                            <h2 class="mb-2 text-xl font-semibold tracking-tight text-gray-900">13. Contact Us</h2>
                            <p class="leading-relaxed text-gray-700">
                                For privacy requests or data protection questions, contact:
                                <a href="mailto:privacy@vaultguardian.com" class="font-medium text-blue-600 hover:text-blue-700">
                                    privacy@vaultguardian.com
                                </a>
                            </p>
                        </section>
                    </div>
                </div>

                <div class="mt-10 flex flex-wrap items-center gap-3">
                    <a
                        :href="route('terms', {}, false)"
                        class="inline-flex items-center rounded-lg border border-blue-200 bg-white px-5 py-2.5 text-sm font-semibold text-blue-700 transition-all hover:border-blue-300 hover:bg-blue-50"
                    >
                        Read Terms of Service
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
