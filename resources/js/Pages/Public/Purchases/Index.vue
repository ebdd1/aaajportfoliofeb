<script setup>
defineProps({
    orders: Array,
});

const formatPrice = (price) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
};

const formatDate = (date) => {
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(date));
};
</script>

<template>
    <div class="min-h-screen bg-cream py-12">
        <div class="container mx-auto px-4 max-w-4xl">
            <h1 class="font-serif text-3xl font-bold text-ink mb-8">Pembelian Saya</h1>

            <div v-if="orders.length === 0" class="bg-paper rounded-2xl border border-oat-dark p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-oat flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-taupe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                </div>
                <h3 class="font-medium text-ink mb-2">Belum ada pembelian</h3>
                <p class="text-sm text-taupe">Mulai beli produk digital pilihan Anda</p>
                <Link href="/products" class="inline-block mt-4 px-6 py-2 bg-terracotta text-cream font-medium rounded-xl">
                    Lihat Produk
                </Link>
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="bg-paper rounded-2xl border border-oat-dark p-6"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-taupe">Order #{{ order.order_id }}</p>
                            <p class="text-xs text-taupe">{{ formatDate(order.paid_at) }}</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-lg">
                            Lunas
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex items-center justify-between"
                        >
                            <div>
                                <p class="font-medium text-ink">{{ item.product?.name }}</p>
                                <p class="text-sm text-taupe">{{ formatPrice(item.price_at_purchase) }}</p>
                            </div>
                            <Link
                                :href="route('download.product', { order: order.id, product: item.product?.id })"
                                class="px-4 py-2 bg-terracotta text-cream font-medium rounded-xl hover:bg-terracotta-dark transition-colors"
                            >
                                Download
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
