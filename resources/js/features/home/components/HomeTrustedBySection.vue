<script setup>
const trustedBrands = [
    { name: 'Google', logo: '/images/trusted/google.svg' },
    { name: 'Microsoft', logo: '/images/trusted/microsoft.svg' },
    { name: 'Amazon', logo: '/images/trusted/amazon.svg' },
    { name: 'Apple', logo: '/images/trusted/apple.svg' },
    { name: 'Netflix', logo: '/images/trusted/netflix.svg' },
    { name: 'Spotify', logo: '/images/trusted/spotify.svg' },
    { name: 'PayPal', logo: '/images/trusted/paypal.svg' },
    { name: 'Adobe', logo: '/images/trusted/adobe.svg' },
];

const marqueeBrands = [...trustedBrands, ...trustedBrands];
</script>

<template>
    <section class="mx-auto w-full max-w-7xl px-6 py-24">
        <div class="mb-10 text-center" data-aos="fade-up">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700">Trusted By</p>
            <h2 class="text-4xl font-semibold tracking-tight text-gray-900 md:text-5xl">Teams that trust us.</h2>
        </div>

        <div class="trusted-strip relative overflow-hidden py-5" data-aos="fade-up" data-aos-delay="80">
            <div class="trusted-track flex min-w-max items-center gap-12 md:gap-16">
                <div
                    v-for="(brand, index) in marqueeBrands"
                    :key="`${brand.name}-${index}`"
                    class="flex h-12 w-[120px] shrink-0 items-center justify-center md:w-[140px]"
                >
                    <img
                        :src="brand.logo"
                        :alt="`${brand.name} logo`"
                        class="max-h-10 w-auto max-w-full object-contain opacity-60 grayscale saturate-0 transition-all duration-300 hover:opacity-100 hover:grayscale-0 hover:saturate-100"
                        loading="lazy"
                    >
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.trusted-strip {
    -webkit-mask-image: linear-gradient(90deg, transparent 0%, #000 9%, #000 91%, transparent 100%);
    mask-image: linear-gradient(90deg, transparent 0%, #000 9%, #000 91%, transparent 100%);
}

.trusted-track {
    animation: trusted-scroll 26s linear infinite;
}

@keyframes trusted-scroll {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}
</style>
