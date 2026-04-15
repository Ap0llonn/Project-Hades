<script setup>
import { onBeforeUnmount, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Rellax from 'rellax';
import { Eye, Fingerprint, Key, Lock, Shield, Zap } from 'lucide-vue-next';
import DefaultLayout from '../../../shared/layouts/DefaultLayout.vue';

const bestFeatures = [
    {
        icon: Key,
        title: 'Password Generator',
        description:
            'Create unbreakable passwords instantly. Customize length, complexity, and character types to meet any security requirement.',
        stats: '20+ characters | Special symbols | No repeats',
    },
    {
        icon: Fingerprint,
        title: 'Biometric Security',
        description:
            'Unlock your vault with Face ID, Touch ID, or fingerprint. Fast access without compromising security.',
        stats: 'Face ID | Touch ID | Windows Hello',
    },
    {
        icon: Zap,
        title: 'Instant Autofill',
        description:
            'Sign in to any website or app with one tap. Works seamlessly across browsers, mobile devices, and desktop apps.',
        stats: 'All browsers | iOS | Android | Desktop',
    },
    {
        icon: Shield,
        title: 'Breach Monitoring',
        description:
            'Get instant alerts if your credentials appear in a data breach. Take action before your accounts are compromised.',
        stats: 'Real-time alerts | Dark web monitoring',
    },
];

const features = [
    {
        icon: Key,
        title: 'Unlimited passwords',
        description: 'Store and sync unlimited passwords, passkeys, and secure notes across all your devices.',
    },
    {
        icon: Zap,
        title: 'Instant autofill',
        description: 'Sign in to websites and apps with a single click. Works everywhere you need it.',
    },
    {
        icon: Shield,
        title: 'Advanced security',
        description: 'Multi-factor authentication, breach monitoring, and encrypted file storage included.',
    },
    {
        icon: Fingerprint,
        title: 'Biometric unlock',
        description: 'Use Face ID, Touch ID, or Windows Hello for seamless yet secure access.',
    },
    {
        icon: Lock,
        title: 'Password generator',
        description: 'Create strong, unique passwords for every account. Customizable length and complexity.',
    },
    {
        icon: Eye,
        title: 'Security dashboard',
        description: 'Get insights into your password health, weak passwords, and reused credentials.',
    },
];

let parallaxScreens = null;

onMounted(() => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    parallaxScreens = new Rellax('.home-parallax-screen', {
        center: true,
        round: true,
        vertical: true,
        horizontal: false,
    });
});

onBeforeUnmount(() => {
    if (parallaxScreens) {
        parallaxScreens.destroy();
        parallaxScreens = null;
    }
});

</script>

