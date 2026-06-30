<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const form = ref({
    name: '',
    email: '',
    message: '',
});

const isSubmitting = ref(false);
const isSuccess = ref(false);
const error = ref('');

const closeModal = () => {
    emit('close');
    setTimeout(() => {
        isSuccess.value = false;
        error.value = '';
        form.value = { name: '', email: '', message: '' };
    }, 300);
};

const handleSubmit = () => {
    if (!form.value.name || !form.value.email || !form.value.message) {
        error.value = 'Semua field harus diisi';
        return;
    }

    isSubmitting.value = true;
    error.value = '';

    router.post('/contact', form.value, {
        onSuccess: () => {
            isSuccess.value = true;
            isSubmitting.value = false;
        },
        onError: (errors) => {
            error.value = errors.message || 'Terjadi kesalahan. Silakan coba lagi.';
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            >
                <div
                    class="absolute inset-0 bg-ink/50 backdrop-blur-sm"
                    @click="closeModal"
                />

                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4"
                >
                    <div
                        v-if="show"
                        class="relative w-full max-w-md bg-paper rounded-2xl shadow-2xl border border-oat-dark overflow-hidden"
                    >
                        <div class="px-6 pt-6 pb-4 bg-gradient-to-br from-terracotta/10 to-transparent">
                            <button
                                @click="closeModal"
                                class="absolute top-4 right-4 p-2 text-taupe hover:text-ink hover:bg-oat/50 rounded-lg transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 6L6 18M6 6l12 12"/>
                                </svg>
                            </button>

                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-terracotta/10 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-serif text-xl font-bold text-ink">Hubungi Saya</h2>
                                    <p class="text-sm text-taupe">Kirim pesan dan saya akan segera merespons</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="isSuccess" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <h3 class="font-serif text-lg font-bold text-ink mb-2">Pesan Terkirim!</h3>
                            <p class="text-sm text-taupe mb-6">Terima kasih sudah menghubungi. Saya akan merespons pesan Anda secepat mungkin.</p>
                            <button
                                @click="closeModal"
                                class="px-6 py-2.5 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
                            >
                                Tutup
                            </button>
                        </div>

                        <form v-else @submit.prevent="handleSubmit" class="px-6 pb-6 space-y-4">
                            <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-xl">
                                <p class="text-sm text-red-600">{{ error }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ink mb-1.5">Nama</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Nama lengkap Anda"
                                    class="w-full px-4 py-3 bg-cream border border-oat-dark rounded-xl text-ink placeholder-taupe/50 focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta transition-all"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ink mb-1.5">Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="email@contoh.com"
                                    class="w-full px-4 py-3 bg-cream border border-oat-dark rounded-xl text-ink placeholder-taupe/50 focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta transition-all"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-ink mb-1.5">Pesan</label>
                                <textarea
                                    v-model="form.message"
                                    rows="4"
                                    placeholder="Tulis pesan Anda di sini..."
                                    class="w-full px-4 py-3 bg-cream border border-oat-dark rounded-xl text-ink placeholder-taupe/50 focus:outline-none focus:ring-2 focus:ring-terracotta/50 focus:border-terracotta transition-all resize-none"
                                />
                            </div>

                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full py-3 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
                            >
                                <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                <span v-if="isSubmitting">Mengirim...</span>
                                <span v-else class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="22" y1="2" x2="11" y2="13"/>
                                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                    </svg>
                                    Kirim Pesan
                                </span>
                            </button>

                            <p class="text-xs text-center text-taupe">Biasanya saya merespons dalam 1-2 hari kerja</p>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
