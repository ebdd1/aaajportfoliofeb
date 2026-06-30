<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Konfirmasi Password" />

        <div class="mb-8 text-center">
            <h1 class="font-serif text-2xl font-bold text-ink mb-2">Konfirmasi Password</h1>
            <p class="text-sm text-taupe">
                Ini adalah area aman aplikasi. Mohon konfirmasi password Anda sebelum melanjutkan.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1"
                    v-model="form.password"
                    required
                    autofocus
                    autocomplete="current-password"
                    placeholder="Masukkan password Anda"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else>Konfirmasi</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
