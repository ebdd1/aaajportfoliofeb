<script setup>
import { Link } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import Footer from '@/Components/Footer.vue';
import { HomeIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline';

defineProps({
    status: {
        type: Number,
        default: 404,
    },
});
</script>

<template>
    <div class="min-h-screen bg-cream flex flex-col">
        <Navigation />

        <main class="flex-1 flex items-center justify-center p-4">
            <div class="text-center max-w-md">
                <!-- 404 Icon -->
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-terracotta/10">
                        <span class="font-serif text-5xl font-bold text-terracotta">404</span>
                    </div>
                </div>

                <!-- Error Message -->
                <h1 class="font-serif text-2xl font-bold text-ink mb-3">
                    Halaman Tidak Ditemukan
                </h1>
                <p class="text-taupe mb-8">
                    Error 404 - Tidak ada halaman disini
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <Link
                        href="/"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
                    >
                        <HomeIcon class="w-5 h-5" />
                        Kembali ke Home
                    </Link>
                    <button
                        @click="$router.back()"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-paper border border-oat-dark text-ink font-medium rounded-xl hover:bg-oat transition-colors"
                    >
                        <ArrowLeftIcon class="w-5 h-5" />
                        Halaman Sebelumnya
                    </button>
                </div>
            </div>
        </main>

        <Footer variant="minimal" />
    </div>
</template>
