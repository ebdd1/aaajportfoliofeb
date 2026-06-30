<script setup>
import { ref, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import Footer from '@/Components/Footer.vue';
import { useCart } from '@/stores/cart';

const props = defineProps({
    profile: Object,
    socialLinks: Array,
});

const { items, total, count, isEmpty, removeItem, clearCart, showToast } = useCart();
const isSubmitting = ref(false);

const selectedMethod = ref('qris');

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
    if (items.value.length === 0) {
        showToast('Keranjang kosong', 'warning');
        return;
    }

    // For single item - redirect to checkout initiate
    if (items.value.length === 1) {
        const firstItem = items.value[0];
        router.post(route('checkout.initiate', firstItem.product.slug), {
            payment_method: selectedMethod.value,
        });
    } else {
        // Multiple items - show info toast
        showToast('Checkout multiple item belum tersedia. Silakan checkout satu per satu.', 'info');
    }
};

const goToProductCheckout = (item) => {
    router.get(route('checkout.show', item.product.slug));
};
</script>

<template>
    <div class="min-h-screen bg-cream flex flex-col">
        <Navigation :profile="profile" variant="minimal" />

        <main class="flex-1 py-8" aria-busy="isSubmitting">
            <div class="container mx-auto px-4 max-w-2xl">
                <!-- Header -->
                <div class="mb-6">
                    <Link href="/products" class="text-sm text-terracotta hover:text-terracotta-dark mb-4 inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                        Lanjut Belanja
                    </Link>
                    <h1 class="font-serif text-3xl font-bold text-ink">Checkout Keranjang</h1>
                    <p class="text-taupe mt-1">{{ count }} item di keranjang</p>
                </div>

                <!-- Empty State -->
                <div v-if="isEmpty" class="bg-paper rounded-2xl border border-oat-dark p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-oat flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                        </svg>
                    </div>
                    <h3 class="font-medium text-ink mb-2">Keranjang Kosong</h3>
                    <p class="text-sm text-taupe mb-4">Yuk mulai belanja produk digital berkualitas</p>
                    <Link href="/products" class="inline-block px-6 py-2 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors">
                        Jelajahi Produk
                    </Link>
                </div>

                <!-- Cart Items -->
                <div v-else class="space-y-4" role="list" aria-label="Item keranjang">
                    <!-- Items List -->
                    <div class="bg-paper rounded-2xl border border-oat-dark divide-y divide-oat-dark/50" role="listitem">
                        <div v-for="item in items" :key="item.id || item.product_id" class="p-4 flex items-center gap-4">
                            <!-- Image -->
                            <div class="w-16 h-16 bg-terracotta/10 rounded-xl flex items-center justify-center shrink-0 overflow-hidden">
                                <img v-if="item.product?.thumbnail" :src="item.product.thumbnail" :alt="item.product.name" class="w-full h-full object-cover" />
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-terracotta/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-ink line-clamp-1">{{ item.product?.name }}</h4>
                                <p class="text-sm text-taupe">Qty: {{ item.quantity }}</p>
                            </div>

                            <!-- Price & Remove -->
                            <div class="text-right">
                                <p class="font-bold text-terracotta">{{ formatPrice((item.product?.price || 0) * item.quantity) }}</p>
                                <button @click="removeItem(item.product_id)" class="text-xs text-red-500 hover:text-red-600 mt-1">Hapus</button>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="bg-paper rounded-2xl border border-oat-dark p-4 flex items-center justify-between">
                        <span class="text-taupe">Total</span>
                        <span class="font-serif text-2xl font-bold text-terracotta">{{ formatPrice(total) }}</span>
                    </div>

                    <!-- Checkout Option Based on Item Count -->
                    <div v-if="items.length === 1">
                        <!-- Single Item: Direct Checkout -->
                        <div class="bg-paper rounded-2xl border border-oat-dark p-4">
                            <h3 class="font-medium text-ink mb-3">Checkout Item Ini</h3>
                            <button
                                @click="handleCheckout"
                                :disabled="isSubmitting"
                                class="w-full py-3 bg-terracotta text-cream font-semibold rounded-xl hover:bg-terracotta-dark disabled:opacity-50 transition-colors focus:outline-none focus:ring-2 focus:ring-terracotta/50"
                                aria-busy="isSubmitting">
                                <span v-if="isSubmitting">Memproses...</span>
                                <span v-else>Bayar Sekarang</span>
                            </button>
                        </div>
                    </div>

                    <div v-else>
                        <!-- Multiple Items: Show Note -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4" role="status">
                            <p class="text-sm text-yellow-800">
                                <strong>Info:</strong> Checkout multiple item belum tersedia. Silakan checkout satu per satu untuk saat ini.
                            </p>
                        </div>

                        <!-- Per-Item Checkout -->
                        <div class="bg-paper rounded-2xl border border-oat-dark p-4">
                            <h3 class="font-medium text-ink mb-3">Checkout Item</h3>
                            <div class="space-y-2">
                                <button
                                    v-for="item in items"
                                    :key="item.id"
                                    @click="goToProductCheckout(item)"
                                    class="w-full py-3 px-4 bg-oat/50 hover:bg-oat text-ink font-medium rounded-xl transition-colors text-left flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-terracotta/50"
                                    :aria-label="'Checkout ' + item.product?.name">
                                    <span class="line-clamp-1 mr-2">{{ item.product?.name }}</span>
                                    <span class="shrink-0 text-terracotta font-bold">{{ formatPrice(item.product?.price) }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Clear Cart -->
                    <button @click="clearCart" class="w-full py-2 text-sm text-taupe hover:text-red-500 transition-colors focus:outline-none focus:underline">
                        Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </main>

        <Footer :profile="profile" :social-links="socialLinks" variant="minimal" />
    </div>
</template>
