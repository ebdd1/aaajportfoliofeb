<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    orders: Object,
});

const stats = computed(() => {
    const orders = props.orders?.data || [];
    const pending = orders.filter(o => o.status === 'pending').length;
    const paid = orders.filter(o => o.status === 'paid').length;
    const revenue = orders
        .filter(o => o.status === 'paid')
        .reduce((sum, o) => sum + (o.total_amount || 0), 0);

    return {
        total: props.orders?.total || 0,
        pending,
        paid,
        revenue,
    };
});

const formatPrice = (amount) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        paid: 'bg-green-100 text-green-800',
        expired: 'bg-gray-100 text-gray-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Menunggu',
        paid: 'Lunas',
        expired: 'Kadaluarsa',
        cancelled: 'Dibatalkan',
    };
    return labels[status] || status;
};
</script>

<template>
    <AdminLayout title="Pesanan Customer">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="font-serif text-2xl font-bold text-ink">Pesanan Customer</h1>
                    <p class="text-taupe text-sm mt-1">Kelola pesanan dari customer</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-paper rounded-xl border border-oat-dark p-4">
                    <p class="text-sm text-taupe">Total Pesanan</p>
                    <p class="text-2xl font-bold text-ink mt-1">{{ stats.total }}</p>
                </div>
                <div class="bg-paper rounded-xl border border-oat-dark p-4">
                    <p class="text-sm text-taupe">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ stats.pending }}</p>
                </div>
                <div class="bg-paper rounded-xl border border-oat-dark p-4">
                    <p class="text-sm text-taupe">Lunas</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ stats.paid }}</p>
                </div>
                <div class="bg-paper rounded-xl border border-oat-dark p-4">
                    <p class="text-sm text-taupe">Total Revenue</p>
                    <p class="text-xl font-bold text-terracotta mt-1">{{ formatPrice(stats.revenue) }}</p>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-paper rounded-xl border border-oat-dark overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-oat/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Order ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Metode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-taupe uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-oat">
                            <tr v-if="!orders?.data?.length">
                                <td colspan="7" class="px-4 py-8 text-center text-taupe">
                                    Belum ada pesanan
                                </td>
                            </tr>
                            <tr v-for="order in orders?.data" :key="order.id" class="hover:bg-oat/30 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-sm text-ink">{{ order.order_id }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-ink">{{ order.user?.name || 'N/A' }}</div>
                                    <div class="text-xs text-taupe">{{ order.user?.email || '' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-terracotta">{{ formatPrice(order.total_amount) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-taupe uppercase">{{ order.payment_method || 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusColor(order.status)]">
                                        {{ getStatusLabel(order.status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-taupe">
                                    {{ formatDate(order.created_at) }}
                                </td>
                                <td class="px-4 py-3">
                                    <Link
                                        :href="route('admin.orders.show', order.id)"
                                        class="text-sm text-terracotta hover:text-terracotta-dark font-medium"
                                    >
                                        Detail
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="orders?.links" class="px-4 py-3 border-t border-oat-dark flex items-center justify-between">
                    <div class="text-sm text-taupe">
                        Menampilkan {{ orders?.from || 0 }} - {{ orders?.to || 0 }} dari {{ orders?.total || 0 }}
                    </div>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in orders?.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1 text-sm rounded-lg',
                                link.active ? 'bg-terracotta text-cream' : 'text-taupe hover:bg-oat'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
