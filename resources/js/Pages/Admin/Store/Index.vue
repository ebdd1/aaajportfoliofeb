<script setup>
defineProps({
    stats: Object,
    recentOrders: Array,
    topProducts: Array,
});

const formatPrice = (price) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(price || 0);
};

const formatDate = (date) => {
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(date));
};

const getStatusColor = (status) => {
    switch (status) {
        case 'paid': return 'bg-green-100 text-green-700';
        case 'pending': return 'bg-yellow-100 text-yellow-700';
        case 'cancelled': return 'bg-red-100 text-red-700';
        case 'expired': return 'bg-gray-100 text-gray-700';
        default: return 'bg-gray-100 text-gray-700';
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'paid': return 'Lunas';
        case 'pending': return 'Menunggu';
        case 'cancelled': return 'Dibatalkan';
        case 'expired': return 'Kedaluwarsa';
        default: return status;
    }
};
</script>

<template>
    <div>
        <div class="mb-6">
            <h1 class="font-serif text-2xl font-bold text-ink">Scale Produk</h1>
            <p class="text-sm text-taupe mt-1">Kelola toko digital dan lihat statistik penjualan</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-paper rounded-xl border border-oat-dark p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-terracotta/10 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-taupe">Total Produk</p>
                        <p class="text-xl font-bold text-ink">{{ stats.total_products }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-paper rounded-xl border border-oat-dark p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-taupe">Total Penjualan</p>
                        <p class="text-xl font-bold text-ink">{{ stats.completed_orders }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-paper rounded-xl border border-oat-dark p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-taupe">Menunggu Bayar</p>
                        <p class="text-xl font-bold text-ink">{{ stats.pending_orders }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-paper rounded-xl border border-oat-dark p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-terracotta/10 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-taupe">Total Revenue</p>
                        <p class="text-xl font-bold text-ink">{{ formatPrice(stats.total_revenue) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-paper rounded-2xl border border-oat-dark overflow-hidden">
                <div class="px-5 py-4 border-b border-oat-dark flex items-center justify-between">
                    <h2 class="font-semibold text-ink">Pesanan Terbaru</h2>
                    <Link href="/admin/store/orders" class="text-sm text-terracotta hover:text-terracotta-dark">Lihat semua</Link>
                </div>
                <div class="divide-y divide-oat-dark">
                    <div v-for="order in recentOrders" :key="order.id" class="px-5 py-3 hover:bg-oat/30 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-ink">{{ order.user?.name || 'Guest' }}</p>
                                <p class="text-xs text-taupe">{{ formatDate(order.created_at) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-ink">{{ formatPrice(order.total_amount) }}</p>
                                <span :class="['px-2 py-0.5 text-xs font-medium rounded', getStatusColor(order.status)]">
                                    {{ getStatusLabel(order.status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-if="recentOrders.length === 0" class="px-5 py-8 text-center text-taupe text-sm">
                        Belum ada pesanan
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-paper rounded-2xl border border-oat-dark overflow-hidden">
                <div class="px-5 py-4 border-b border-oat-dark flex items-center justify-between">
                    <h2 class="font-semibold text-ink">Produk Terlaris</h2>
                    <Link href="/admin/products" class="text-sm text-terracotta hover:text-terracotta-dark">Kelola</Link>
                </div>
                <div class="divide-y divide-oat-dark">
                    <div v-for="(product, index) in topProducts" :key="product.id" class="px-5 py-3 hover:bg-oat/30 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-terracotta/10 text-xs font-bold text-terracotta flex items-center justify-center">{{ index + 1 }}</span>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-ink">{{ product.name }}</p>
                                <p class="text-xs text-taupe">{{ product.order_items_count || 0 }} penjualan</p>
                            </div>
                            <span class="text-sm font-semibold text-ink">{{ formatPrice(product.price) }}</span>
                        </div>
                    </div>
                    <div v-if="topProducts.length === 0" class="px-5 py-8 text-center text-taupe text-sm">
                        Belum ada produk
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
