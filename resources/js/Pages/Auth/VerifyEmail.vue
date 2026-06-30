<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi Email" />

        <div class="mb-8 text-center">
            <h1 class="font-serif text-2xl font-bold text-ink mb-2">Verifikasi Email</h1>
            <p class="text-sm text-taupe">
                Kami telah mengirim link verifikasi ke email Anda.
            </p>
        </div>

        <div class="p-4 mb-6 bg-oat rounded-xl border border-oat-dark">
            <p class="text-sm text-taupe">
                Jika Anda belum menerima email verifikasi, klik tombol di bawah untuk mengirim ulang.
            </p>
        </div>

        <!-- Success Message -->
        <div v-if="status === 'verification-link-sent'" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <p class="text-sm text-green-700">
                Link verifikasi baru telah dikirim ke email Anda.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Mengirim...</span>
                    <span v-else>Kirimi Ulang Email Verifikasi</span>
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6 text-center">
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="text-sm text-taupe hover:text-red-500 transition-colors"
            >
                Keluar
            </Link>
        </div>
    </GuestLayout>
</template>
