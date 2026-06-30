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
    <div class="min-h-screen bg-cream flex flex-col">
        <Navigation :profile="profile" variant="default" />

        <main class="flex-1">
            <slot />
        </main>

        <Footer :profile="profile" :social-links="socialLinks" variant="minimal" :show-social="true" />
    </div>
</template>
