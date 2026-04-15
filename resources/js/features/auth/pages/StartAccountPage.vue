<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ArrowRight, Eye, Fingerprint, Mail, Shield, Star } from 'lucide-vue-next';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import AuthLayout from '../../../shared/layouts/AuthLayout.vue';

const page = usePage();

const startAccountRequest = useForm({
    email: '',
});

const emailError = computed(() => startAccountRequest.errors.email || page.props.errors?.email || '');

const trustReviews = [
    {
        source: 'Trustpilot',
        rating: '4.8/5',
        summary: 'Top-rated security and ease of use',
        reviews: '12,000+ reviews',
    },
    {
        source: 'G2',
        rating: '4.7/5',
        summary: 'Leader in password manager satisfaction',
        reviews: '3,500+ reviews',
    },
    {
        source: 'Capterra',
        rating: '4.9/5',
        summary: 'Highly recommended by business teams',
        reviews: '2,100+ reviews',
    },
];

function handleSubmit() {
    startAccountRequest.post(route('start-account.perform'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Start Account | VaultGuardian" />

    <AuthLayout>
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

            <section class="relative z-10 mx-auto w-full max-w-4xl px-6 pb-20 pt-14 text-center">
                <h1 class="mb-6 text-5xl tracking-tight text-gray-900 md:text-7xl">
                    Start your
                    <span class="bg-gradient-to-r from-blue-600 to-blue-400 bg-clip-text font-bold text-transparent">
                        secure journey
                    </span>
                </h1>

                <p class="mx-auto mb-10 max-w-2xl text-lg text-gray-600">
                    Enter your email to receive a secure verification link. After verification, you will finish setup by
                    creating your master password.
                </p>

                <form class="mx-auto w-full max-w-3xl text-left" @submit.prevent="handleSubmit">
                    <div class="mb-4 flex flex-col gap-3 sm:relative sm:block">
                        <Mail class="left-6 top-1/2 z-10 hidden h-6 w-6 -translate-y-1/2 text-gray-400 sm:absolute sm:block" />
                        <input
                            id="email"
                            v-model="startAccountRequest.email"
                            type="email"
                            autocomplete="email"
                            placeholder="Enter your email address"
                            class="w-full rounded-2xl border-2 border-gray-300 px-5 py-4 text-base shadow-lg transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/20 sm:py-6 sm:pl-16 sm:pr-52 sm:text-lg"
                            @input="startAccountRequest.clearErrors('email')"
                        />
                        <button
                            type="submit"
                            :disabled="startAccountRequest.processing"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-8 py-4 font-semibold text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/30 disabled:cursor-not-allowed disabled:opacity-70 sm:absolute sm:right-2 sm:top-1/2 sm:w-auto sm:-translate-y-1/2"
                        >
                            Send Link
                            <ArrowRight class="h-5 w-5" />
                        </button>
                    </div>

                    <p v-if="emailError" class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700">
                        {{ emailError }}
                    </p>

                    <p class="mt-4 text-center text-xs text-gray-500 sm:text-sm">
                        By continuing, you agree to our
                        <Link :href="route('terms', {}, false)" class="font-medium text-blue-600 hover:text-blue-700">Terms of Service</Link>
                        and
                        <Link :href="route('privacy', {}, false)" class="font-medium text-blue-600 hover:text-blue-700">Privacy Policy</Link>.
                    </p>
                </form>

                <div class="mx-auto mt-8 w-full max-w-5xl rounded-2xl border border-blue-100 bg-white/85 p-6 text-left shadow-sm backdrop-blur">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold uppercase tracking-[0.14em] text-blue-600">
                            Trusted by Reviewers
                        </p>
                        <p class="text-xs text-gray-500">
                            Independent customer review platforms
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <article
                            v-for="review in trustReviews"
                            :key="review.source"
                            class="rounded-xl border border-gray-200 bg-gradient-to-br from-white to-blue-50/40 p-5"
                        >
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-base font-semibold text-gray-900">{{ review.source }}</p>
                                <p class="text-sm font-bold text-blue-600">{{ review.rating }}</p>
                            </div>

                            <div class="mb-3 flex items-center gap-1.5">
                                <Star v-for="star in 5" :key="`${review.source}-${star}`" class="h-4 w-4 fill-blue-500 text-blue-500" />
                            </div>

                            <p class="mb-2 text-sm font-medium text-gray-700">{{ review.summary }}</p>
                            <p class="text-xs text-gray-500">{{ review.reviews }}</p>
                        </article>
                    </div>
                </div>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-4 text-sm text-gray-500 md:gap-8">
                    <div class="flex items-center gap-2">
                        <Shield class="h-4 w-4 text-blue-600" />
                        <span>256-bit AES encryption</span>
                    </div>
                    <div class="hidden h-4 w-px bg-gray-300 md:block" />
                    <div class="flex items-center gap-2">
                        <Fingerprint class="h-4 w-4 text-blue-600" />
                        <span>Biometric authentication</span>
                    </div>
                    <div class="hidden h-4 w-px bg-gray-300 md:block" />
                    <div class="flex items-center gap-2">
                        <Eye class="h-4 w-4 text-blue-600" />
                        <span>Zero-knowledge architecture</span>
                    </div>
                </div>
            </section>
        </div>
    </AuthLayout>
</template>
