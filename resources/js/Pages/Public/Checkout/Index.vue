<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import Footer from '@/Components/Footer.vue';

const props = defineProps({
    product: Object,
    profile: Object,
    socialLinks: Array,
});

const selectedMethod = ref('qris');
const isSubmitting = ref(false);

const paymentMethods = [
    { id: 'qris', name: 'QRIS', icon: '📱' },
    { id: 'bni_va', name: 'BNI VA', icon: '🏦' },
    { id: 'bri_va', name: 'BRI VA', icon: '🏦' },
    { id: 'mandiri_va', name: 'Mandiri VA', icon: '🏦' },
];

const formatPrice = (price) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
};

const handleCheckout = () => {
    isSubmitting.value = true;
    router.post(route('checkout.initiate', props.product.slug), {
        payment_method: selectedMethod.value,
    });
};
</script>

<template>
    <div class="min-h-screen bg-cream flex flex-col">
        <Navigation :profile="profile" variant="minimal" />

        <main class="flex-1 py-12" aria-busy="isSubmitting">
            <div class="container mx-auto px-4 max-w-2xl">
                <h1 class="font-serif text-3xl font-bold text-ink mb-8">Checkout</h1>

                <section aria-labelledby="product-heading" class="bg-paper rounded-2xl border border-oat-dark p-6 mb-6">
                    <h2 id="product-heading" class="semibold text-ink mb-4">Produk</h2>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-terracotta/10 rounded-xl flex items-center justify-center">
                            <img v-if="product.thumbnail" :src="product.thumbnail" :alt="product.name" class="w-full h-full object-cover rounded-xl" />
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-ink">{{ product.name }}</h3>
                            <p class="text-sm text-taupe">{{ product.category?.name }}</p>
                            <p class="font-bold text-terracotta mt-1">{{ formatPrice(product.price) }}</p>
                        </div>
                    </div>
                </section>

                <section aria-labelledby="payment-heading">
                    <h2 id="payment-heading" class="semibold text-ink mb-4">Metode Pembayaran</h2>
                    <div role="radiogroup" aria-label="Pilihan metode pembayaran" class="space-y-3">
                        <label
                            v-for="method in paymentMethods"
                            :key="method.id"
                            :class="[
                                'flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all',
                                selectedMethod === method.id
                                    ? 'border-terracotta bg-terracotta/5'
                                    : 'border-oat-dark hover:border-terracotta/50'
                            ]"
                        >
                            <input
                                v-model="selectedMethod"
                                type="radio"
                                :value="method.id"
                                class="sr-only"
                                :aria-label="method.name"
                            />
                            <span class="text-2xl" aria-hidden="true">{{ method.icon }}</span>
                            <span class="font-medium text-ink">{{ method.name }}</span>
                            <svg v-if="selectedMethod === method.id" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-terracotta ml-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 13"/>
                            </svg>
                        </label>
                    </div>

                    <button
                        @click="handleCheckout"
                        :disabled="isSubmitting"
                        aria-busy="isSubmitting"
                        class="w-full mt-6 py-4 bg-terracotta text-cream font-semibold rounded-xl hover:bg-terracotta-dark disabled:opacity-50 transition-colors focus:outline-none focus:ring-2 focus:ring-terracotta/50">
                        <span v-if="isSubmitting">Memproses...</span>
                        <span v-else>Bayar Sekarang</span>
                    </button>
                </section>
            </div>
        </main>

        <Footer :profile="profile" :social-links="socialLinks" variant="minimal" />
    </div>
</template>
