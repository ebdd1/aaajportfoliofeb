<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ArrowDownTrayIcon, ShoppingBagIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

defineProps({
    orders: Array,
});

const downloadingIds = ref(new Set());
const isDownloading = (id) => downloadingIds.value.has(id);

const downloadFile = (orderId, productId) => {
    downloadingIds.value.add(productId);
    window.location.href = route('download.product', { order: orderId, product: productId });
    setTimeout(() => {
        downloadingIds.value.delete(productId);
    }, 2000);
};

const formatPrice = (amount) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusTheme = (status) => {
    switch (status) {
        case 'paid': return { color: 'text-terracotta', dot: 'bg-terracotta', bg: 'bg-terracotta/5', label: 'Lunas' };
        case 'pending': return { color: 'text-amber-600', dot: 'bg-amber-500', bg: 'bg-amber-50', label: 'Menunggu Pembayaran' };
        case 'expired': return { color: 'text-red-600', dot: 'bg-red-500', bg: 'bg-red-50', label: 'Kedaluwarsa' };
        case 'cancelled': return { color: 'text-taupe', dot: 'bg-taupe', bg: 'bg-oat', label: 'Dibatalkan' };
        default: return { color: 'text-taupe', dot: 'bg-taupe', bg: 'bg-oat', label: status };
    }
};
</script>

<template>
    <UserLayout title="Pembelian Saya">
        <div>
            <!-- Back to Dashboard -->
            <Link :href="route('user.dashboard.home')" class="inline-flex items-center gap-2 text-sm text-taupe hover:text-terracotta transition-colors mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Kembali ke Dashboard
            </Link>

            <!-- Header -->
            <div class="mb-10">
                <h1 class="font-serif text-3xl md:text-4xl font-bold text-ink mb-3 tracking-tight">Pembelian Saya</h1>
                <p class="text-taupe text-lg">Akses, pantau, dan unduh produk digital yang telah Anda beli.</p>
            </div>

            <!-- Orders List -->
            <div v-if="orders && orders.length > 0" class="space-y-8">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="bg-paper rounded-2xl border border-oat-dark shadow-sm overflow-hidden"
                >
                    <!-- Order Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 md:px-6 border-b border-oat-dark bg-cream/30">
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                            <div>
                                <p class="text-xs text-taupe font-medium uppercase tracking-wider mb-0.5">Order ID</p>
                                <p class="font-mono text-sm font-semibold text-ink">{{ order.order_id }}</p>
                            </div>
                            <div class="h-8 w-px bg-oat-dark hidden sm:block"></div>
                            <div>
                                <p class="text-xs text-taupe font-medium uppercase tracking-wider mb-0.5">Tanggal</p>
                                <p class="text-sm font-medium text-ink">{{ order.paid_at ? formatDate(order.paid_at) : formatDate(order.created_at) }}</p>
                            </div>
                            <div class="h-8 w-px bg-oat-dark hidden sm:block"></div>
                            <div>
                                <p class="text-xs text-taupe font-medium uppercase tracking-wider mb-0.5">Total</p>
                                <p class="text-sm font-bold text-ink">{{ formatPrice(order.total_amount) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <Link :href="route('orders.show', order.order_id)" class="px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-ink border border-oat-dark rounded-lg hover:bg-oat transition-colors hidden sm:block">
                                Detail Pesanan
                            </Link>
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg" :class="getStatusTheme(order.status).bg">
                                <span :class="['w-2 h-2 rounded-full', getStatusTheme(order.status).dot]"></span>
                                <span :class="['text-xs font-bold uppercase tracking-wide', getStatusTheme(order.status).color]">
                                    {{ getStatusTheme(order.status).label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="p-5 md:px-6 space-y-4">
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex flex-col sm:flex-row sm:items-center gap-4"
                        >
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-16 h-16 rounded-xl bg-oat flex items-center justify-center shrink-0 text-ink">
                                    <ShoppingBagIcon class="w-7 h-7" />
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-serif font-bold text-ink text-lg truncate mb-1">
                                        {{ item.product?.name || 'Produk Digital' }}
                                    </h4>
                                    <p class="text-sm font-medium text-taupe">{{ formatPrice(item.price_at_purchase || order.total_amount) }}</p>
                                </div>
                            </div>

                            <div class="sm:shrink-0 pt-2 sm:pt-0 border-t border-oat-dark sm:border-0 mt-2 sm:mt-0">
                                <div v-if="order.status === 'paid'">
                                    <button
                                        @click="downloadFile(order.id, item.product_id)"
                                        :disabled="isDownloading(item.product_id)"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-ink text-paper text-sm font-medium rounded-xl hover:bg-terracotta transition-colors disabled:opacity-70 disabled:cursor-not-allowed shadow-sm"
                                    >
                                        <template v-if="isDownloading(item.product_id)">
                                            <svg class="animate-spin h-5 w-5 text-paper" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Mengunduh...
                                        </template>
                                        <template v-else>
                                            <ArrowDownTrayIcon class="w-5 h-5" />
                                            Unduh Produk
                                        </template>
                                    </button>
                                </div>
                                <div v-else-if="order.status === 'pending'" class="text-sm font-medium text-amber-600 flex items-center gap-1.5">
                                    <ExclamationCircleIcon class="w-5 h-5" />
                                    Menunggu pembayaran
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Payment Info -->
                    <div v-if="order.status === 'pending' && order.payment_number" class="p-5 md:px-6 bg-amber-50/50 border-t border-amber-100 flex flex-col sm:flex-row gap-4 sm:items-center justify-between">
                        <div>
                            <p class="text-sm text-amber-900 mb-1">
                                <span class="font-medium">Virtual Account:</span> <span class="font-mono font-bold">{{ order.payment_number }}</span>
                            </p>
                            <p class="text-xs text-amber-700">
                                Segera selesaikan pembayaran sebelum <span class="font-semibold">{{ formatDate(order.expired_at) }}</span>
                            </p>
                        </div>
                        <a
                            :href="order.checkout_url"
                            target="_blank"
                            v-if="order.checkout_url"
                            class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors"
                        >
                            Bayar Sekarang
                        </a>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-paper rounded-2xl border border-oat-dark border-dashed p-12 md:p-20 text-center">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-oat flex items-center justify-center text-taupe">
                    <ShoppingBagIcon class="w-10 h-10" />
                </div>
                <h2 class="font-serif text-2xl font-bold text-ink mb-3">Belum Ada Pembelian</h2>
                <p class="text-taupe text-lg mb-8 max-w-md mx-auto">Koleksi digital Anda masih kosong. Mulai jelajahi karya dan produk digital kami.</p>
                <Link
                    href="/products"
                    class="inline-flex items-center justify-center px-8 py-3.5 bg-ink text-paper font-medium rounded-xl hover:bg-terracotta transition-colors shadow-sm text-lg"
                >
                    Jelajahi Produk
                </Link>
            </div>
        </div>
    </UserLayout>
</template>