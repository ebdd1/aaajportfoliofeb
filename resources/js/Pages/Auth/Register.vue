<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    intended: String,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const page = usePage();
const intended = page.props.intended;
</script>

<template>
    <GuestLayout>
        <Head title="Daftar" />

        <!-- Intended URL Banner -->
        <div v-if="intended" class="mb-6 p-4 bg-terracotta/10 border border-terracotta/20 rounded-2xl">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-terracotta shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <div>
                    <p class="text-sm text-ink font-medium">Daftar untuk melanjutkan</p>
                    <p class="text-xs text-taupe">Setelah daftar, kamu akan diarahkan ke checkout</p>
                </div>
            </div>
        </div>

        <!-- Logo/Brand Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-terracotta/10 rounded-2xl mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
            </div>
            <h1 class="font-serif text-3xl font-bold text-ink mb-2">Buat Akun Baru</h1>
            <p class="text-taupe">Daftar untuk mulai berbelanja</p>
        </div>

        <!-- Form Card -->
        <div class="bg-paper/80 backdrop-blur-xl rounded-3xl p-8 shadow-xl shadow-terracotta/5 border border-oat-dark/20">
            <form @submit.prevent="submit" class="space-y-5">
                <!-- Name -->
                <div>
                    <InputLabel for="name" value="Nama Lengkap" class="mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-taupe/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <TextInput
                            id="name"
                            type="text"
                            class="pl-12 bg-cream/50"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Masukkan nama lengkap"
                        />
                    </div>
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <InputLabel for="email" value="Alamat Email" class="mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-taupe/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <TextInput
                            id="email"
                            type="email"
                            class="pl-12 bg-cream/50"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="nama@email.com"
                        />
                    </div>
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <InputLabel for="password" value="Kata Sandi" class="mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-taupe/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            class="pl-12 bg-cream/50"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                        />
                    </div>
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <InputLabel for="password_confirmation" value="Konfirmasi Kata Sandi" class="mb-2" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-taupe/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                <path d="M9 17v2a2 2 0 0 0 4 0v-2"/>
                            </svg>
                        </div>
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="pl-12 bg-cream/50"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi kata sandi"
                        />
                    </div>
                    <InputError :message="form.errors.password_confirmation" class="mt-2" />
                </div>

                <!-- Submit -->
                <div class="pt-2">
                    <PrimaryButton
                        class="w-full justify-center py-4 text-base"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        <template v-if="form.processing">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 0 12s5.373 12 12 12h0a8 8 0 008-8V0z"></path>
                            </svg>
                            Memproses...
                        </template>
                        <template v-else>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <line x1="19" y1="8" x2="19" y2="14"/>
                                <line x1="22" y1="11" x2="16" y2="11"/>
                            </svg>
                            Daftar
                        </template>
                    </PrimaryButton>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-oat-dark/30"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-paper text-taupe">atau</span>
                </div>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-taupe mb-4">Sudah punya akun?</p>
                <Link
                    :href="route('login')"
                    class="inline-flex items-center justify-center gap-2 w-full py-4 rounded-2xl border-2 border-oat-dark text-ink font-semibold hover:border-terracotta hover:bg-terracotta/5 transition-all duration-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Masuk di Sini
                </Link>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <Link href="/" class="inline-flex items-center gap-2 text-sm text-taupe hover:text-ink transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5"/>
                    <path d="m12 19-7-7 7-7"/>
                </svg>
                Kembali ke Beranda
            </Link>
        </div>
    </GuestLayout>
</template>