<template>
    <Head title="VaultGuardian | Password Manager" />

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

            <div class="relative z-10">
                <section class="mx-auto w-full max-w-7xl px-6 py-20 text-center" data-aos="fade-up">
                <h1 class="mb-6 text-5xl tracking-tight text-gray-900 sm:text-6xl md:text-8xl" data-aos="fade-up" data-aos-delay="80">
                    Your digital life,
                    <br />
                    <span class="bg-gradient-to-r from-blue-600 to-blue-400 bg-clip-text font-bold text-transparent">
                        secured with elegance
                    </span>
                </h1>

                <p class="mx-auto mb-12 max-w-2xl text-lg text-gray-600" data-aos="fade-up" data-aos-delay="140">
                    A password manager that combines military-grade encryption with refined design. Store, generate, and
                    autofill passwords across all your devices with confidence.
                </p>

                <div class="flex flex-col items-center justify-center gap-4 sm:flex-row" data-aos="fade-up" data-aos-delay="200">
                    <Link
                        href="/start-account"
                        class="rounded-xl bg-blue-600 px-8 py-4 font-semibold text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/30"
                    >
                        Start Free Trial
                    </Link>
                    <a
                        href="#security"
                        class="rounded-xl border border-gray-300 px-8 py-4 font-semibold text-gray-900 transition-all hover:border-blue-600"
                    >
                        See How It Works
                    </a>
                </div>


                </section>

                <section class="mx-auto w-full max-w-7xl px-6 pb-28 pt-8">
                    <div class="mx-auto flex max-w-6xl flex-col items-center gap-6 md:flex-row md:items-end md:justify-center md:gap-8">
                        <article
                            class="home-parallax-screen relative h-72 w-full max-w-[380px] overflow-hidden rounded-3xl border border-blue-300 bg-gradient-to-b from-blue-500 to-blue-700 p-6 shadow-2xl shadow-blue-900/30 will-change-transform md:h-[430px] md:max-w-none md:flex-1"
                            data-rellax-speed="4"
                        >
                            <div class="mb-6 h-3 w-36 rounded-full bg-blue-200/70" />
                            <div class="space-y-4">
                                <div class="h-3 w-full rounded-full bg-blue-100/35" />
                                <div class="h-3 w-11/12 rounded-full bg-blue-100/35" />
                                <div class="h-40 rounded-2xl border border-blue-200/40 bg-blue-100/20 md:h-56" />
                            </div>
                        </article>

                        <article
                            class="home-parallax-screen relative h-80 w-full max-w-[430px] overflow-hidden rounded-3xl border border-blue-300 bg-gradient-to-b from-blue-500 to-blue-700 p-7 shadow-2xl shadow-blue-900/35 will-change-transform md:h-[500px] md:flex-[1.2]"
                            data-rellax-speed="1.5"
                        >
                            <div class="mb-7 h-3 w-44 rounded-full bg-blue-200/80" />
                            <div class="space-y-4">
                                <div class="h-3 w-full rounded-full bg-blue-100/40" />
                                <div class="h-3 w-10/12 rounded-full bg-blue-100/40" />
                                <div class="h-3 w-9/12 rounded-full bg-blue-100/40" />
                                <div class="h-48 rounded-2xl border border-blue-200/45 bg-blue-100/20 md:h-64" />
                            </div>
                        </article>

                        <article
                            class="home-parallax-screen relative h-72 w-full max-w-[380px] overflow-hidden rounded-3xl border border-blue-300 bg-gradient-to-b from-blue-500 to-blue-700 p-6 shadow-2xl shadow-blue-900/30 will-change-transform md:h-[430px] md:max-w-none md:flex-1"
                            data-rellax-speed="4"
                        >
                            <div class="mb-6 h-3 w-36 rounded-full bg-blue-200/70" />
                            <div class="space-y-4">
                                <div class="h-3 w-full rounded-full bg-blue-100/35" />
                                <div class="h-3 w-10/12 rounded-full bg-blue-100/35" />
                                <div class="h-40 rounded-2xl border border-blue-200/40 bg-blue-100/20 md:h-56" />
                            </div>
                        </article>
                    </div>
                </section>

                <section id="features" class="mx-auto mt-12 w-full max-w-7xl px-6">
                    <div class="mb-16 text-center" data-aos="fade-up">
                        <h2 class="mb-4 text-4xl font-semibold tracking-tight text-gray-900 md:text-5xl">Best Features</h2>
                        <p class="text-lg text-gray-600">Powerful capabilities designed for your peace of mind</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <article
                            v-for="(feature, index) in bestFeatures"
                            :key="feature.title"
                            data-aos="fade-up"
                            :data-aos-delay="index * 90"
                            class="group relative cursor-pointer overflow-hidden rounded-2xl border border-gray-200 bg-white p-10 shadow-sm transition-all hover:scale-[1.02] hover:border-blue-300 hover:shadow-xl hover:shadow-blue-500/10"
                        >
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/0 transition-all group-hover:from-blue-500/[0.05] group-hover:to-blue-500/[0.02]" />
                            <div class="absolute right-0 top-0 h-32 w-32 rounded-full bg-blue-500 opacity-0 blur-[80px] transition-opacity group-hover:opacity-10" />

                            <div class="relative">
                                <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50 transition-all group-hover:scale-110 group-hover:rotate-3">
                                    <component :is="feature.icon" class="h-8 w-8 text-blue-600" :stroke-width="1.5" />
                                </div>

                                <h3 class="mb-4 text-2xl font-bold tracking-tight text-gray-900">{{ feature.title }}</h3>
                                <p class="mb-6 leading-relaxed text-gray-600">{{ feature.description }}</p>
                                <div class="flex items-center gap-2 text-sm font-medium text-blue-600">
                                    <div class="h-1 w-1 rounded-full bg-blue-600" />
                                    <span>{{ feature.stats }}</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <section id="security" class="mx-auto w-full max-w-7xl px-6 py-32">
                    <div class="mb-20 text-center" data-aos="fade-up">
                        <h2 class="mb-4 text-4xl font-semibold tracking-tight text-gray-900 md:text-5xl">Security meets simplicity</h2>
                        <p class="text-lg text-gray-600">Everything you need to protect what matters most</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <article
                            v-for="(feature, index) in features"
                            :key="feature.title"
                            data-aos="fade-up"
                            :data-aos-delay="index * 70"
                            class="group relative cursor-pointer rounded-2xl border border-gray-200 bg-white p-8 shadow-sm transition-all hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg"
                        >
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-500/0 to-blue-500/0 transition-all group-hover:from-blue-500/[0.03] group-hover:to-blue-500/[0.01]" />
                            <div class="relative">
                                <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-100 to-blue-50 transition-transform group-hover:scale-110">
                                    <component :is="feature.icon" class="h-7 w-7 text-blue-600" :stroke-width="1.5" />
                                </div>
                                <h3 class="mb-3 text-xl font-bold tracking-tight text-gray-900">{{ feature.title }}</h3>
                                <p class="leading-relaxed text-gray-600">{{ feature.description }}</p>
                            </div>
                        </article>
                    </div>
                </section>

                <section id="pricing" class="mx-auto w-full max-w-7xl px-6 pb-28">
                    <div class="relative overflow-hidden rounded-3xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100/50 p-10 md:p-16" data-aos="zoom-in-up">
                        <div class="absolute right-0 top-0 h-64 w-64 rounded-full bg-blue-400 opacity-20 blur-[100px]" />
                        <div class="relative text-center">
                            <h2 class="mb-6 text-4xl font-semibold tracking-tight text-gray-900 md:text-6xl">
                                Ready to secure your digital life?
                            </h2>
                            <p class="mx-auto mb-10 max-w-2xl text-lg text-gray-600">
                                Join thousands of users who trust VaultGuardian to protect their most important accounts.
                                Start your 30-day free trial today.
                            </p>
                            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                                <Link
                                    href="/start-account"
                                    class="rounded-xl bg-blue-600 px-8 py-4 font-semibold text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/30"
                                >
                                    Get Started Free
                                </Link>
                                <a
                                    href="#"
                                    class="rounded-xl border border-gray-300 bg-white px-8 py-4 font-semibold text-gray-900 transition-all hover:border-blue-600"
                                >
                                    Contact Sales
                                </a>
                            </div>
                            <p class="mt-6 text-sm text-gray-500">No credit card required | Cancel anytime</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </DefaultLayout>
</template>
