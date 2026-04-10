<script setup>
import { Head } from '@inertiajs/vue3';
import HomeCtaSection from '../components/HomeCtaSection.vue';
import HomeFeaturesSection from '../components/HomeFeaturesSection.vue';
import HomeHeroSection from '../components/HomeHeroSection.vue';
import HomePricingSection from '../components/HomePricingSection.vue';
import DefaultLayout from '../../../shared/layouts/DefaultLayout.vue';
</script>

<template>
    <Head title="The Vault | Enterprise Grade Security" />

    <DefaultLayout>
        <HomeHeroSection />
        <HomeFeaturesSection />
        <HomePricingSection />
        <HomeCtaSection />
    </DefaultLayout>
</template>
