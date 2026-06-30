<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Kata Sandi" />

        <div class="mb-8 text-center">
            <h1 class="font-serif text-2xl font-bold text-ink mb-2">Lupa Kata Sandi?</h1>
            <p class="text-sm text-taupe">
                Tenang, kami akan kirimkan link reset ke email Anda.
            </p>
        </div>

        <!-- Success Message -->
        <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <p class="text-sm text-green-700">{{ status }}</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Alamat Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Mengirim...</span>
                    <span v-else>Kirimi Link Reset</span>
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-taupe">
                Ingat kata sandi Anda?
                <Link
                    :href="route('login')"
                    class="text-terracotta hover:text-terracotta-dark font-medium transition-colors"
                >
                    Masuk di sini
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>
