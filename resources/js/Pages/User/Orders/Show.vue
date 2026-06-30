<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { ArrowLeftIcon, CheckCircleIcon, ClockIcon, XCircleIcon, ExclamationCircleIcon, ArrowDownTrayIcon, ShoppingBagIcon } from '@heroicons/vue/24/outline';
import { CheckCircleIcon as CheckCircleSolid } from '@heroicons/vue/24/solid';
import axios from 'axios';

const props = defineProps({
    order: Object,
    timeline: Array,
});

const formatPrice = (amount) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const currentOrder = ref(props.order);
const currentTimeline = ref(props.timeline);
let pollInterval = null;

const fetchOrderStatus = async () => {
    if (currentOrder.value.status !== 'pending') {
        if (pollInterval) clearInterval(pollInterval);
        return;
    }
    
    try {
        const response = await axios.get(route('api.orders.show', currentOrder.value.order_id));
        
        // Only update if status changed to prevent UI jank
        if (response.data.order.status !== currentOrder.value.status) {
            currentOrder.value = response.data.order;
            currentTimeline.value = response.data.timeline;
            
            // Reload page data formally if it changed to paid
            if (currentOrder.value.status === 'paid') {
                router.reload();
            }
        }
    } catch (error) {
        console.error('Failed to fetch order status', error);
    }
};

onMounted(() => {
    if (currentOrder.value.status === 'pending') {
        // Poll every 5 seconds for pending orders
        pollInterval = setInterval(fetchOrderStatus, 5000);
    }
});

onUnmounted(() => {
    if (pollInterval) {
        clearInterval(pollInterval);
    }
});
</script>

<template>
    <Head :title="`Detail Pesanan ${currentOrder.order_id}`" />

    <UserLayout title="Detail Pesanan">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8 flex items-center gap-4">
                <Link :href="route('ushome.user')" class="p-2 -ml-2 rounded-xl text-taupe hover:text-ink hover:bg-oat transition-colors">
                    <ArrowLeftIcon class="w-6 h-6" />
                </Link>
                <div>
                    <h1 class="font-serif text-2xl md:text-3xl font-bold text-ink tracking-tight flex items-center gap-3">
                        Pesanan <span class="text-terracotta">{{ currentOrder.order_id }}</span>
                    </h1>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Products -->
                    <div class="bg-paper rounded-2xl border border-oat-dark shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-oat-dark bg-cream/30">
                            <h2 class="font-serif text-lg font-bold text-ink">Item Pesanan</h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div v-for="item in currentOrder.items" :key="item.id" class="flex flex-col sm:flex-row gap-4">
                                <div class="w-16 h-16 rounded-xl bg-oat flex items-center justify-center shrink-0 text-ink">
                                    <ShoppingBagIcon class="w-7 h-7" />
                                </div>
                                <div class="flex-1 flex flex-col justify-center">
                                    <h4 class="font-serif font-bold text-ink text-lg truncate mb-1">
                                        {{ item.product?.name || 'Produk Digital' }}
                                    </h4>
                                    <p class="text-sm font-medium text-taupe">{{ formatPrice(item.price_at_purchase || currentOrder.total_amount) }}</p>
                                </div>
                                <div v-if="currentOrder.status === 'paid'" class="flex items-center">
                                    <a :href="route('download.product', { order: currentOrder.id, product: item.product_id })" class="inline-flex items-center gap-2 px-4 py-2 bg-oat text-ink text-sm font-medium rounded-lg hover:bg-terracotta hover:text-paper transition-colors">
                                        <ArrowDownTrayIcon class="w-4 h-4" />
                                        Unduh
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-paper rounded-2xl border border-oat-dark shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-oat-dark bg-cream/30">
                            <h2 class="font-serif text-lg font-bold text-ink">Rincian Pembayaran</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm text-taupe">Total Belanja</span>
                                    <span class="text-sm font-medium text-ink">{{ formatPrice(currentOrder.total_amount) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-t border-oat-dark pt-3">
                                    <span class="text-base font-bold text-ink">Total Tagihan</span>
                                    <span class="text-xl font-bold text-terracotta">{{ formatPrice(currentOrder.total_amount) }}</span>
                                </div>
                            </div>
                            
                            <div v-if="currentOrder.status === 'pending' && currentOrder.payment_number" class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-100 text-center">
                                <p class="text-sm text-amber-900 mb-1">Kode Pembayaran / VA</p>
                                <p class="font-mono text-xl font-bold tracking-wider text-amber-600 mb-3">{{ currentOrder.payment_number }}</p>
                                <p class="text-xs text-amber-700">Segera selesaikan pembayaran sebelum {{ formatDate(currentOrder.expired_at) }}</p>
                                
                                <div class="mt-4 inline-flex items-center gap-2 text-xs text-amber-600">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menunggu verifikasi pembayaran...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Timeline -->
                <div class="lg:col-span-1">
                    <div class="bg-paper rounded-2xl border border-oat-dark shadow-sm overflow-hidden sticky top-24">
                        <div class="px-6 py-4 border-b border-oat-dark bg-cream/30">
                            <h2 class="font-serif text-lg font-bold text-ink">Status Pesanan</h2>
                        </div>
                        <div class="p-6">
                            <div class="relative">
                                <!-- Line connecting dots -->
                                <div class="absolute left-3.5 top-3.5 bottom-3.5 w-0.5 bg-oat-dark -z-10"></div>
                                
                                <div class="space-y-6">
                                    <div v-for="(event, index) in currentTimeline" :key="index" class="flex gap-4">
                                        <!-- Timeline dot -->
                                        <div class="relative flex-shrink-0 mt-1">
                                            <div v-if="event.completed" class="w-7 h-7 rounded-full bg-paper flex items-center justify-center">
                                                <CheckCircleSolid v-if="event.status === 'paid' || event.status === 'completed'" class="w-6 h-6 text-green-500" />
                                                <CheckCircleSolid v-else-if="event.status === 'pending'" class="w-6 h-6 text-terracotta" />
                                                <XCircleIcon v-else-if="event.status === 'cancelled' || event.status === 'expired'" class="w-6 h-6 text-red-500 bg-white rounded-full" />
                                            </div>
                                            <div v-else class="w-7 h-7 rounded-full bg-paper flex items-center justify-center">
                                                <div class="w-3 h-3 rounded-full bg-taupe"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Timeline content -->
                                        <div class="flex-1 pb-1">
                                            <p class="font-medium" :class="event.completed ? 'text-ink' : 'text-taupe'">{{ event.label }}</p>
                                            <p class="text-xs text-taupe mt-0.5">{{ event.description }}</p>
                                            <p v-if="event.date" class="text-xs text-taupe mt-1">{{ formatDate(event.date) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
