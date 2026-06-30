<script setup>
import { Link } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import Footer from '@/Components/Footer.vue';

defineProps({
    profile: {
        type: Object,
        default: () => ({
            name: 'Febryanus',
            email: 'hello@febryanus.dev',
            tagline: 'Portfolio'
        }),
    },
    socialLinks: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-cream via-oat/30 to-cream flex flex-col">
        <Navigation :profile="profile" variant="default" />

        <main class="flex-1 flex items-center justify-center relative overflow-hidden pt-20 pb-12">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-terracotta/10 rounded-full blur-3xl transform translate-x-1/3 -translate-y-1/3"></div>
                <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-terracotta-light/10 rounded-full blur-3xl transform -translate-x-1/3 translate-y-1/3"></div>
            </div>

            <div class="w-full max-w-md mx-4 relative z-10 px-2">
                <slot />
            </div>
        </main>

        <Footer :profile="profile" :social-links="socialLinks" variant="minimal" :show-social="true" />
    </div>
</template>
