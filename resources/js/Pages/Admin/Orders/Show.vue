<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const page = usePage();
const order = computed(() => page.props.order);
const items = computed(() => order.value?.items || []);

const formatPrice = (amount) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
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
        pending: 'Menunggu Pembayaran',
        paid: 'Lunas',
        expired: 'Kadaluarsa',
        cancelled: 'Dibatalkan',
    };
    return labels[status] || status;
};
</script>

<template>
    <AdminLayout title="Detail Pesanan">
        <div class="space-y-6">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm">
                <Link href="/admin/orders" class="text-taupe hover:text-terracotta">Pesanan</Link>
                <span class="text-taupe">/</span>
                <span class="text-ink font-medium">{{ order?.order_id }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Status -->
                    <div class="bg-paper rounded-xl border border-oat-dark p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-semibold text-lg text-ink">Status Pesanan</h2>
                            <span :class="['px-3 py-1 rounded-full text-sm font-medium', getStatusColor(order?.status)]">
                                {{ getStatusLabel(order?.status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-taupe">Order ID</p>
                                <p class="font-mono text-ink">{{ order?.order_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-taupe">Tanggal</p>
                                <p class="text-ink">{{ formatDate(order?.created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-taupe">Metode Pembayaran</p>
                                <p class="text-ink uppercase">{{ order?.payment_method || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-taupe">Batas Pembayaran</p>
                                <p class="text-ink">{{ order?.expired_at ? formatDate(order.expired_at) : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-paper rounded-xl border border-oat-dark p-6">
                        <h2 class="font-semibold text-lg text-ink mb-4">Item Pesanan</h2>

                        <div class="space-y-4">
                            <div v-for="item in items" :key="item.id" class="flex items-center justify-between py-3 border-b border-oat last:border-0">
                                <div>
                                    <p class="font-medium text-ink">{{ item.product?.name || 'Produk Dihapus' }}</p>
                                    <p class="text-sm text-taupe">Qty: 1</p>
                                </div>
                                <p class="font-semibold text-terracotta">{{ formatPrice(item.price_at_purchase) }}</p>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-oat-dark flex justify-between items-center">
                            <span class="font-semibold text-ink">Total</span>
                            <span class="text-xl font-bold text-terracotta">{{ formatPrice(order?.total_amount) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Customer Info -->
                    <div class="bg-paper rounded-xl border border-oat-dark p-6">
                        <h2 class="font-semibold text-ink mb-4">Customer</h2>

                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-taupe">Nama</p>
                                <p class="font-medium text-ink">{{ order?.user?.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-taupe">Email</p>
                                <p class="text-ink">{{ order?.user?.email || 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-paper rounded-xl border border-oat-dark p-6">
                        <h2 class="font-semibold text-ink mb-4">Aksi</h2>

                        <div class="space-y-3">
                            <!-- Mark as Paid -->
                            <form v-if="order?.status === 'pending'" :action="route('admin.orders.mark-paid', order?.id)" method="post" class="block">
                                <input type="hidden" name="_token" :value="page.props.csrf_token" />
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                                    Tandai Lunas
                                </button>
                            </form>

                            <!-- Cancel -->
                            <form v-if="order?.status === 'pending'" :action="route('admin.orders.cancel', order?.id)" method="post" class="block">
                                <input type="hidden" name="_token" :value="page.props.csrf_token" />
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                                    Batalkan
                                </button>
                            </form>

                            <!-- Simulate Payment (for testing) -->
                            <form v-if="order?.status === 'pending'" :action="route('admin.orders.simulate', order?.id)" method="post" class="block">
                                <input type="hidden" name="_token" :value="page.props.csrf_token" />
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                    Simulasi Bayar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div v-if="order?.payment_number" class="bg-paper rounded-xl border border-oat-dark p-6">
                        <h2 class="font-semibold text-ink mb-4">Info Pembayaran</h2>

                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-taupe">Payment Number</p>
                                <p class="font-mono text-sm text-ink break-all">{{ order.payment_number }}</p>
                            </div>
                            <div v-if="order?.paid_at">
                                <p class="text-sm text-taupe">Tanggal Bayar</p>
                                <p class="text-ink">{{ formatDate(order.paid_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
