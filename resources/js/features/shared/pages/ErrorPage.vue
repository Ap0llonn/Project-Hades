<script setup>
import { Head } from '@inertiajs/vue3';
import { Home, Search, ShieldAlert } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import AuthLayout from '../../../shared/layouts/AuthLayout.vue';

const props = defineProps({
    errorCode: {
        type: [String, Number],
        default: '404',
    },
    title: {
        type: String,
        default: 'Page not found',
    },
    message: {
        type: String,
        default: "The page you're looking for doesn't exist or has been moved.",
    },
});

function goBack() {
    window.history.back();
}
</script>

<template>
    <Head :title="`${props.errorCode} | VaultGuardian`" />

    <AuthLayout>
        <div class="relative min-h-screen overflow-x-hidden bg-white text-gray-900">
            <div class="pointer-events-none fixed inset-0 opacity-[0.4]">
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

            <div class="fixed left-1/4 top-0 h-96 w-96 rounded-full bg-blue-500 opacity-[0.15] blur-[120px]" />
            <div class="fixed bottom-0 right-1/4 h-96 w-96 rounded-full bg-blue-400 opacity-[0.12] blur-[120px]" />

            <div class="relative z-10">

                <div class="mx-auto max-w-4xl px-6 py-20">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative mb-12">
                            <div class="flex h-32 w-32 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-100 to-blue-50">
                                <ShieldAlert class="h-16 w-16 text-blue-600" :stroke-width="1.5" />
                            </div>
                            <div class="absolute -right-2 -top-2 flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600">
                                <Search class="h-4 w-4 text-white" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <span
                                class="bg-gradient-to-r from-blue-600 to-blue-400 bg-clip-text text-8xl tracking-tight text-transparent"
                                style="font-family: 'DM Sans', sans-serif; font-weight: 700;"
                            >
                                {{ props.errorCode }}
                            </span>
                        </div>

                        <h1
                            class="mb-4 text-4xl tracking-tight text-gray-900 md:text-5xl"
                            style="font-family: 'DM Sans', sans-serif; font-weight: 600;"
                        >
                            {{ props.title }}
                        </h1>

                        <p class="mb-12 max-w-xl text-lg text-gray-600" style="font-family: 'DM Sans', sans-serif;">
                            {{ props.message }}
                        </p>

                        <div class="flex gap-4">
                            <a
                                :href="route('home')"
                                class="flex items-center gap-2 rounded-xl bg-blue-600 px-8 py-4 text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-600/30"
                                style="font-family: 'DM Sans', sans-serif; font-weight: 600;"
                            >
                                <Home class="h-5 w-5" />
                                Back to Home
                            </a>
                            <button
                                type="button"
                                class="rounded-xl border border-gray-300 px-8 py-4 text-gray-900 transition-all hover:border-blue-600"
                                style="font-family: 'DM Sans', sans-serif; font-weight: 600;"
                                @click="goBack"
                            >
                                Go Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
