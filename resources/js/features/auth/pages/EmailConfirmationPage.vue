<script setup>
import {Head} from '@inertiajs/vue3';
import {Star} from 'lucide-vue-next';
import {route} from 'ziggy-js';
import AuthLayout from "@/shared/layouts/AuthLayout.vue";

const props = defineProps({
    email: {
        type: String,
        default: '',
    },
});

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
</script>

<template>
    <Head title="Confirm Email | VaultGuardian"/>
    <AuthLayout>
        <div class="relative min-h-screen overflow-x-hidden bg-white text-gray-900">
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

            <div class="fixed left-1/4 top-0 h-96 w-96 rounded-full bg-blue-500 opacity-[0.15] blur-[120px]"/>
            <div class="fixed bottom-0 right-1/4 h-96 w-96 rounded-full bg-blue-400 opacity-[0.12] blur-[120px]"/>

            <div class="relative z-10">


                <div class="mx-auto max-w-2xl px-6 py-20">
                    <div class="flex flex-col items-center text-center">

                        <h1
                            class="animate-fade-up mb-6 text-5xl tracking-tight text-gray-900 md:text-6xl"
                            style="animation-delay: 240ms; font-family: 'DM Sans', sans-serif; font-weight: 700; line-height: 1.1;"
                        >
                            Please check your email
                        </h1>

                        <div class="animate-fade-up mb-4" style="animation-delay: 360ms;">
                            <p class="mb-3 text-lg text-gray-600" style="font-family: 'DM Sans', sans-serif;">
                                We've sent a verification link to
                            </p>
                            <p class="mb-6 text-xl text-blue-600"
                               style="font-family: 'DM Sans', sans-serif; font-weight: 600;">
                                {{ props.email || 'your email address' }}
                            </p>
                            <p class="text-gray-500" style="font-family: 'DM Sans', sans-serif;">
                                Click the link in the email to verify your account and continue setting up your vault
                                from there.
                            </p>
                            <p class="mt-4 text-sm text-gray-700" style="font-family: 'DM Sans', sans-serif;">
                                Didn't receive the email? You might already have an account.
                                <a :href="route('login')" class="font-semibold text-blue-600 hover:text-blue-700">
                                    Log in here
                                </a>.
                            </p>
                        </div>

                        <div class="animate-fade-up mt-4" style="animation-delay: 720ms;">
                            <a
                                :href="route('start-account')"
                                class="text-sm text-gray-600 transition-colors hover:text-blue-600"
                                style="font-family: 'DM Sans', sans-serif;"
                            >
                                Wrong email? <span style="font-weight: 600;">Go back and change it</span>
                            </a>
                        </div>

                        <section class="mx-auto mt-6 w-full max-w-5xl animate-fade-up" style="animation-delay: 800ms;">
                            <div class="mb-5 text-center">
                                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-blue-600">
                                    Trusted by Reviewers
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Independent customer review platforms
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <article
                                    v-for="(review, index) in trustReviews"
                                    :key="review.source"
                                    class="rounded-xl border border-blue-100 bg-white/70 p-5 text-left shadow-sm backdrop-blur transition-all hover:-translate-y-0.5 hover:shadow-md"
                                    :data-aos-delay="880 + (index * 90)"
                                >
                                    <div class="mb-3 flex items-center justify-between">
                                        <p class="text-base font-semibold text-gray-900" style="font-family: 'DM Sans', sans-serif;">{{ review.source }}</p>
                                        <p class="text-sm font-bold text-blue-600">{{ review.rating }}</p>
                                    </div>

                                    <div class="mb-3 flex items-center gap-1.5">
                                        <Star v-for="star in 5" :key="`${review.source}-${star}`" class="h-4 w-4 fill-blue-500 text-blue-500" />
                                    </div>

                                    <p class="mb-2 text-sm font-medium text-gray-700" style="font-family: 'DM Sans', sans-serif;">{{ review.summary }}</p>
                                    <p class="text-xs text-gray-500" style="font-family: 'DM Sans', sans-serif;">{{ review.reviews }}</p>
                                </article>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<style scoped>
@keyframes fade-up {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-down {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes glow {
    0% {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    }
    50% {
        box-shadow: 0 0 40px rgba(59, 130, 246, 0.5);
    }
    100% {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    }
}

@keyframes orbit {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.animate-fade-up {
    opacity: 0;
    animation: fade-up 0.8s ease forwards;
}

.animate-fade-down {
    opacity: 0;
    animation: fade-down 0.6s ease forwards;
}

.animate-glow {
    animation: glow 2s ease-in-out infinite;
}

.animate-orbit {
    animation: orbit 20s linear infinite;
}
</style>
