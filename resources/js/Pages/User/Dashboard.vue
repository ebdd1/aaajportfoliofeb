<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    ShoppingBagIcon,
    CheckBadgeIcon,
    ClockIcon,
    ArrowRightIcon,
    SparklesIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    user: Object,
    stats: Object,
    recentOrders: Array,
});

const purchasesUrl = computed(() => {
    return props.user?.dashboard_url + '/purchases';
});

const formatPrice = (amount) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
};

const getStatusTheme = (status) => {
    switch (status) {
        case 'paid': return { color: 'text-terracotta', dot: 'bg-terracotta', label: 'Lunas' };
        case 'pending': return { color: 'text-amber-600', dot: 'bg-amber-500', label: 'Menunggu' };
        case 'expired': return { color: 'text-red-600', dot: 'bg-red-500', label: 'Kedaluwarsa' };
        case 'cancelled': return { color: 'text-taupe', dot: 'bg-taupe', label: 'Dibatalkan' };
        default: return { color: 'text-taupe', dot: 'bg-taupe', label: status };
    }
};
</script>

<template>
    <UserLayout title="Dashboard">
        <div>
            <!-- Header -->
            <div class="mb-10">
                <h1 class="font-serif text-3xl md:text-4xl font-bold text-ink mb-3 tracking-tight">
                    Selamat Datang, {{ user?.name || 'User' }}!
                </h1>
                <p class="text-taupe text-lg">Pantau koleksi produk digital dan riwayat pembelian Anda.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                <div class="bg-paper rounded-2xl border border-oat-dark p-6 shadow-sm hover:shadow-elevated transition-shadow duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-oat flex items-center justify-center text-ink shrink-0">
                            <ShoppingBagIcon class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-taupe mb-1">Total Pembelian</p>
                            <p class="text-3xl font-serif font-bold text-ink leading-none">{{ stats?.total_orders || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-paper rounded-2xl border border-oat-dark p-6 shadow-sm hover:shadow-elevated transition-shadow duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-terracotta/10 flex items-center justify-center text-terracotta shrink-0">
                            <CheckBadgeIcon class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-taupe mb-1">Produk Lunas</p>
                            <p class="text-3xl font-serif font-bold text-ink leading-none">{{ stats?.paid_orders || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-paper rounded-2xl border border-oat-dark p-6 shadow-sm hover:shadow-elevated transition-shadow duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                            <ClockIcon class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-taupe mb-1">Menunggu Bayar</p>
                            <p class="text-3xl font-serif font-bold text-ink leading-none">{{ stats?.pending_orders || 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-10">
                <Link
                    href="/products"
                    class="group relative overflow-hidden bg-ink rounded-2xl p-6 sm:p-8 hover:-translate-y-1 transition-transform duration-300 flex items-center justify-between"
                >
                    <div class="relative z-10">
                        <h3 class="font-serif text-xl font-bold text-paper mb-1">Jelajahi Produk</h3>
                        <p class="text-taupe-light text-sm">Lihat koleksi digital terbaru</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-paper/10 flex items-center justify-center text-paper group-hover:bg-paper/20 transition-colors relative z-10">
                        <SparklesIcon class="w-5 h-5" />
                    </div>
                </Link>

                <Link
                    :href="route('user.purchases')"
                    class="group bg-paper rounded-2xl border border-oat-dark p-6 sm:p-8 hover:border-terracotta hover:shadow-elevated transition-all duration-300 flex items-center justify-between"
                >
                    <div>
                        <h3 class="font-serif text-xl font-bold text-ink mb-1">Riwayat Pembelian</h3>
                        <p class="text-taupe text-sm">Akses & unduh produk Anda</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-oat flex items-center justify-center text-ink group-hover:bg-terracotta group-hover:text-paper transition-colors">
                        <ArrowRightIcon class="w-5 h-5" />
                    </div>
                </Link>
            </div>

            <!-- Recent Orders -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-serif text-2xl font-bold text-ink">Aktivitas Terbaru</h2>
                    <Link :href="route('user.purchases')" class="text-sm font-medium text-terracotta hover:text-terracotta-dark inline-flex items-center gap-1 group">
                        Lihat semua
                        <ArrowRightIcon class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </Link>
                </div>

                <div v-if="recentOrders && recentOrders.length > 0" class="bg-paper rounded-2xl border border-oat-dark overflow-hidden">
                    <ul class="divide-y divide-oat-dark">
                        <li v-for="order in recentOrders" :key="order.id" class="p-5 hover:bg-cream/50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-oat flex items-center justify-center text-ink shrink-0 hidden sm:flex">
                                    <ShoppingBagIcon class="w-6 h-6" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-ink truncate text-base mb-0.5">
                                        {{ order.items?.[0]?.product?.name || 'Produk Digital' }}
                                    </h4>
                                    <p class="text-sm text-taupe font-mono">{{ order.short_order_id || order.order_id }}</p>
                                </div>
                                <div class="flex items-center justify-between sm:flex-col sm:items-end gap-2 sm:gap-1">
                                    <p class="font-bold text-ink">{{ formatPrice(order.total_amount) }}</p>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="['w-2 h-2 rounded-full', getStatusTheme(order.status).dot]"></span>
                                        <span :class="['text-sm font-medium', getStatusTheme(order.status).color]">
                                            {{ getStatusTheme(order.status).label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <div v-else class="bg-paper rounded-2xl border border-oat-dark border-dashed p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-oat flex items-center justify-center text-taupe mx-auto mb-4">
                        <ShoppingBagIcon class="w-8 h-8" />
                    </div>
                    <h3 class="font-serif text-lg font-bold text-ink mb-2">Belum ada aktivitas</h3>
                    <p class="text-taupe mb-6 max-w-sm mx-auto">Anda belum melakukan pembelian produk digital apapun.</p>
                    <Link
                        href="/products"
                        class="inline-flex items-center justify-center px-6 py-3 bg-ink text-paper font-medium rounded-xl hover:bg-terracotta transition-colors shadow-sm"
                    >
                        Mulai Belanja
                    </Link>
                </div>
            </div>
        </div>
    </UserLayout>
</template>