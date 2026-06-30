<script setup>
import { ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import {
    LockClosedIcon,
    EnvelopeIcon,
    EyeIcon,
    EyeSlashIcon,
    ShieldCheckIcon,
} from '@heroicons/vue/24/outline';

const page = usePage();
const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post('/hyadms/malemologin/ds', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-terracotta/5 via-cream to-terracotta/10 flex items-center justify-center p-4">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiMwMDAiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+')] opacity-50" />

        <div class="relative w-full max-w-md">
            <!-- Logo & Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-terracotta shadow-lg shadow-terracotta/30 mb-4">
                    <ShieldCheckIcon class="w-8 h-8 text-cream" />
                </div>
                <h1 class="font-serif text-3xl font-bold text-ink mb-2">Admin Portal</h1>
                <p class="text-taupe text-sm">Portfolio Febryanus - Secure Access</p>
            </div>

            <!-- Login Card -->
            <div class="bg-paper rounded-2xl shadow-xl shadow-terracotta/10 border border-oat-dark p-8">
                <!-- Error Message -->
                <div
                    v-if="page.props.flash?.error"
                    class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl"
                >
                    <p class="text-sm text-red-600">{{ page.props.flash.error }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-ink mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <EnvelopeIcon class="h-5 w-5 text-taupe" />
                            </div>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="admin@example.com"
                                class="block w-full pl-10 pr-4 py-3 bg-cream border border-oat-dark rounded-xl text-ink placeholder-taupe focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta transition-all duration-200"
                                :class="{ 'border-red-400': form.errors.email }"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-ink mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <LockClosedIcon class="h-5 w-5 text-taupe" />
                            </div>
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="block w-full pl-10 pr-12 py-3 bg-cream border border-oat-dark rounded-xl text-ink placeholder-taupe focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta transition-all duration-200"
                                :class="{ 'border-red-400': form.errors.password }"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-taupe hover:text-ink transition-colors"
                            >
                                <EyeIcon v-if="!showPassword" class="h-5 w-5" />
                                <EyeSlashIcon v-else class="h-5 w-5" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="w-4 h-4 text-terracotta bg-cream border-oat-dark rounded focus:ring-terracotta/50"
                            />
                            <span class="text-sm text-taupe">Ingat saya</span>
                        </label>
                        <Link
                            href="/forgot-password"
                            class="text-sm text-terracotta hover:text-terracotta-dark transition-colors"
                        >
                            Lupa password?
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 px-4 bg-terracotta text-cream font-semibold rounded-xl hover:bg-terracotta-dark focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <span v-if="form.processing">Memproses...</span>
                        <span v-else>Masuk ke Dashboard</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-oat-dark"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-paper text-taupe">atau</span>
                    </div>
                </div>

                <!-- Back to User Login -->
                <p class="text-center text-sm text-taupe">
                    Login sebagai user?
                    <Link
                        href="/login"
                        class="text-terracotta hover:text-terracotta-dark font-medium transition-colors"
                    >
                        Klik di sini
                    </Link>
                </p>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-taupe/70 mt-6">
                © {{ new Date().getFullYear() }} Febryanus Tambing. All rights reserved.
            </p>
        </div>
    </div>
</template>
